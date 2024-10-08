<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destino extends Model
{
    use HasFactory;

    protected $table = 'destinos';

    protected $fillable = [
        'nombre','default',
    ];
    public function destino()
    {
        return $this->belongsTo(Destino::class, 'id_destino');
    }
}
