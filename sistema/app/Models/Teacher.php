<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'dni',
        'nombres',
        'apellidos',
        'correo',
        'telefono'
    ];

    // Accesor para nombre completo
    public function getNombreCompletoAttribute()
    {
        return $this->nombres . ' ' . $this->apellidos;
    }

    // Accesor para iniciales (para avatar)
    public function getInicialesAttribute()
    {
        $iniciales = substr($this->nombres, 0, 1) . substr($this->apellidos, 0, 1);
        return strtoupper($iniciales);
    }
}