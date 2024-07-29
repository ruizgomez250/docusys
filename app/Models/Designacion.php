<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designacion extends Model
{
    use HasFactory;
    protected $table = 'designaciones';

    protected $fillable = ['legislador_id', 'entidad_id', 'cargo_id', 'fecha_inicio', 'fecha_fin'];

    public function legislador()
    {
        return $this->belongsTo(Legislador::class, 'legislador_id');
    }

    public function entidad()
    {
        return $this->belongsTo(Entidad::class, 'entidad_id');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }
}
