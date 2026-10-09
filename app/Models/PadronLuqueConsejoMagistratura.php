<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PadronLuqueConsejoMagistratura extends Model
{
    protected $table = 'padron_luque_consejo_magistratura';

    protected $guarded = [];

    protected $casts = [
        'egreso' => 'integer',
        'pagina_origen' => 'integer',
        'fila_origen' => 'integer',
    ];
}
