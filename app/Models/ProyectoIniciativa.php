<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyectoIniciativa extends Model
{
    use HasFactory;

    protected $table = 'proyectos_iniciativas';

    protected $fillable = ['nombre'];
}
