<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecorridoDoc extends Model
{
    use HasFactory;

    // Especificar la tabla asociada al modelo
    protected $table = 'recorrido_doc';

    // Definir los campos que se pueden asignar masivamente
    protected $fillable = [
        'descripcion',
        'id_mentrada',
        'fecha',
        'id_usuario',
    ];

    // Definir la relación con la tabla mesa_entrada
    public function mesaEntrada()
    {
        return $this->belongsTo(MesaEntrada::class, 'id_mentrada');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
    
}
