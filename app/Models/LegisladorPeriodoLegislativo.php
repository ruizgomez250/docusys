<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegisladorPeriodoLegislativo extends Model
{
    use HasFactory;

    protected $table = 'legislador_periodo_legislativo';

    protected $fillable = [
        'legislador_id',
        'periodo_legislativo_id',
    ];

    // Definir relaciones si es necesario
    public function legislador()
    {
        return $this->belongsTo(Legislador::class);
    }

    public function periodoLegislativo()
    {
        return $this->belongsTo(PeriodoLegislativo::class);
    }
}
