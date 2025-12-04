<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReadingSession; // Cambia de Reading a ReadingSession

class CompareController extends Controller
{
    public function index()
    {
        return view('compare.index');
    }

    public function process(Request $request)
    {
        // Lógica para procesar comparación
        $request->validate([
            'reading_id' => 'required|exists:reading_sessions,id' // Cambia la tabla
        ]);

        $reading = ReadingSession::findOrFail($request->reading_id);
        
        // Aquí iría la lógica de comparación si no existe
        if (!$reading->precision || !$reading->wer) {
            // Calcular métricas si no existen
            $this->calculateMetrics($reading);
        }

        return redirect()->route('compare.results', ['reading' => $reading->id]);
    }

    public function results(ReadingSession $reading) // Cambia el tipo de parámetro
    {
        // Cargar relaciones necesarias
        $reading->load(['student', 'teacher', 'text']);
        
        return view('compare.results', compact('reading'));
    }

    private function calculateMetrics(ReadingSession $reading) // Cambia el tipo
    {
        // Lógica para calcular precisión y WER
        // Esta es una implementación básica - ajusta según tus necesidades
        
        $originalText = $reading->text->texto_plano ?? '';
        $transcription = $reading->transcripcion ?? '';
        
        if (empty($originalText) || empty($transcription)) {
            return;
        }
        
        // Convertir a arrays de palabras
        $originalWords = preg_split('/\s+/', trim($originalText));
        $transcribedWords = preg_split('/\s+/', trim($transcription));
        
        // Cálculo básico de similitud (debes implementar mejor)
        $matches = 0;
        $minLength = min(count($originalWords), count($transcribedWords));
        
        for ($i = 0; $i < $minLength; $i++) {
            if (strtolower($originalWords[$i]) === strtolower($transcribedWords[$i])) {
                $matches++;
            }
        }
        
        if (count($originalWords) > 0) {
            $precision = ($matches / count($originalWords)) * 100;
            $wer = ((count($originalWords) - $matches) / count($originalWords)) * 100;
            
            $reading->update([
                'precision' => round($precision, 2),
                'wer' => round($wer, 2),
                'status' => 'ready'
            ]);
        }
    }
}