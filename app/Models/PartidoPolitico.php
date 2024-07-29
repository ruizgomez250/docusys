<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartidoPolitico extends Model
{
    use HasFactory;
    protected $table = 'partidos_politicos';

    protected $fillable = ['nombre', 'sigla', 'descripcion'];

    public function legisladores()
    {
        return $this->hasMany(Legislador::class, 'partido_id');
    }
}
