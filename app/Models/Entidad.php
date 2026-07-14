<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entidad extends Model
{
    use HasFactory;
    protected $table = 'entidades';

    protected $fillable = ['nombre', 'tipo', 'descripcion'];

    public function designaciones()
    {
        return $this->hasMany(Designacion::class, 'entidad_id');
    }
}
