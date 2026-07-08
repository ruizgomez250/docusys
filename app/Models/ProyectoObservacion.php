<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyectoObservacion extends Model
{
    use HasFactory;

    protected $table = 'proyecto_observaciones';

    protected $fillable = [
        'id_proyecto',
        'numero',
        'contenido',
    ];

    public function proyecto()
    {
        return $this->belongsTo(ProyectosEnEstudio::class, 'id_proyecto');
    }
}
