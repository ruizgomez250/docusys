<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    use HasFactory;
    protected $table = 'cargos';

    protected $fillable = ['nombre', 'descripcion'];

    public function designaciones()
    {
        return $this->hasMany(Designacion::class, 'cargo_id');
    }
}
