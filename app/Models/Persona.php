<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'personas';

    protected $fillable = ['ci', 'nombre', 'apellido', 'direccion', 'telefono', 'email', 'fecha_nac'];

    public function legisladores()
    {
        return $this->hasMany(Legislador::class, 'persona_id');
    }
}
