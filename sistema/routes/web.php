<?php

use App\Http\Controllers\{
    DashboardController, StudentController, TeacherController, TextController,
    AssignmentController, ReadingController, CompareController, ProfileController
};
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Redirige a la página de login si no está autenticado
Route::get('/', function () {
    return redirect('/login');
});

// Rutas de autenticación
require __DIR__.'/auth.php';

// Rutas protegidas para usuarios autenticados
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/admin', [DashboardController::class, 'index'])->name('dashboard');

    // Rutas para estudiantes
    Route::resource('/admin/students', StudentController::class);

    // Rutas para docentes
    Route::resource('/admin/teachers', TeacherController::class);

    // Rutas para asignaciones
    Route::resource('/admin/assignments', AssignmentController::class);

    // RUTAS PARA TEXTOS
    Route::get('/admin/texts', [TextController::class, 'index'])->name('texts.index');
    Route::get('/admin/texts/api', [TextController::class, 'apiIndex'])->name('texts.api.index');
    Route::get('/admin/texts/create', [TextController::class, 'create'])->name('texts.create');
    Route::post('/admin/texts', [TextController::class, 'store'])->name('texts.store');
    Route::get('/admin/texts/{text}', [TextController::class, 'show'])->name('texts.show');
    Route::get('/admin/texts/{text}/edit', [TextController::class, 'edit'])->name('texts.edit');
    Route::put('/admin/texts/{text}', [TextController::class, 'update'])->name('texts.update');
    Route::delete('/admin/texts/{text}', [TextController::class, 'destroy'])->name('texts.destroy');
    Route::post('/admin/texts/{text}/upload-pdf', [TextController::class, 'uploadPdf'])->name('texts.uploadPdf');
    Route::post('/admin/texts/process-pdf', [TextController::class, 'processPdf'])->name('texts.process-pdf');

    // RUTAS PARA LECTURAS - SOLO LAS NECESARIAS
    Route::get('/admin/readings', [ReadingController::class, 'index'])->name('readings.index');
    Route::post('/admin/readings', [ReadingController::class, 'store'])->name('readings.store');
    Route::put('/admin/readings/{reading}', [ReadingController::class, 'update'])->name('readings.update');
    Route::delete('/admin/readings/{reading}', [ReadingController::class, 'destroy'])->name('readings.destroy');
    
    // Rutas adicionales para readings
    Route::post('/admin/readings/{reading}/upload-audio', [ReadingController::class, 'uploadAudio'])->name('readings.uploadAudio');
    Route::post('/admin/readings/{reading}/asr', [ReadingController::class, 'runASR'])->name('readings.runASR');
    Route::get('/admin/readings/api', [ReadingController::class, 'apiIndex'])->name('readings.api.index');

    // Redirecciones para rutas que ya no se usan
    Route::get('/admin/readings/create', [ReadingController::class, 'create'])->name('readings.create');
    Route::get('/admin/readings/{reading}', [ReadingController::class, 'show'])->name('readings.show');
    Route::get('/admin/readings/{reading}/edit', [ReadingController::class, 'edit'])->name('readings.edit');

    // Comparador
    Route::get('/admin/compare', [CompareController::class, 'index'])->name('compare.index');
    Route::post('/admin/compare/process', [CompareController::class, 'process'])->name('compare.process');
    Route::get('/admin/compare/results/{result}', [CompareController::class, 'results'])->name('compare.results');

    // Cerrar sesión
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Perfil del usuario
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::put('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

    // API interna para AJAX
    Route::prefix('api')->group(function () {
        Route::get('/texts/search', [TextController::class, 'search'])->name('api.texts.search');
        Route::get('/students/search', [StudentController::class, 'search'])->name('api.students.search');
        Route::get('/students', [StudentController::class, 'apiIndex'])->name('students.api.index');
        Route::get('/teachers/search', [TeacherController::class, 'search'])->name('api.teachers.search');
        Route::get('/teachers', [TeacherController::class, 'apiIndex'])->name('teachers.api.index');
        Route::get('/readings/statistics', [ReadingController::class, 'statistics'])->name('api.readings.statistics');
        Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('api.dashboard.stats');
        
        // Nuevas rutas para readings
        Route::get('/students-for-readings', [ReadingController::class, 'getStudents'])->name('api.readings.students');
        Route::get('/teachers-for-readings', [ReadingController::class, 'getTeachers'])->name('api.readings.teachers');
        Route::get('/texts-for-readings', [ReadingController::class, 'getTexts'])->name('api.readings.texts');
    });
});

// Ruta de salud del sistema
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'environment' => app()->environment()
    ]);
});

// Ruta de fallback para manejar 404
Route::fallback(function () {
    if (request()->is('api/*')) {
        return response()->json([
            'message' => 'Endpoint no encontrado',
            'status' => 404
        ], 404);
    }
    
    return response()->view('errors.404', [], 404);
});