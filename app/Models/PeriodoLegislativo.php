<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodoLegislativo extends Model
{
    use HasFactory;
    protected $table = 'periodos_legislativos';

    protected $fillable = [
        'nombre', 'inicio', 'fin', 'activo'
    ];

    public function legisladores()
    {
        return $this->belongsToMany(Legislador::class, 'legislador_periodo_legislativo');
    }


}
