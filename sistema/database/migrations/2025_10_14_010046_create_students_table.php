<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('dni', 12)->unique();
            $table->string('nombres');
            $table->string('apellidos');
            $table->unsignedTinyInteger('edad')->nullable();
            $table->enum('grado', ['1','2','3','4','5','6']);
            $table->string('seccion', 5)->nullable();
            $table->string('colegio')->nullable();
            $table->string('apoderado_nombre')->nullable();
            $table->string('apoderado_telefono', 20)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};