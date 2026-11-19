<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha Comercial - {{ $producto->nombre }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 13px; color: #333; background: #fff; }
        .ficha-header { border-bottom: 3px solid #1e5f74; padding-bottom: 15px; margin-bottom: 20px; }
        .table-custom th { background-color: #f1f5f9; width: 35%; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body class="p-4">
    <div class="container" style="max-width: 800px;">
        <div class="no-print d-flex justify-content-between align-items-center mb-4 p-2 bg-light rounded border">
            <span>Vista de Impresión / Descarga en PDF</span>
            <div>
                <button onclick="window.print()" class="btn btn-primary btn-sm">
                    Imprimir / Guardar como PDF
                </button>
                <button onclick="window.close()" class="btn btn-secondary btn-sm">Cerrar</button>
            </div>
        </div>

        <div class="ficha-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-0 text-primary">FICHA TÉCNICA COMERCIAL</h3>
                <small class="text-muted">SIG Lácteos Cotopaxi • Red de Conexión Comercial</small>
            </div>
            <div class="text-end">
                <span class="badge bg-secondary">{{ $producto->categoria_nombre }}</span>
                <div class="small text-muted mt-1">Fecha: {{ date('d/m/Y') }}</div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-8">
                <h4 class="fw-bold">{{ $producto->nombre }}</h4>
                <p class="text-muted">{{ $producto->descripcion ?: 'Producto lácteo artesanal elaborado con leche de alta calidad en el cantón Latacunga, provincia de Cotopaxi.' }}</p>
            </div>
            <div class="col-4 text-end">
                <div class="p-3 bg-light rounded border text-center">
                    <small class="text-muted d-block">Precio al Comercio</small>
                    <h3 class="fw-bold text-success mb-0">${{ number_format($producto->precio_comercio, 2) }}</h3>
                    <small class="text-muted">PVP Sugerido: ${{ number_format($producto->precio_sugerido, 2) }}</small>
                </div>
            </div>
        </div>

        <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">1. ESPECIFICACIONES TÉCNICAS DEL PRODUCTO</h6>
        <table class="table table-bordered table-custom mb-4">
            <tr>
                <th>Presentación</th>
                <td>{{ $producto->presentacion }}</td>
            </tr>
            <tr>
                <th>Vida Útil</th>
                <td>{{ $producto->vida_util_dias }} días a partir de elaboración</td>
            </tr>
            <tr>
                <th>Requerimientos de Frío</th>
                <td>{{ $producto->requiere_refrigeracion ? 'Sí, conservar entre ' . $producto->temperatura_conservacion : 'Temperatura ambiente' }}</td>
            </tr>
            <tr>
                <th>Registro Sanitario</th>
                <td>{{ $producto->registro_sanitario ?: 'En trámite / Notificación disponible' }}</td>
            </tr>
            <tr>
                <th>Certificaciones Disponibles</th>
                <td>
                    {{ $producto->bpm_disponible ? '✅ Buenas Prácticas de Manufactura (BPM)' : '' }}
                    {{ $producto->ruc_disponible ? ' • ✅ RUC / Facturación' : '' }}
                    {{ $producto->tabla_nutricional ? ' • ✅ Tabla Nutricional' : '' }}
                </td>
            </tr>
        </table>

        <h6 class="fw-bold text-primary border-bottom pb-1 mb-2">2. CONDICIONES COMERCIALES Y DE ENTREGA</h6>
        <table class="table table-bordered table-custom mb-4">
            <tr>
                <th>Pedido Mínimo</th>
                <td>{{ $producto->pedido_minimo }}</td>
            </tr>
            <tr>
                <th>Capacidad de Abastecimiento</th>
                <td>{{ $producto->capacidad_semanal }}</td>
            </tr>
            <tr>
                <th>Zonas de Cobertura</th>
                <td>{{ $producto->zonas_entrega }}</td>
            </tr>
            <tr>
                <th>Frecuencia y Días de Entrega</th>
                <td>{{ $producto->frecuencia_entrega }}</td>
            </tr>
            <tr>
                <th>Condiciones de Pago Aceptadas</th>
                <td>Contado contra entrega / Crédito a convenir según historial</td>
            </tr>
        </table>

        <div class="mt-4 pt-3 border-top d-flex justify-content-between small text-muted">
            <div>
                <strong>Emprendimiento:</strong> Lácteos Artesanales del Cotopaxi<br>
                <strong>Contacto:</strong> 0987654321 • emprendedor@lacteosandinos.com
            </div>
            <div class="text-end">
                Plataforma SIG Lácteos Cotopaxi<br>
                Catálogo validado para colocación en percha
            </div>
        </div>
    </div>
</body>
</html>
