<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivosDocumento extends Model
{
    use HasFactory;

    protected $table = 'archivos_documentos';
    protected $fillable = [
        'id_mentrada', 'nombre_archivo', 'ruta_archivo',
    ];
}
