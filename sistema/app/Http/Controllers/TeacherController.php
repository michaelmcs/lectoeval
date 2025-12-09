<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::latest();
        
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombres', 'like', "%{$search}%")
                  ->orWhere('apellidos', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%")
                  ->orWhere('correo', 'like', "%{$search}%");
            });
        }
        
        $teachers = $query->get();
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($teachers);
        }
        
        return view('teachers.index', compact('teachers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dni' => 'required|string|max:12|unique:teachers,dni',
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'correo' => 'nullable|email|unique:teachers,correo',
            'telefono' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $teacher = Teacher::create([
                'dni' => $request->dni,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'correo' => $request->correo,
                'telefono' => $request->telefono,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Docente creado exitosamente',
                'teacher' => $teacher
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el docente: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Teacher $teacher)
    {
        return response()->json($teacher);
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validator = Validator::make($request->all(), [
            'dni' => 'required|string|max:12|unique:teachers,dni,' . $teacher->id,
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'correo' => 'nullable|email|unique:teachers,correo,' . $teacher->id,
            'telefono' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $teacher->update([
                'dni' => $request->dni,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'correo' => $request->correo,
                'telefono' => $request->telefono,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Docente actualizado exitosamente',
                'teacher' => $teacher
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el docente: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Teacher $teacher)
    {
        try {
            $teacher->delete();

            return response()->json([
                'success' => true,
                'message' => 'Docente eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el docente: ' . $e->getMessage()
            ], 500);
        }
    }


    public function apiIndex()
    {
        $teachers = Teacher::latest()->get();
        return response()->json($teachers);
    }
}