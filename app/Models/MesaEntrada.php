<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MesaEntrada extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mesa_entrada';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nro_mentrada',
        'anho',
        'fecha_recepcion',
        'id_origen',
        'id_tipo_doc',
        'id_tipo_docr',
        'id_destino',
        'observacion',
        'estado',
        'id_usuario',
        'duplicado',
        'nro_suplementario',
        'modificar',
    ];


    /**
     * Get the user that owns the mesa_entrada.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Get the origen record associated with the mesa_entrada.
     */
    public function origen()
    {
        return $this->belongsTo(Origen::class, 'id_origen');
    }

    /**
     * Get the tipo_doc record associated with the mesa_entrada.
     */
    public function tipoDoc()
    {
        return $this->belongsTo(TipoDoc::class, 'id_tipo_doc');
    }

    public function tipoDocR()
    {
        return $this->belongsTo(TipoDocR::class, 'id_tipo_docr');
    }
    /**
     * Get the destino record associated with the mesa_entrada.
     */
    public function destino()
    {
        return $this->belongsTo(Destino::class, 'id_destino');
    }
    public function recorridoDocs()
    {
        return $this->hasMany(RecorridoDoc::class, 'id_mentrada');
    }

    public function documentos()
    {
        return $this->hasMany(ArchivosDocumento::class, 'id_mentrada', 'id');
    }

    public function firmantes()
    {
        return $this->belongsToMany(Firmante::class, 'mesa_entrada_firmante', 'id_mentrada', 'id_firmante');
    }
    public function mapaRecorridos()
    {
        return $this->hasMany(MapaRecorrido::class, 'id_mentrada');
    }
    public function ultimoRecorrido()
    {
        return $this->hasOne(MapaRecorrido::class, 'id_mentrada', 'id')
            ->latest('created_at'); // trae el último por fecha
    }
}
