<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroAsistencia extends Model
{
    use HasFactory;
    protected $table = 'registros_asistencia';

    protected $fillable = ['legislador_id', 'sesion_id', 'fecha_sesion', 'estado', 'justificacion'];

    public function legislador()
    {
        return $this->belongsTo(Legislador::class, 'legislador_id');
    }
}
