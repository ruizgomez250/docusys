<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodoLegislativo extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'periodos_legislativos';

    // Atributos asignables en masa
    protected $fillable = ['nombre', 'inicio', 'fin', 'activo'];

    // Relación con los legisladores
    public function legisladores()
    {
        return $this->belongsToMany(Legislador::class, 'legislador_periodo_legislativo')
                    ->withTimestamps();
    }
}
