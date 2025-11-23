<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'dni','nombres','apellidos','edad','grado','seccion','colegio',
        'apoderado_nombre','apoderado_telefono','observaciones'
    ];

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class);
    }

    public function readings()
    {
        return $this->hasMany(ReadingSession::class);
    }
}