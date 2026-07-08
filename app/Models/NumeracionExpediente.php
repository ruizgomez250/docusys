<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumeracionExpediente extends Model
{
    use HasFactory;

    protected $table = 'numeracion_expedientes';

    protected $fillable = [
        'nro_expediente',
        'anho',
    ];
}
