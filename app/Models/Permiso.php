<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;

    protected $fillable = [
        'legislador_id',
        'tipo_permiso',
        'fecha',
        'observacion',
        'estado',
    ];

    public function legislador()
    {
        return $this->belongsTo(Legislador::class);
    }
    
}

