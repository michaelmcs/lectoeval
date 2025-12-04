<?php

namespace App\Http\Controllers;

use App\Models\ReadingSession;
use App\Models\Student;
use App\Models\Text;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReadingController extends Controller
{
    public function index()
    {
        // Solo carga la vista principal; todo lo demás es por AJAX (API interna)
        return view('readings.index');
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'student_id' => 'required|exists:students,id',
                'text_id'    => 'required|exists:texts,id',
                'teacher_id' => 'nullable|exists:teachers,id',
            ]);

            $reading = ReadingSession::create([
                'student_id'   => $data['student_id'],
                'text_id'      => $data['text_id'],
                'teacher_id'   => $data['teacher_id'] ?? null,
                'status'       => 'draft',
                'audio_path'   => null,
                'duration_ms'  => null,
                'transcripcion'    => null,
                'wer'              => null,
                'precision'        => null,
                'velocidad_ppm'    => null,
                'resultado_json'   => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sesión de lectura creada correctamente',
                'reading' => $reading->load(['student', 'text', 'teacher']),
            ]);

        } catch (\Exception $e) {
            $msg = 'Error al crear la sesión: ' . $e->getMessage();
            $msg = $this->sanitizeUtf8($msg);

            return response()->json([
                'success' => false,
                'message' => $msg,
            ], 500);
        }
    }

    public function update(Request $request, ReadingSession $reading)
    {
        try {
            $data = $request->validate([
                'student_id' => 'required|exists:students,id',
                'text_id'    => 'required|exists:texts,id',
                'teacher_id' => 'nullable|exists:teachers,id',
            ]);

            $reading->update([
                'student_id' => $data['student_id'],
                'text_id'    => $data['text_id'],
                'teacher_id' => $data['teacher_id'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sesión de lectura actualizada correctamente',
                'reading' => $reading->load(['student', 'text', 'teacher']),
            ]);

        } catch (\Exception $e) {
            $msg = 'Error al actualizar la sesión: ' . $e->getMessage();
            $msg = $this->sanitizeUtf8($msg);

            return response()->json([
                'success' => false,
                'message' => $msg,
            ], 500);
        }
    }

    public function destroy(ReadingSession $reading)
    {
        try {
            if ($reading->audio_path && Storage::disk('public')->exists($reading->audio_path)) {
                Storage::disk('public')->delete($reading->audio_path);
            }

            $reading->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sesión de lectura eliminada correctamente',
            ]);

        } catch (\Exception $e) {
            $msg = 'Error al eliminar la sesión: ' . $e->getMessage();
            $msg = $this->sanitizeUtf8($msg);

            return response()->json([
                'success' => false,
                'message' => $msg,
            ], 500);
        }
    }

    public function uploadAudio(Request $request, ReadingSession $reading)
    {
        \Log::info('=== INICIANDO UPLOAD DE AUDIO ===');
        \Log::info('Reading ID: ' . $reading->id);

        try {
            if (!$request->hasFile('audio')) {
                \Log::error('No se recibió archivo de audio');
                return response()->json([
                    'success' => false,
                    'message' => 'No se recibió ningún archivo de audio',
                ], 400);
            }

            $file = $request->file('audio');

            \Log::info('Archivo recibido:', [
                'name'        => $file->getClientOriginalName(),
                'size'        => $file->getSize(),
                'mime'        => $file->getMimeType(),
                'client_mime' => $file->getClientMimeType(),
                'extension'   => $file->getClientOriginalExtension(),
            ]);

            $request->validate([
                'audio'       => 'required|file|max:15360', // 15 MB
                'duration_ms' => 'required|integer|min:1',
            ]);

            $directory = 'readings';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            $fileName = 'reading_' . $reading->id . '_' . time() . '.webm';
            $path = $file->storeAs($directory, $fileName, 'public');

            $reading->update([
                'audio_path'   => $path,              // ruta relativa en storage/app/public
                'duration_ms'  => $request->duration_ms,
                'status'       => 'processing',
                'transcripcion'    => null,
                'wer'              => null,
                'precision'        => null,
                'velocidad_ppm'    => null,
                'resultado_json'   => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Audio subido correctamente. Ahora puedes ejecutar el análisis ASR.',
                'path'    => $path,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en uploadAudio: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            $msg = 'Error al subir el audio: ' . $e->getMessage();
            $msg = $this->sanitizeUtf8($msg);

            return response()->json([
                'success' => false,
                'message' => $msg,
            ], 500);
        }
    }

    public function runASR(Request $request, ReadingSession $reading)
    {
        \Log::info('=== INICIANDO ANÁLISIS ASR ===');
        \Log::info('Reading ID: ' . $reading->id);
        \Log::info('Audio path (relativo): ' . $reading->audio_path);

        try {
            if (!$reading->audio_path) {
                $msg = 'No hay audio para procesar';
                \Log::error($msg);

                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 400);
                }
                return back()->with('error', $msg);
            }

            $audioPath = storage_path('app/public/' . $reading->audio_path);
            \Log::info('Ruta completa del audio: ' . $audioPath);

            if (!file_exists($audioPath)) {
                $msg = 'El archivo de audio no existe';
                \Log::error($msg . ' en: ' . $audioPath);

                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 400);
                }
                return back()->with('error', $msg);
            }

            \Log::info('Archivo de audio encontrado, tamaño: ' . filesize($audioPath) . ' bytes');

            // Convertir a WAV 16k mono
            \Log::info('Convirtiendo audio a WAV...');
            $wavOut = $this->convertToWav($audioPath);
            \Log::info('Audio convertido a: ' . $wavOut);

            // Obtener texto objetivo desde el modelo Text
            $targetText = $reading->text->texto_plano ?? '';
            \Log::info('Longitud del texto objetivo: ' . mb_strlen($targetText));

            // Ejecutar análisis ASR (llamando al script Python)
            \Log::info('Ejecutando análisis ASR...');
            $result = $this->executeASR($wavOut, $targetText);

            if ($result) {
                \Log::info('Análisis ASR completado exitosamente');
                \Log::info('Resultados - Precisión: ' . $result['precision']);
                \Log::info('Resultados - WER: ' . $result['wer']);
                \Log::info('Resultados - PPM: ' . $result['ppm']);

                $reading->update([
                    'transcripcion'  => $result['transcription'] ?? null,
                    'wer'            => $result['wer'] ?? null,
                    'precision'      => $result['precision'] ?? null,
                    'velocidad_ppm'  => $result['ppm'] ?? null,
                    'resultado_json' => isset($result['diffs'])
                        ? json_encode($result['diffs'], JSON_UNESCAPED_UNICODE)
                        : null,
                    'status'         => 'ready',
                ]);

                if (file_exists($wavOut)) {
                    unlink($wavOut);
                }

                $payload = [
                    'success' => true,
                    'message' => 'Análisis ASR completado correctamente',
                    'reading' => $reading->load(['student', 'text', 'teacher']),
                ];

                if ($request->wantsJson()) {
                    return response()->json($payload);
                }

                return back()->with('success', $payload['message']);
            } else {
                throw new \Exception('No se pudo procesar el audio - resultado vacío');
            }

        } catch (\Exception $e) {
            \Log::error('ERROR en runASR: ' . $e->getMessage());
            \Log::error('ERROR Stack trace: ' . $e->getTraceAsString());

            $reading->update(['status' => 'error']);

            $msg = 'Error en el análisis ASR: ' . $e->getMessage();
            $msg = $this->sanitizeUtf8($msg);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 500);
            }

            return back()->with('error', $msg);
        }
    }

    private function convertToWav(string $audioPath): string
    {
        $wavOut = storage_path('app/tmp/' . uniqid('reading_') . '.wav');

        if (!file_exists(dirname($wavOut))) {
            mkdir(dirname($wavOut), 0755, true);
        }

        $command = "ffmpeg -y -i \"{$audioPath}\" -ac 1 -ar 16000 \"{$wavOut}\" 2>&1";
        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($wavOut)) {
            \Log::error('Error al convertir a WAV. Return code: ' . $returnCode);
            \Log::error('FFmpeg output: ' . implode("\n", $output));
            throw new \Exception('Error al convertir el audio a formato WAV');
        }

        return $wavOut;
    }

    private function executeASR(string $audioPath, string $targetText): array
    {
        $python      = env('TRAINING_PYTHON', 'python');
        $trainingDir = env('TRAINING_DIR', '../entrenamiento');

        $script = base_path($trainingDir) . DIRECTORY_SEPARATOR . 'asr_whisper.py';

        if (!file_exists($script)) {
            throw new \Exception('No se encontró el script de ASR en: ' . $script);
        }

        // Archivo temporal con el texto objetivo
        $tmpTarget = storage_path('app/tmp/' . uniqid('target_') . '.txt');
        if (!file_exists(dirname($tmpTarget))) {
            mkdir(dirname($tmpTarget), 0755, true);
        }
        file_put_contents($tmpTarget, $targetText);



            // Forzar modo UTF-8 en Python (-X utf8)
$command = escapeshellcmd($python) . ' -X utf8 '
    . escapeshellarg($script)
    . ' --audio ' . escapeshellarg($audioPath)
    . ' --target ' . escapeshellarg($tmpTarget)
    . ' 2>&1';


        \Log::info('Comando ASR: ' . $command);

        $output     = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if (file_exists($tmpTarget)) {
            unlink($tmpTarget);
        }

        if ($returnCode !== 0) {
            \Log::error('ASR return code: ' . $returnCode);
            \Log::error('ASR output: ' . implode("\n", $output));
            throw new \Exception('Error en el script de ASR: ' . $this->sanitizeUtf8(implode(', ', $output)));
        }

        if (empty($output)) {
            throw new \Exception('El script de ASR no devolvió salida');
        }

        // Buscar la última línea que parezca JSON puro
        $jsonLine = null;
        for ($i = count($output) - 1; $i >= 0; $i--) {
            $line = trim($output[$i]);
            if ($line === '') {
                continue;
            }
            if ($line[0] === '{' && substr($line, -1) === '}') {
                $jsonLine = $line;
                break;
            }
        }

        if (!$jsonLine) {
            \Log::error('No se encontró JSON limpio en la salida del ASR: ' . implode("\n", $output));
            throw new \Exception('No se pudo parsear la salida del ASR como JSON');
        }

        $jsonLine = $this->sanitizeUtf8($jsonLine);

        $json = json_decode($jsonLine, true);

        if (!$json) {
            \Log::error('Error al decodificar JSON: ' . json_last_error_msg());
            \Log::error('JSON recibido: ' . $jsonLine);
            throw new \Exception('No se pudo parsear la salida del ASR como JSON');
        }

        return $json;
    }

    private function sanitizeUtf8($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'sanitizeUtf8'], $value);
        }

        if (is_string($value)) {
            return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }

        return $value;
    }

    /** API: listar todas las sesiones */


    public function apiIndex()
{
    // Solo mostrar lecturas con análisis completado (status = 'ready')
    $readings = ReadingSession::with(['student', 'text', 'teacher'])
        ->where('status', 'ready')
        ->latest()
        ->get();

    return response()->json($this->sanitizeUtf8($readings->toArray()));
}

    

    /** API: selects */
    public function getStudents()
    {
        try {
            $students = Student::select('id', 'nombres', 'apellidos', 'grado')
                ->orderBy('apellidos')
                ->orderBy('nombres')
                ->get();

            return response()->json($this->sanitizeUtf8($students->toArray()));
        } catch (\Exception $e) {
            $msg = 'Error al cargar estudiantes: ' . $e->getMessage();
            $msg = $this->sanitizeUtf8($msg);

            return response()->json([
                'error' => $msg,
            ], 500);
        }
    }




    public function getTeachers()
    {
        try {
            $teachers = Teacher::select('id', 'nombres', 'apellidos')
                ->orderBy('apellidos')
                ->orderBy('nombres')
                ->get();

            return response()->json($this->sanitizeUtf8($teachers->toArray()));
        } catch (\Exception $e) {
            $msg = 'Error al cargar docentes: ' . $e->getMessage();
            $msg = $this->sanitizeUtf8($msg);

            return response()->json([
                'error' => $msg,
            ], 500);
        }
    }

    public function getTexts()
    {
        try {
            $texts = Text::select('id', 'titulo', 'tema', 'palabras_totales', 'texto_plano')
                ->orderBy('titulo')
                ->get();

            return response()->json($this->sanitizeUtf8($texts->toArray()));
        } catch (\Exception $e) {
            $msg = 'Error al cargar textos: ' . $e->getMessage();
            $msg = $this->sanitizeUtf8($msg);

            return response()->json([
                'error' => $msg,
            ], 500);
        }
    }

    /** API: estadísticas rápidas */
    public function statistics()
    {
        try {
            $total      = ReadingSession::count();
            $completed  = ReadingSession::where('status', 'ready')->count();
            $processing = ReadingSession::where('status', 'processing')->count();
            $errors     = ReadingSession::where('status', 'error')->count();
            $drafts     = ReadingSession::where('status', 'draft')->count();

            return response()->json(compact('total', 'completed', 'processing', 'errors', 'drafts'));
        } catch (\Exception $e) {
            $msg = 'Error al cargar estadísticas: ' . $e->getMessage();
            $msg = $this->sanitizeUtf8($msg);

            return response()->json([
                'error' => $msg,
            ], 500);
        }
    }

    /** Rutas legacy: redirigir a index sin romper enlaces viejos */
    public function create()
    {
        return redirect()->route('readings.index');
    }

    public function show(ReadingSession $reading)
    {
        return redirect()->route('readings.index');
    }

    public function edit(ReadingSession $reading)
    {
        return redirect()->route('readings.index');
    }
}
