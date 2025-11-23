<?php

namespace App\Http\Controllers;

use App\Models\Text;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class TextController extends Controller
{
    public function index()
    {
        if (request()->ajax() || request()->wantsJson() || request()->get('format') === 'json') {
            $texts = Text::latest()->get();
            return response()->json($texts);
        }
        
        $texts = Text::latest()->paginate(15);
        return view('texts.index', compact('texts'));
    }

    public function apiIndex()
    {
        $texts = Text::latest()->get();
        return response()->json($texts);
    }

    public function create()
    {
        return view('texts.create');
    }

    public function store(Request $request)
    {
        $this->ensureDirectoriesExist();

        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'tema' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'texto_plano' => 'nullable|string',
            'pdf' => 'nullable|file|mimes:pdf|max:10240'
        ]);

        try {
            $palabrasTotales = 0;
            $ocrStatus = 'pending';
            
            if (!empty($data['texto_plano'])) {
                $palabrasTotales = str_word_count($data['texto_plano']);
                $ocrStatus = 'ok';
            }

            $text = Text::create([
                'titulo' => $data['titulo'],
                'tema' => $data['tema'] ?? null,
                'descripcion' => $data['descripcion'] ?? null,
                'texto_plano' => $data['texto_plano'] ?? null,
                'palabras_totales' => $palabrasTotales,
                'ocr_status' => $ocrStatus,
            ]);

            // Si hay PDF, guardarlo
            if ($request->hasFile('pdf')) {
                $file = $request->file('pdf');
                $path = $file->store('texts', 'public');
                
                $text->update([
                    'pdf_path' => $path,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Texto guardado correctamente',
                'text' => $text
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el texto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Text $text)
    {
        return view('texts.show', compact('text'));
    }

    public function edit(Text $text)
    {
        return view('texts.edit', compact('text'));
    }

    public function update(Request $request, Text $text)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'tema' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'texto_plano' => 'nullable|string',
        ]);

        try {
            $palabrasTotales = 0;
            $ocrStatus = $text->ocr_status;
            
            if (!empty($data['texto_plano'])) {
                $palabrasTotales = str_word_count($data['texto_plano']);
                $ocrStatus = 'ok';
            }

            $text->update([
                'titulo' => $data['titulo'],
                'tema' => $data['tema'] ?? null,
                'descripcion' => $data['descripcion'] ?? null,
                'texto_plano' => $data['texto_plano'] ?? null,
                'palabras_totales' => $palabrasTotales,
                'ocr_status' => $ocrStatus,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Texto actualizado correctamente',
                'text' => $text->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el texto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Text $text)
    {
        try {
            if ($text->pdf_path) {
                Storage::disk('public')->delete($text->pdf_path);
            }
            
            $text->delete();

            return response()->json([
                'success' => true,
                'message' => 'Texto eliminado correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el texto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function processPdf(Request $request)
    {
        try {
            if (!$request->hasFile('pdf')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se recibió archivo PDF'
                ], 400);
            }

            $file = $request->file('pdf');
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            
            // EXTRAER TEXTO REAL DEL PDF
            $pdfText = $this->extractTextFromPdf($file);
            
            if (empty(trim($pdfText))) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo extraer texto del PDF. El archivo puede ser una imagen, estar protegido o no contener texto extraíble.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'text' => $pdfText,
                'word_count' => str_word_count($pdfText),
                'status' => 'success',
                'file_info' => [
                    'name' => $fileName,
                    'size_kb' => round($fileSize / 1024, 2)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error procesando PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    private function extractTextFromPdf($pdfFile)
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($pdfFile->getPathname());
            $text = $pdf->getText();
            
            // Limpiar y formatear el texto
            $text = preg_replace('/\s+/', ' ', $text); // Eliminar espacios múltiples
            $text = trim($text);
            
            // Si el texto está vacío, podría ser un PDF escaneado (imagen)
            if (empty($text)) {
                throw new \Exception('El PDF parece ser una imagen escaneada. Se requiere OCR avanzado para extraer texto de imágenes.');
            }
            
            return $text;
            
        } catch (\Exception $e) {
            // Si falla la librería, intentar con una alternativa
            return $this->extractTextWithFallback($pdfFile, $e);
        }
    }

    private function extractTextWithFallback($pdfFile, $originalError)
    {
        try {
            // Intentar con shell command si pdftotext está disponible
            if ($this->isPdftotextAvailable()) {
                $tempPath = $pdfFile->getPathname();
                $outputPath = tempnam(sys_get_temp_dir(), 'pdf_text');
                
                shell_exec("pdftotext \"{$tempPath}\" \"{$outputPath}\"");
                
                if (file_exists($outputPath)) {
                    $text = file_get_contents($outputPath);
                    unlink($outputPath);
                    
                    if (!empty(trim($text))) {
                        return trim($text);
                    }
                }
            }
            
            // Si todo falla, lanzar el error original
            throw new \Exception($originalError->getMessage());
            
        } catch (\Exception $e) {
            throw new \Exception('No se pudo extraer texto del PDF: ' . $originalError->getMessage());
        }
    }

    private function isPdftotextAvailable()
    {
        $output = shell_exec('which pdftotext');
        return !empty($output);
    }

    /**
     * Asegura que los directorios necesarios existan
     */
    private function ensureDirectoriesExist()
    {
        $directories = [
            storage_path('app/public/texts'),
            storage_path('app/public/temp')
        ];

        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }
}