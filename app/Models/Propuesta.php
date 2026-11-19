<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Propuesta extends Model
{
    protected $table = 'propuestas';

    protected $fillable = [
        'user_id',
        'establecimiento_id',
        'oportunidad_id',
        'mensaje',
        'productos_json',
        'estado',
        'respuesta_comercio',
        'fecha_visita_propuesta',
    ];

    protected $casts = [
        'productos_json'         => 'array',
        'fecha_visita_propuesta' => 'datetime',
    ];

    public const RESPUESTAS_COMERCIO = [
        'me_interesa'     => 'Me interesa',
        'solicito_info'   => 'Solicito información',
        'agendar_visita'  => 'Agendar visita',
        'no_interesado'   => 'No interesado por ahora',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class);
    }

    public function oportunidad()
    {
        return $this->belongsTo(Oportunidad::class);
    }
}
