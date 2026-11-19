@extends('layouts.app')

@section('title', $establecimiento->nombre_establecimiento . ' - Ficha Comercial')

@section('content')
<div class="container py-3">
    <!-- Navegación de migas de pan -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('mapa.index') }}">Mapa de Oportunidades</a></li>
            <li class="breadcrumb-item"><a href="{{ route('mapa.index', ['parroquia' => $establecimiento->parroquia]) }}">{{ $establecimiento->parroquia }}</a></li>
            <li class="breadcrumb-item active">{{ $establecimiento->nombre_establecimiento }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Columna Izquierda: Información Principal -->
        <div class="col-12 col-lg-8">
            <div class="card card-custom mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                        <div>
                            <span class="badge badge-{{ strtolower($establecimiento->tipo_establecimiento) }} px-3 py-2 fs-6">
                                {{ $establecimiento->tipo_establecimiento }}
                            </span>
                            @if($establecimiento->verificado)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 ms-1">
                                    <i class="bi bi-patch-check-fill"></i> Verificado ({{ $establecimiento->fecha_verificacion ? $establecimiento->fecha_verificacion->format('d/m/Y') : 'Nov 2026' }})
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 ms-1">
                                    <i class="bi bi-clock-history"></i> Pendiente de verificación
                                </span>
                            @endif
                        </div>
                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalReportar">
                            <i class="bi bi-flag me-1"></i> Reportar información incorrecta
                        </button>
                    </div>

                    <h2 class="fw-bold mb-2">{{ $establecimiento->nombre_establecimiento }}</h2>
                    <p class="text-muted fs-6 mb-4">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                        {{ $establecimiento->direccion_completa ?? $establecimiento->barrio_sector }}, {{ $establecimiento->parroquia }}, {{ $establecimiento->canton }} ({{ $establecimiento->provincia }})
                    </p>

                    <!-- Acciones Rápidas Principales (Objetivo 6) -->
                    <div class="d-flex flex-wrap gap-2 mb-4 p-3 bg-light rounded-3 border">
                        @if(!$oportunidadExistente)
                            <form method="POST" action="{{ route('establecimientos.guardar-oportunidad', $establecimiento->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="bi bi-bookmark-plus me-1"></i> Guardar Oportunidad
                                </button>
                            </form>
                        @else
                            <a href="{{ route('oportunidades.index') }}" class="btn btn-success">
                                <i class="bi bi-check-all me-1"></i> En "Mis Oportunidades" ({{ $oportunidadExistente->estado_label }})
                            </a>
                        @endif

                        @if($establecimiento->telefono)
                            <a href="{{ $establecimiento->url_whatsapp }}" target="_blank" class="btn btn-success">
                                <i class="bi bi-whatsapp me-1"></i> Contactar por WhatsApp
                            </a>
                            <a href="tel:{{ $establecimiento->telefono }}" class="btn btn-outline-dark">
                                <i class="bi bi-telephone me-1"></i> Llamar
                            </a>
                        @endif

                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPropuesta">
                            <i class="bi bi-send-fill me-1"></i> Enviar Propuesta Comercial
                        </button>

                        <a href="{{ $establecimiento->url_como_llegar }}" target="_blank" class="btn btn-outline-secondary">
                            <i class="bi bi-sign-turn-right me-1"></i> Cómo llegar (GPS)
                        </a>
                    </div>

                    <!-- Tabla de Condiciones y Requisitos Comerciales (Objetivo 3) -->
                    <h5 class="fw-bold border-bottom pb-2 mb-3 text-primary">
                        <i class="bi bi-clipboard2-data me-1"></i> Condiciones de Comercialización y Recepción
                    </h5>
                    
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle">
                            <tbody>
                                <tr>
                                    <th class="bg-light" style="width: 35%;">Recepción de Proveedores</th>
                                    <td>
                                        @if($establecimiento->recepcion_proveedores == 'Recibe propuestas')
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Recibe propuestas</span>
                                        @else
                                            <span class="badge bg-secondary">No recibe por ahora / Por confirmar</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Interés en Productos Artesanales / Asociaciones</th>
                                    <td>
                                        {{ $establecimiento->interes_artesanales ? '✅ Sí, interesado en productores locales y artesanales' : '❌ No prioritario' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Horario de Atención a Proveedores</th>
                                    <td>{{ $establecimiento->horario_proveedores ?? '08:00 a 12:00 (Lunes a Viernes)' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Horario de Atención al Público</th>
                                    <td>{{ $establecimiento->horario_atencion ?? '08:00 a 20:00' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Requisitos de Ingreso</th>
                                    <td>{{ $establecimiento->requisitos_ingreso ?? 'Muestra previa y ficha técnica' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Condiciones de Compra</th>
                                    <td>{{ $establecimiento->condiciones_compra ?? 'Pago contra entrega' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Infraestructura de Frío</th>
                                    <td>
                                        {{ $establecimiento->tiene_refrigeracion ? '✅ Cuenta con refrigeración (' . ($establecimiento->capacidad_refrigeracion ?? 'disponible') . ')' : '❌ No cuenta con refrigeración' }}
                                    </td>
                                </tr>
                                @if(strtolower($establecimiento->tipo_establecimiento) === 'supermercado')
                                <tr>
                                    <th class="bg-light text-danger">Toma de Decisiones (Supermercado)</th>
                                    <td>
                                        <strong>{{ $establecimiento->tipo_decision === 'oficina_central' ? 'Oficina Central / Matriz' : 'En esta Sucursal' }}</strong>
                                        <div class="small text-muted">Contacto responsable: {{ $establecimiento->contacto_decision ?? 'Gerencia de Compras' }}</div>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Datos Comerciales Actuales -->
                    <h5 class="fw-bold border-bottom pb-2 mb-3 text-primary">
                        <i class="bi bi-cart3 me-1"></i> Líneas y Demanda Actual
                    </h5>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">Categorías de Lácteos que vende</small>
                                <strong>{{ $establecimiento->productos_lacteos ?: 'Leche, Queso, Yogurt, Mantequilla' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">Producto con Mayor Demanda</small>
                                <strong class="text-success">{{ $establecimiento->producto_mayor_demanda ?: 'Leche entera' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">Marcas que comercializa</small>
                                <span>{{ $establecimiento->marcas_vende ?: 'Toni, Parmalat, Vita' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">Frecuencia y Volumen de Compra</small>
                                <span>{{ $establecimiento->frecuencia_abastecimiento ?? 'Semanal' }} ({{ $establecimiento->volumen_compra_semanal ?? 'N/D' }})</span>
                            </div>
                        </div>
                    </div>

                    @if($establecimiento->observaciones)
                        <div class="alert alert-info border-0 mt-3">
                            <i class="bi bi-info-circle-fill me-1"></i> <strong>Observaciones de campo:</strong> {{ $establecimiento->observaciones }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Mapa Exacto & Contacto -->
        <div class="col-12 col-lg-4">
            <!-- Tarjeta de Contacto Comercial -->
            <div class="card card-custom mb-4">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-person-lines-fill text-primary me-2"></i>Contacto Comercial</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Nombre del Responsable</small>
                        <h6 class="fw-bold">{{ $establecimiento->nombre_propietario ?: 'Administrador del Local' }}</h6>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Teléfono / WhatsApp</small>
                        <h6 class="fw-bold">{{ $establecimiento->telefono ?: 'No disponible' }}</h6>
                    </div>
                    @if($establecimiento->correo_electronico)
                    <div class="mb-3">
                        <small class="text-muted d-block">Correo Electrónico</small>
                        <span>{{ $establecimiento->correo_electronico }}</span>
                    </div>
                    @endif
                    <div>
                        <small class="text-muted d-block">Tipo de Tenencia del Local</small>
                        <span>{{ $establecimiento->tipo_local ?: 'Propio' }}</span>
                    </div>
                </div>
            </div>

            <!-- Mapa con Punto Exacto -->
            <div class="card card-custom mb-4">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Punto Exacto en el Mapa</h5>
                </div>
                <div class="card-body p-2">
                    <div id="miniMap" style="height: 250px; border-radius: 8px;"></div>
                    <div class="p-2 text-center small text-muted">
                        Lat: {{ $establecimiento->latitud }}, Lng: {{ $establecimiento->longitud }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: ENVIAR PROPUESTA (Objetivo 6) -->
<div class="modal fade" id="modalPropuesta" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('propuestas.store') }}">
                @csrf
                <input type="hidden" name="establecimiento_id" value="{{ $establecimiento->id }}">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-send me-1"></i> Enviar Propuesta Comercial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        Presenta tus productos a <strong>{{ $establecimiento->nombre_establecimiento }}</strong>. Se creará un registro de contacto en "Mis Oportunidades".
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Selecciona productos de tu catálogo</label>
                        @forelse($productos as $prod)
                            <div class="form-check border p-2 rounded mb-1">
                                <input class="form-check-input ms-1 me-2" type="checkbox" name="productos_seleccionados[]" value="{{ $prod->id }}" id="p{{ $prod->id }}" checked>
                                <label class="form-check-label w-100" for="p{{ $prod->id }}">
                                    <strong>{{ $prod->nombre }}</strong> ({{ $prod->presentacion }}) - <span class="text-success">${{ number_format($prod->precio_comercio, 2) }}</span>
                                </label>
                            </div>
                        @empty
                            <div class="alert alert-warning small">No tienes productos registrados en tu catálogo. <a href="{{ route('productos.create') }}">Registrar uno ahora</a>.</div>
                        @endforelse
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Mensaje de presentación / propuesta</label>
                        <textarea name="mensaje" class="form-control" rows="4" required placeholder="Estimado/a, somos productores locales del Cotopaxi y nos gustaría ofrecerles nuestros productos lácteos con entrega semanal directa y garantía de frescura...">Estimado/a {{ $establecimiento->nombre_propietario ?: 'administrador' }}, nos ponemos en contacto desde SIG Lácteos para presentarle nuestra línea de lácteos artesanales del Cantón Latacunga, con excelente margen comercial y entrega puntual en su local.</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send-fill me-1"></i> Enviar Propuesta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: REPORTAR INFORMACIÓN (Objetivo 8) -->
<div class="modal fade" id="modalReportar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('establecimientos.reportar', $establecimiento->id) }}">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-danger"><i class="bi bi-flag-fill me-1"></i> Reportar Información Incorrecta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Tipo de error detectado</label>
                        <select name="tipo_error" class="form-select" required>
                            <option value="Telefono incorrecto o fuera de servicio">Teléfono incorrecto o fuera de servicio</option>
                            <option value="Negocio cerrado definitivamente">Negocio cerrado definitivamente</option>
                            <option value="Ya no comercializa productos lácteos">Ya no comercializa productos lácteos</option>
                            <option value="Ubicación GPS en el mapa incorrecta">Ubicación GPS en el mapa incorrecta</option>
                            <option value="Cambio de propietario o nombre">Cambio de propietario o nombre</option>
                            <option value="Otro">Otro motivo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Detalle de la corrección</label>
                        <textarea name="descripcion" class="form-control" rows="3" required placeholder="Describe la información correcta o los detalles..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Enviar Reporte</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Inicializar mini mapa con la ubicación exacta
    const lat = {{ $establecimiento->latitud }};
    const lng = {{ $establecimiento->longitud }};

    const miniMap = L.map('miniMap').setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(miniMap);

    L.marker([lat, lng]).addTo(miniMap)
        .bindPopup("<strong>{{ $establecimiento->nombre_establecimiento }}</strong><br>{{ $establecimiento->direccion_completa }}")
        .openPopup();
</script>
@endpush
