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
    ];

    // Definir las relaciones con MesaEntrada y Firmante
    public function mesaEntrada()
    {
        return $this->belongsTo(MesaEntrada::class, 'id_mentrada');
    }

    public function firmante()
    {
        return $this->belongsTo(Firmante::class, 'id_firmante');
    }

    /**
     * Obtener los nombres de los firmantes asociados a una mesa de entrada y concatenarlos con comas.
     *
     * @param int $idMesaEntrada
     * @return string
     */
    public static function obtenerFirmantesPorMesaEntrada($idMesaEntrada)
    {
        // Obtener los firmantes asociados a esa mesa de entrada
        $firmantes = self::where('id_mentrada', $idMesaEntrada)
            ->with('firmante')
            ->get()
            ->pluck('firmante.nombre')
            ->filter(); // Filtramos posibles valores nulos

        // Unir los nombres en una sola cadena, separados por comas
        return $firmantes->implode(', ');
    }                
}