<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapaRecorrido extends Model
{
    use HasFactory;

    // Definir la tabla asociada al modelo
    protected $table = 'mapa_recorrido';

    // Definir los campos que se pueden asignar masivamente
    protected $fillable = [
        'id_mentrada',
        'fecha_recepcion',
        'id_actual',
        'id_destino',
        'observacion',
        'estado',
    ];

    // Definir las relaciones
    public function mesaEntrada()
    {
        return $this->belongsTo(MesaEntrada::class, 'id_mentrada');
    }

    public function destinoActual()
    {
        return $this->belongsTo(Destino::class, 'id_actual');
    }

    public function destino()
    {
        return $this->belongsTo(Destino::class, 'id_destino');
    }
}
