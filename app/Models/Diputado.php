<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diputado extends Model
{
    use HasFactory;

    protected $table = 'diputados';

    protected $fillable = ['nombre', 'agrupacion', 'orden'];

    public function scopeOrdenados($query)
    {
        return $query->orderByRaw('orden IS NULL')->orderBy('orden')->orderBy('nombre');
    }
}
