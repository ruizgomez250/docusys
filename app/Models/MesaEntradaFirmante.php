<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MesaEntradaFirmante extends Model
{
    use HasFactory;

    protected $table = 'mesa_entrada_firmante';

    // Definir los campos que se pueden llenar con asignación masiva
    protected $fillable = [
        'id_mentrada',
        'id_firmante',
        'tipo',
    ];

    // Definir las relaciones (asumiendo que existen modelos para 'MesaEntrada' y 'Firmante')
    
    public function mesaEntrada()
    {
        return $this->belongsTo(MesaEntrada::class, 'id_mentrada');
    }

    public function firmante()
    {
        return $this->belongsTo(Firmante::class, 'id_firmante');
    }
}
