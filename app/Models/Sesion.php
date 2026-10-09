<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sesion extends Model
{
    use HasFactory;

    public const ESTADO_BORRADOR = 'BORRADOR';
    public const ESTADO_FINALIZADO = 'FINALIZADO';

    protected $table = 'sesiones';

    protected $fillable = [
        'fecha_sesion',
        'tipo_sesion',
        'nro_sesion',
        'estado',
        'finalizado_at',
    ];

    protected $casts = [
        'fecha_sesion' => 'date',
        'finalizado_at' => 'datetime',
    ];

    public function estaFinalizada(): bool
    {
        return $this->estado === self::ESTADO_FINALIZADO;
    }

    public function proyectos()
    {
        return $this->belongsToMany(
            ProyectosEnEstudio::class,
            'sesion_proyecto',
            'sesion_id',
            'proyecto_id'
        )->withTimestamps();
    }
}
