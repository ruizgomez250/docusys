<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sesion extends Model
{
    use HasFactory;

    protected $table = 'sesiones';

    protected $fillable = [
        'fecha_sesion',
        'tipo_sesion',
        'nro_sesion',
    ];

    protected $casts = [
        'fecha_sesion' => 'date',
    ];

    public function proyectos()
    {
        return $this->hasMany(ProyectosEnEstudio::class, 'sesion_id');
    }
}
