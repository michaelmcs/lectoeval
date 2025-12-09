<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::orderBy('apellidos');
        
        // Búsqueda
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombres', 'like', "%{$search}%")
                  ->orWhere('apellidos', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%")
                  ->orWhere('colegio', 'like', "%{$search}%");
            });
        }
        
        $students = $query->get();
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($students);
        }
        
        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dni' => 'required|string|max:12|unique:students,dni',
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'edad' => 'nullable|integer|min:3|max:16',
            'grado' => 'required|in:1,2,3,4,5,6',
            'seccion' => 'nullable|string|max:5',
            'colegio' => 'nullable|string|max:255',
            'apoderado_nombre' => 'nullable|string|max:255',
            'apoderado_telefono' => 'nullable|string|max:20',
            'observaciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $student = Student::create([
                'dni' => $request->dni,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'edad' => $request->edad,
                'grado' => $request->grado,
                'seccion' => $request->seccion,
                'colegio' => $request->colegio,
                'apoderado_nombre' => $request->apoderado_nombre,
                'apoderado_telefono' => $request->apoderado_telefono,
                'observaciones' => $request->observaciones,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estudiante creado exitosamente',
                'student' => $student
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el estudiante: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Student $student)
    {
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validator = Validator::make($request->all(), [
            'dni' => 'required|string|max:12|unique:students,dni,' . $student->id,
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'edad' => 'nullable|integer|min:3|max:16',
            'grado' => 'required|in:1,2,3,4,5,6',
            'seccion' => 'nullable|string|max:5',
            'colegio' => 'nullable|string|max:255',
            'apoderado_nombre' => 'nullable|string|max:255',
            'apoderado_telefono' => 'nullable|string|max:20',
            'observaciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $student->update([
                'dni' => $request->dni,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'edad' => $request->edad,
                'grado' => $request->grado,
                'seccion' => $request->seccion,
                'colegio' => $request->colegio,
                'apoderado_nombre' => $request->apoderado_nombre,
                'apoderado_telefono' => $request->apoderado_telefono,
                'observaciones' => $request->observaciones,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estudiante actualizado exitosamente',
                'student' => $student
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estudiante: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Student $student)
    {
        try {
            $student->delete();

            return response()->json([
                'success' => true,
                'message' => 'Estudiante eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el estudiante: ' . $e->getMessage()
            ], 500);
        }
    }
    public function apiIndex()
    {
        $students = Student::orderBy('apellidos')->get();
        return response()->json($students);
    }
}