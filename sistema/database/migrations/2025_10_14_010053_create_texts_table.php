<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('texts', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('tema')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('pdf_path')->nullable();
            $table->longText('texto_plano')->nullable();
            $table->enum('ocr_status', ['pending','ok','error'])->default('pending');
            $table->unsignedInteger('palabras_totales')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('texts');
    }
};