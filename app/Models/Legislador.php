<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Legislador extends Model
{
    use HasFactory;
    protected $table = 'legisladores';

    protected $fillable = [
        'ci', 'nombre', 'apellido', 'circunscripcion', 'telefono', 'email', 'fecha_nac', 'cargo', 'partido_id'
    ];

    public function partido()
    {
        return $this->belongsTo(PartidoPolitico::class);
    }

    public function periodos()
    {
        return $this->belongsToMany(PeriodoLegislativo::class, 'legislador_periodo_legislativo');
    }
}
