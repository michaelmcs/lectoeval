<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('text_id')->constrained('texts')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->string('audio_path')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->longText('transcripcion')->nullable();
            $table->decimal('wer', 5, 2)->nullable();
            $table->decimal('precision', 5, 2)->nullable();
            $table->unsignedInteger('velocidad_ppm')->nullable();
            $table->json('resultado_json')->nullable();
            $table->enum('status', ['draft','processing','ready','error'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_sessions');
    }
};