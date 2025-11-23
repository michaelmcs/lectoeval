<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Para MySQL necesitamos usar SQL directo para modificar el ENUM
        DB::statement("ALTER TABLE texts MODIFY COLUMN ocr_status ENUM('pending', 'ok', 'error', 'processing') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::statement("ALTER TABLE texts MODIFY COLUMN ocr_status ENUM('pending', 'ok', 'error') DEFAULT 'pending'");
    }
};