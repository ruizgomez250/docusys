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
        'edicion_habilitada',
        'edicion_habilitada_at',
        'edicion_habilitada_por',
        'usar_documento_padre',
        'documento_padre_id',
        'nro_expediente',
        'nro_expediente_silpy',
        'anho',
        'camara',
        'acapite',
        'destino',
        'iniciativa',
        'diputados',
        'presentado_por',
        'fecha_recepcion_texto',
        'membrete',
        'leyenda',
        'cantidad_observaciones',
        'contenido',
    ];

    protected $casts = [
        'edicion_habilitada' => 'boolean',
        'edicion_habilitada_at' => 'datetime',
    ];

    public function mesaEntrada()
    {
        return $this->belongsTo(MesaEntrada::class, 'id_mentrada');
    }

    public function sesion()
    {
        return $this->belongsTo(Sesion::class, 'sesion_id');
    }

    public function sesiones()
    {
        return $this->belongsToMany(
            Sesion::class,
            'sesion_proyecto',
            'proyecto_id',
            'sesion_id'
        )->withTimestamps();
    }

    public function perteneceASesionFinalizada(): bool
    {
        if ($this->relationLoaded('sesiones')) {
            return $this->sesiones->contains(
                fn (Sesion $sesion) => $sesion->estado === Sesion::ESTADO_FINALIZADO
            );
        }

        return $this->sesiones()
            ->where('sesiones.estado', Sesion::ESTADO_FINALIZADO)
            ->exists();
    }

    public function estaBloqueadoPorFinalizacion(): bool
    {
        return $this->perteneceASesionFinalizada() && ! $this->edicion_habilitada;
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
