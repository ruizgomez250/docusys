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
        'sesion_id',
        'usar_documento_padre',
        'documento_padre_id',
        'nro_expediente',
        'anho',
        'camara',
        'acapite',
        'destino',
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

    public function sesion()
    {
        return $this->belongsTo(Sesion::class, 'sesion_id');
    }

    public function documentoPadre()
    {
        return $this->belongsTo(ProyectosEnEstudio::class, 'documento_padre_id');
    }

    public function observaciones()
    {
        return $this->hasMany(ProyectoObservacion::class, 'id_proyecto');
    }
}
