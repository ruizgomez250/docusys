<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyectosEnEstudio extends Model
{
    use HasFactory;

    protected $table = 'proyectos_en_estudio';

    protected $fillable = [
        'id_mentrada',
        'nro_expediente',
        'anho',
        'camara',
        'presentado_por',
        'fecha_recepcion_texto',
        'membrete',
        'leyenda',
        'cantidad_observaciones',
        'contenido',
    ];

    public function mesaEntrada()
    {
        return $this->belongsTo(MesaEntrada::class, 'id_mentrada');
    }

    public function observaciones()
    {
        return $this->hasMany(ProyectoObservacion::class, 'id_proyecto');
    }
}
