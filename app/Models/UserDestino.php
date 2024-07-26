<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDestino extends Model
{
    use HasFactory;

    protected $table = 'user_destino';

    protected $fillable = [
        'user_id',
        'destino_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function destino()
    {
        return $this->belongsTo(Destino::class);
    }
}
