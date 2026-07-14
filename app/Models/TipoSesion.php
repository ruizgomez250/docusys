<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoSesion extends Model
{
    use HasFactory;
    protected $table = 'tipos_sesiones';

    protected $fillable = ['nombre', 'descripcion'];


}
