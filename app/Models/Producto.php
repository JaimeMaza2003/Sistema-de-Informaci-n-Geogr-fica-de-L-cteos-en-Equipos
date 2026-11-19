<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'user_id',
        'nombre',
        'categoria_id',
        'categoria_nombre',
        'descripcion',
        'presentacion',
        'precio_comercio',
        'precio_sugerido',
        'pedido_minimo',
        'capacidad_semanal',
        'vida_util_dias',
        'requiere_refrigeracion',
        'temperatura_conservacion',
        'zonas_entrega',
        'frecuencia_entrega',
        'registro_sanitario',
        'bpm_disponible',
        'ruc_disponible',
        'tabla_nutricional',
        'imagen_url',
        'activo',
    ];

    protected $casts = [
        'precio_comercio'        => 'float',
        'precio_sugerido'        => 'float',
        'vida_util_dias'         => 'integer',
        'requiere_refrigeracion' => 'boolean',
        'bpm_disponible'         => 'boolean',
        'ruc_disponible'         => 'boolean',
        'tabla_nutricional'      => 'boolean',
        'activo'                 => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function oportunidades()
    {
        return $this->hasMany(Oportunidad::class);
    }

    /**
     * ALGORITMO DE VINCULACIÓN Y COMPATIBILIDAD (Objetivo 5)
     * Encuentra establecimientos compatibles para este producto,
     * calcula un puntaje de afinidad y genera una explicación detallada.
     */
    public function buscarEstablecimientosCompatibles($limite = 30)
    {
        $establecimientos = Establecimiento::conLacteos()->get();
        $compatibles = [];

        $categoriaNorm = strtolower($this->categoria_nombre);
        $zonasArray = array_map('trim', explode(',', strtolower($this->zonas_entrega)));

        foreach ($establecimientos as $est) {
            $puntos = 0;
            $motivos = [];

            // 1. Compatibilidad de Categoría de Producto
            $prodsEst = strtolower($est->productos_lacteos . ' ' . $est->producto_mayor_demanda);
            $keywords = ['leche', 'queso', 'yogurt', 'mantequilla'];
            $matchCat = false;
            foreach ($keywords as $kw) {
                if (str_contains($categoriaNorm, $kw) && str_contains($prodsEst, $kw)) {
                    $matchCat = true;
                    break;
                }
            }

            if ($matchCat) {
                $puntos += 35;
                $motivos[] = "Vende productos de tu misma categoría ({$this->categoria_nombre})";
            } elseif ($est->comercializa_lacteos) {
                $puntos += 15;
                $motivos[] = "Comercializa lácteos y busca diversificar su oferta";
            }

            // 2. Zona de entrega y cobertura
            $parroquiaEst = strtolower($est->parroquia);
            $matchZona = false;
            foreach ($zonasArray as $zona) {
                if (str_contains($parroquiaEst, $zona) || str_contains($zona, $parroquiaEst) || str_contains($zona, 'latacunga')) {
                    $matchZona = true;
                    break;
                }
            }
            if ($matchZona) {
                $puntos += 25;
                $motivos[] = "Está dentro de tu zona de entrega declarada ({$est->parroquia})";
            }

            // 3. Disponibilidad para recibir proveedores
            if ($est->recepcion_proveedores === 'Recibe propuestas' || $est->interes_nuevos_proveedores) {
                $puntos += 20;
                $motivos[] = "Está abierto y confirmado para recibir nuevos proveedores";
            }

            // 4. Interés en productos artesanales y asociaciones
            if ($est->interes_artesanales) {
                $puntos += 10;
                $motivos[] = "Tiene interés explícito en productos artesanales y de productores locales";
            }

            // 5. Cadena de frío y condiciones de conservación
            if ($this->requiere_refrigeracion) {
                if ($est->tiene_refrigeracion) {
                    $puntos += 10;
                    $motivos[] = "Cuenta con infraestructura de refrigeración adecuada ({$est->capacidad_refrigeracion})";
                } else {
                    $puntos -= 25; // Penalización por falta de refrigeración
                }
            } else {
                $puntos += 10;
            }

            // Filtrar solo los que tienen afinidad aceptable (> 30 puntos)
            if ($puntos >= 35) {
                $compatibles[] = [
                    'establecimiento'   => $est,
                    'puntaje'           => min(100, max(0, $puntos)),
                    'explicacion'       => implode(', ', $motivos) . '.',
                    'distancia'         => $est->distanciaA(-0.78119, -78.57534), // Distancia de referencia
                ];
            }
        }

        // Ordenar por puntaje descendente
        usort($compatibles, fn($a, $b) => $b['puntaje'] <=> $a['puntaje']);

        return array_slice($compatibles, 0, $limite);
    }
}
