<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyectoDestino extends Model
{
    use HasFactory;

    protected $table = 'proyectos_destinos';

    protected $fillable = ['nombre'];
}
