<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Text;
use App\Models\ReadingSession;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'students' => Student::count(),
            'teachers' => Teacher::count(),
            'texts' => Text::count(),
            'readings' => ReadingSession::count(),
        ];
        
        return view('dashboard', compact('stats'));
    }
}