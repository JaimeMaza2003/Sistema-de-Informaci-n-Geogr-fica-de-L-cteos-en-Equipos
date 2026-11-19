<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReporteEstablecimiento extends Model
{
    protected $table = 'reportes_establecimiento';

    protected $fillable = [
        'user_id',
        'establecimiento_id',
        'tipo_error',
        'descripcion',
        'estado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class);
    }
}
