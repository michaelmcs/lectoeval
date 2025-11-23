<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id','text_id','teacher_id','audio_path','duration_ms',
        'transcripcion','wer','precision','velocidad_ppm','resultado_json','status'
    ];

    protected $casts = ['resultado_json'=>'array'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function text()
    {
        return $this->belongsTo(Text::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}