<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('students')->get();
        $students = Student::all();
        return view('assignments.index', compact('teachers', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'student_id' => 'required|exists:students,id',
        ]);

        $teacher = Teacher::find($request->teacher_id);
        $teacher->students()->syncWithoutDetaching($request->student_id);

        return back()->with('success', 'Estudiante asignado correctamente.');
    }
}