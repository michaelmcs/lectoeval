import argparse
import json
import re
import sys
import time
import contextlib
import wave

import whisper


# Forzar stdout a UTF-8 (evita caracteres raros tipo � en Windows)
if hasattr(sys.stdout, "reconfigure"):
    sys.stdout.reconfigure(encoding="utf-8")


def load_text(path: str) -> str:
    """Lee el texto objetivo en UTF-8."""
    with open(path, "r", encoding="utf-8", errors="replace") as f:
        return f.read().strip()


def normalize_text(text: str) -> str:
    """
    Normaliza texto para comparación:
    - Minúsculas
    - Quita signos de puntuación básicos
    - Colapsa espacios
    """
    text = text.lower()
    # Reemplazar comillas tipográficas por simples
    text = text.replace("“", '"').replace("”", '"').replace("’", "'")
    # Quitar signos de puntuación comunes
    text = re.sub(r"[.,;:!?\"“”'’()\[\]{}¡¿\-–—]", " ", text)
    # Colapsar espacios
    text = re.sub(r"\s+", " ", text).strip()
    return text


def tokenize(text: str):
    """Convierte texto normalizado en lista de palabras."""
    if not text:
        return []
    return text.split(" ")


def levenshtein_alignment(ref_words, hyp_words):
    """
    Calcula distancia de edición y alineación simple para WER:
    - ref_words: lista de palabras objetivo
    - hyp_words: lista de palabras dichas
    Retorna:
      (wer, substitutions, deletions, insertions, diffs)
    """
    n = len(ref_words)
    m = len(hyp_words)

    # DP matrix
    dp = [[0] * (m + 1) for _ in range(n + 1)]
    op = [[None] * (m + 1) for _ in range(n + 1)]

    # Inicialización
    for i in range(1, n + 1):
        dp[i][0] = i
        op[i][0] = "D"  # deletion
    for j in range(1, m + 1):
        dp[0][j] = j
        op[0][j] = "I"  # insertion

    # Rellenar DP
    for i in range(1, n + 1):
        for j in range(1, m + 1):
            if ref_words[i - 1] == hyp_words[j - 1]:
                dp[i][j] = dp[i - 1][j - 1]
                op[i][j] = "E"  # equal
            else:
                # sustitución
                sub_cost = dp[i - 1][j - 1] + 1
                # inserción
                ins_cost = dp[i][j - 1] + 1
                # eliminación
                del_cost = dp[i - 1][j] + 1

                best = min(sub_cost, ins_cost, del_cost)
                dp[i][j] = best

                if best == sub_cost:
                    op[i][j] = "S"
                elif best == ins_cost:
                    op[i][j] = "I"
                else:
                    op[i][j] = "D"

    # Backtracking para obtener diffs
    i, j = n, m
    substitutions = deletions = insertions = 0
    diffs = []

    while i > 0 or j > 0:
        operation = op[i][j] if i >= 0 and j >= 0 else None

        if operation == "E":
            # palabra correcta, no la añadimos a diffs
            i -= 1
            j -= 1

        elif operation == "S":
            substitutions += 1
            diffs.append({
                "said": hyp_words[j - 1],
                "target": ref_words[i - 1],
                "pos": i - 1
            })
            i -= 1
            j -= 1

        elif operation == "I":
            insertions += 1
            diffs.append({
                "said": hyp_words[j - 1],
                "target": None,
                "pos": i  # inserta antes de la palabra i
            })
            j -= 1

        elif operation == "D":
            deletions += 1
            diffs.append({
                "said": None,
                "target": ref_words[i - 1],
                "pos": i - 1
            })
            i -= 1

        else:
            # Seguridad / borde raro
            break

    # WER = (S + D + I) / N_ref
    if n == 0:
        wer = 0.0 if m == 0 else 100.0
    else:
        wer = (substitutions + deletions + insertions) / n * 100.0

    # Agregar resumen de omisiones (deletions) al final estilo tu JSON previo
    if deletions > 0:
        diffs.append({"omissions": deletions})

    return wer, substitutions, deletions, insertions, list(reversed(diffs))


def estimate_audio_duration_seconds(audio_path: str) -> float:
    """Obtiene la duración aproximada del audio WAV usando wave."""
    try:
        with contextlib.closing(wave.open(audio_path, "r")) as f:
            frames = f.getnframes()
            rate = f.getframerate()
            if rate == 0:
                return 0.0
            return frames / float(rate)
    except Exception:
        # Si falla, devolvemos 0 y luego usamos elapsed_s
        return 0.0


def main():
    parser = argparse.ArgumentParser(
        description="ASR con Whisper + comparación contra texto objetivo"
    )
    parser.add_argument(
        "--audio",
        required=True,
        help="Ruta al archivo de audio (WAV 16k mono recomendado)",
    )
    parser.add_argument(
        "--target",
        required=True,
        help="Ruta al archivo de texto con el contenido objetivo (UTF-8)",
    )

    args = parser.parse_args()

    audio_path = args.audio
    target_path = args.target

    t0 = time.time()

    # 1. Cargar modelo Whisper (puedes cambiar 'small' por 'base'/'medium' si quieres)
    model = whisper.load_model("small")

    # 2. Transcribir audio
    result = model.transcribe(
        audio_path,
        language="es",  # puedes omitir para auto-detectar
    )

    transcription = result.get("text", "").strip()

    # 3. Leer texto objetivo
    target_text = load_text(target_path)

    # 4. Normalizar y tokenizar
    norm_ref = normalize_text(target_text)
    norm_hyp = normalize_text(transcription)

    ref_words = tokenize(norm_ref)
    hyp_words = tokenize(norm_hyp)

    wer, subs, dels, ins, diffs = levenshtein_alignment(ref_words, hyp_words)

    # 5. Calcular precisión (complemento de WER)
    precision = max(0.0, 100.0 - wer)

    # 6. Duración del audio (segundos) para PPM
    audio_duration = estimate_audio_duration_seconds(audio_path)
    elapsed_s = time.time() - t0  # tiempo total de procesamiento

    if audio_duration <= 0:
        # Si no pudimos obtener duración, usamos elapsed_s como aproximado
        audio_duration = elapsed_s if elapsed_s > 0 else 1.0

    num_words_hyp = len(hyp_words)
    ppm = 0
    if audio_duration > 0 and num_words_hyp > 0:
        ppm = round(num_words_hyp / audio_duration * 60)

    # 7. Armar respuesta final
    output = {
        "transcription": transcription,
        "wer": round(wer, 2),
        "precision": round(precision, 2),
        "ppm": ppm,
        "elapsed_s": round(elapsed_s, 2),
        "diffs": diffs,
    }

    # 8. Imprimir SOLO JSON en UTF-8, sin escapar acentos
    print(json.dumps(output, ensure_ascii=False))


if __name__ == "__main__":
    main()
