<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Legislador extends Model
{
    use HasFactory;
    protected $table = 'legisladores';

    protected $fillable = ['persona_id', 'cargo', 'partido_id', 'periodo_legislativo_id', 'activo'];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function partido()
    {
        return $this->belongsTo(PartidoPolitico::class, 'partido_id');
    }

    public function periodos()
    {
        return $this->belongsToMany(PeriodoLegislativo::class, 'legislador_periodo_legislativo')
                    ->withTimestamps();
    }


    public function designaciones()
    {
        return $this->hasMany(Designacion::class, 'legislador_id');
    }

    public function registrosAsistencia()
    {
        return $this->hasMany(RegistroAsistencia::class, 'legislador_id');
    }
}
