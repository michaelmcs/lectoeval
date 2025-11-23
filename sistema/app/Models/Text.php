<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Text extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo', 'tema', 'descripcion', 'pdf_path', 
        'texto_plano', 'ocr_status', 'palabras_totales',
        'created_by', 'updated_by'
    ];

    public function readings()
    {
        return $this->hasMany(ReadingSession::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}