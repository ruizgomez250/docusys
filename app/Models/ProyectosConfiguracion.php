<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyectosConfiguracion extends Model
{
    use HasFactory;

    protected $table = 'proyectos_configuracion';

    protected $fillable = [
        'membrete',
        'leyenda',
        'tipo_sesion',
        'nro_sesion',
        'ultimo_nro_expediente',
    ];
}
