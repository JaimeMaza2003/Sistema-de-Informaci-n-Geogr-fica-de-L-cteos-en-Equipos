<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Establecimiento extends Model
{
    protected $table = 'establecimientos';

    protected $fillable = [
        'numero_encuesta',
        'nombre_establecimiento',
        'nombre_propietario',
        'tipo_establecimiento',
        'direccion_completa',
        'provincia',
        'canton',
        'parroquia',
        'barrio_sector',
        'telefono',
        'correo_electronico',
        'horario_atencion',
        'horario_proveedores',
        'clientes_por_dia',
        'comercializa_lacteos',
        'productos_lacteos',
        'marcas_vende',
        'vende_productores_locales',
        'producto_mayor_demanda',
        'volumen_compra_semanal',
        'frecuencia_abastecimiento',
        'principal_proveedor',
        'tiene_refrigeracion',
        'capacidad_refrigeracion',
        'recepcion_proveedores',
        'interes_nuevos_proveedores',
        'interes_artesanales',
        'requisitos_ingreso',
        'condiciones_compra',
        'tipo_decision',
        'contacto_decision',
        'verificado',
        'reportado',
        'tipo_local',
        'observaciones',
        'latitud',
        'longitud',
        'fecha_verificacion',
        'user_id',
    ];

    protected $casts = [
        'latitud'                    => 'float',
        'longitud'                   => 'float',
        'comercializa_lacteos'       => 'boolean',
        'vende_productores_locales'  => 'boolean',
        'tiene_refrigeracion'        => 'boolean',
        'interes_nuevos_proveedores' => 'boolean',
        'interes_artesanales'        => 'boolean',
        'verificado'                 => 'boolean',
        'reportado'                  => 'boolean',
        'clientes_por_dia'           => 'integer',
        'fecha_verificacion'         => 'date',
    ];

    public function oportunidades()
    {
        return $this->hasMany(Oportunidad::class);
    }

    public function propuestas()
    {
        return $this->hasMany(Propuesta::class);
    }

    public function reportes()
    {
        return $this->hasMany(ReporteEstablecimiento::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeConLacteos($q)
    {
        return $q->where('comercializa_lacteos', true);
    }

    public function scopeRecibenProveedores($q)
    {
        return $q->where(function($sub) {
            $sub->where('recepcion_proveedores', 'Recibe propuestas')
                ->orWhere('interes_nuevos_proveedores', true);
        });
    }

    public function scopeEnParroquia($q, $parroquia)
    {
        return $q->where('parroquia', $parroquia);
    }

    public function scopeDeTipo($q, $tipo)
    {
        return $q->where('tipo_establecimiento', $tipo);
    }

    // Distancia en Km usando fórmula del semiverseno (Haversine)
    public function distanciaA($lat, $lng): float
    {
        if (!$this->latitud || !$this->longitud || !$lat || !$lng) {
            return 9999.0;
        }

        $earthRadius = 6371; // km
        $dLat = deg2rad($this->latitud - $lat);
        $dLng = deg2rad($this->longitud - $lng);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat)) * cos(deg2rad($this->latitud)) *
             sin($dLng/2) * sin($dLng/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return round($earthRadius * $c, 2);
    }

    // Link a Google Maps "Cómo llegar"
    public function getUrlComoLlegarAttribute(): string
    {
        return "https://www.google.com/maps/dir/?api=1&destination={$this->latitud},{$this->longitud}";
    }

    // Link directo a WhatsApp
    public function getUrlWhatsappAttribute(): ?string
    {
        if (!$this->telefono) {
            return null;
        }
        $tel = preg_replace('/[^0-9]/', '', $this->telefono);
        if (str_starts_with($tel, '0')) {
            $tel = '593' . substr($tel, 1);
        } elseif (!str_starts_with($tel, '593')) {
            $tel = '593' . $tel;
        }
        $msg = urlencode("Hola {$this->nombre_propietario}, te contacto desde la plataforma SIG Lácteos sobre tu establecimiento {$this->nombre_establecimiento}.");
        return "https://wa.me/{$tel}?text={$msg}";
    }
}
