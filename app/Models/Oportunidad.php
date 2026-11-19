<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Oportunidad extends Model
{
    protected $table = 'oportunidades';

    protected $fillable = [
        'user_id',
        'establecimiento_id',
        'producto_id',
        'estado',
        'motivo_cierre',
        'notas_privadas',
        'fecha_proxima_gestion',
        'condiciones_acordadas',
        'monto_primer_pedido',
        'fecha_primer_pedido',
    ];

    protected $casts = [
        'fecha_proxima_gestion' => 'date',
        'fecha_primer_pedido'   => 'date',
        'monto_primer_pedido'   => 'float',
    ];

    public const ESTADOS = [
        'por_contactar'     => ['nombre' => 'Por contactar',     'color' => 'secondary', 'icono' => 'bi-telephone-plus'],
        'contactado'        => ['nombre' => 'Contactado',        'color' => 'info',      'icono' => 'bi-chat-dots'],
        'muestra_entregada' => ['nombre' => 'Muestra entregada', 'color' => 'primary',   'icono' => 'bi-box'],
        'en_negociacion'    => ['nombre' => 'En negociación',    'color' => 'warning',   'icono' => 'bi-cash-coin'],
        'primer_pedido'     => ['nombre' => 'Primer pedido',     'color' => 'success',   'icono' => 'bi-cart-check'],
        'punto_activo'      => ['nombre' => 'Punto de venta activo', 'color' => 'success', 'icono' => 'bi-shop'],
        'no_concretada'     => ['nombre' => 'No concretada',     'color' => 'danger',    'icono' => 'bi-x-circle'],
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function propuestas()
    {
        return $this->hasMany(Propuesta::class);
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado]['nombre'] ?? $this->estado;
    }

    public function getEstadoBadgeAttribute(): string
    {
        $color = self::ESTADOS[$this->estado]['color'] ?? 'secondary';
        return "badge bg-{$color}";
    }
}
