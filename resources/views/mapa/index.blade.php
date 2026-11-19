@extends('layouts.app')

@section('title', 'Mapa de Oportunidades Comerciales')

@push('styles')
<style>
    #mapContainer {
        height: calc(100vh - 170px);
        min-height: 500px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        z-index: 1;
    }
    .filter-panel {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        padding: 16px;
    }
    .store-card {
        transition: all 0.2s ease;
        border: 1px solid #e2e8f0;
    }
    .store-card:hover {
        border-color: #1e5f74;
        transform: translateY(-2px);
    }
    .custom-div-icon {
        background: transparent;
        border: none;
    }
    .marker-pin {
        width: 32px;
        height: 32px;
        border-radius: 50% 50% 50% 0;
        position: absolute;
        transform: rotate(-45deg);
        left: 50%;
        top: 50%;
        margin: -16px 0 0 -16px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
    }
    .marker-pin i {
        transform: rotate(45deg);
        color: white;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-lg-4">
    <!-- Barra superior de filtros rápidos y búsqueda -->
    <div class="filter-panel mb-3">
        <form method="GET" action="{{ route('mapa.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="vista" value="{{ $vista }}" id="inputVista">
            
            <div class="col-12 col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-start-0" placeholder="Buscar comercio, sector o dueño...">
                </div>
            </div>

            <div class="col-6 col-md-2">
                <select name="parroquia" class="form-select bg-light">
                    <option value="">Todas las parroquias</option>
                    @foreach($parroquias as $p)
                        <option value="{{ $p }}" {{ request('parroquia') == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select name="tipo" class="form-select bg-light">
                    <option value="">Todos los tipos</option>
                    @foreach($tipos as $t)
                        <option value="{{ $t }}" {{ request('tipo') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select name="categoria" class="form-select bg-light">
                    <option value="">Categoría producto</option>
                    <option value="Leche" {{ request('categoria') == 'Leche' ? 'selected' : '' }}>Leche</option>
                    <option value="Queso" {{ request('categoria') == 'Queso' ? 'selected' : '' }}>Queso</option>
                    <option value="Yogurt" {{ request('categoria') == 'Yogurt' ? 'selected' : '' }}>Yogurt</option>
                    <option value="Mantequilla" {{ request('categoria') == 'Mantequilla' ? 'selected' : '' }}>Mantequilla</option>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select name="recepcion" class="form-select bg-light">
                    <option value="">Recepción proveedores</option>
                    <option value="si" {{ request('recepcion') == 'si' ? 'selected' : '' }}>✅ Reciben propuestas</option>
                    <option value="artesanal" {{ request('recepcion') == 'artesanal' ? 'selected' : '' }}>⭐ Interés en artesanales</option>
                </select>
            </div>

            <div class="col-12 col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100" title="Filtrar">
                    <i class="bi bi-funnel-fill"></i>
                </button>
                <a href="{{ route('mapa.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>

        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
            <div class="small text-muted">
                Mostrando <strong>{{ $totalEncontrados }}</strong> comercios identificados en Latacunga
            </div>
            <!-- Alternador Mapa / Listado -->
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn {{ $vista == 'mapa' ? 'btn-primary' : 'btn-outline-primary' }}" onclick="cambiarVista('mapa')">
                    <i class="bi bi-map-fill me-1"></i> Mapa
                </button>
                <button type="button" class="btn {{ $vista == 'listado' ? 'btn-primary' : 'btn-outline-primary' }}" onclick="cambiarVista('listado')">
                    <i class="bi bi-list-ul me-1"></i> Listado
                </button>
            </div>
        </div>
    </div>

    <!-- VISTA: MAPA -->
    <div id="vistaMapa" style="{{ $vista == 'mapa' ? '' : 'display: none;' }}">
        <div class="row g-3">
            <div class="col-12 col-lg-9">
                <div id="mapContainer"></div>
            </div>
            <!-- Panel lateral de detalle rápido -->
            <div class="col-12 col-lg-3">
                <div class="card card-custom h-100" style="max-height: calc(100vh - 170px); overflow-y: auto;">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-cursor-fill me-1"></i> Comercio Seleccionado</h6>
                        <small class="text-muted">Haz clic en cualquier punto del mapa</small>
                    </div>
                    <div class="card-body" id="quickDetail">
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-pin-map display-4 text-secondary opacity-50"></i>
                            <p class="mt-2 small">Selecciona un marcador para ver la ficha rápida, contacto y requisitos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- VISTA: LISTADO -->
    <div id="vistaListado" style="{{ $vista == 'listado' ? '' : 'display: none;' }}">
        <div class="row g-3">
            @forelse($establecimientos as $est)
                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <div class="card card-custom store-card h-100">
                        <div class="card-body p-3 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge badge-{{ strtolower($est->tipo_establecimiento) }}">
                                    {{ $est->tipo_establecimiento }}
                                </span>
                                @if($est->recepcion_proveedores == 'Recibe propuestas')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle small">
                                        <i class="bi bi-check-circle"></i> Recibe propuestas
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary small">
                                        Por confirmar
                                    </span>
                                @endif
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $est->nombre_establecimiento }}</h6>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-geo-alt text-danger me-1"></i> {{ $est->parroquia }} • {{ $est->barrio_sector ?? 'Centro' }}
                            </p>
                            <div class="small bg-light p-2 rounded mb-3 flex-grow-1">
                                <div><strong>Lácteos:</strong> {{ Str::limit($est->productos_lacteos ?? 'Varios', 40) }}</div>
                                <div><strong>Refrigeración:</strong> {{ $est->tiene_refrigeracion ? 'Sí (' . ($est->capacidad_refrigeracion ?: 'disponible') . ')' : 'No tiene' }}</div>
                                @if($est->nombre_propietario)
                                    <div><strong>Contacto:</strong> {{ $est->nombre_propietario }}</div>
                                @endif
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('establecimientos.show', $est->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-card-text me-1"></i> Ver Ficha Completa
                                </a>
                                <div class="btn-group btn-group-sm">
                                    @if($est->telefono)
                                        <a href="{{ $est->url_whatsapp }}" target="_blank" class="btn btn-outline-success">
                                            <i class="bi bi-whatsapp"></i> Contactar
                                        </a>
                                    @endif
                                    <a href="{{ $est->url_como_llegar }}" target="_blank" class="btn btn-outline-secondary">
                                        <i class="bi bi-sign-turn-right"></i> Cómo llegar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-shop-window display-3 text-muted"></i>
                    <h5 class="mt-3">No se encontraron establecimientos con los filtros seleccionados</h5>
                    <a href="{{ route('mapa.index') }}" class="btn btn-primary mt-2">Restablecer filtros</a>
                </div>
            @endforelse
        </div>
        <div class="mt-4 d-flex justify-content-center">
            {{ $establecimientos->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Datos de marcadores pasados desde el backend
    const marcadoresData = @json($marcadores);

    // Inicialización del Mapa con Leaflet (OpenStreetMap universal)
    const map = L.map('mapContainer').setView([-0.78119, -78.57534], 12); // Centro en Cantón Latacunga

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors • SIG Lácteos Cotopaxi'
    }).addTo(map);

    // Colores según tipo de comercio
    const colorPorTipo = {
        'Tienda': '#0d6efd',
        'Minimarket': '#198754',
        'Supermercado': '#6f42c1',
        'Panaderia': '#fd7e14',
        'Panadería': '#fd7e14',
        'Distribuidor': '#d63384'
    };

    const iconoPorTipo = {
        'Tienda': 'bi-shop',
        'Minimarket': 'bi-basket',
        'Supermercado': 'bi-cart4',
        'Panaderia': 'bi-cake2',
        'Panadería': 'bi-cake2',
        'Distribuidor': 'bi-truck'
    };

    // Agrupación de marcadores (MarkerCluster)
    const markersGroup = L.markerClusterGroup({
        maxClusterRadius: 40,
        spiderfyOnMaxZoom: true
    });

    marcadoresData.forEach(m => {
        if (!m.latitud || !m.longitud) return;

        const color = colorPorTipo[m.tipo_establecimiento] || '#0d6efd';
        const icono = iconoPorTipo[m.tipo_establecimiento] || 'bi-shop';

        const customIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div class="marker-pin" style="background-color: ${color};">
                     <i class="bi ${icono}"></i>
                   </div>`,
            iconSize: [30, 42],
            iconAnchor: [15, 42]
        });

        const marker = L.marker([m.latitud, m.longitud], { icon: customIcon });

        // Popup interactivo
        const popupContent = `
            <div style="min-width: 200px;">
                <span class="badge" style="background-color: ${color};">${m.tipo_establecimiento}</span>
                <h6 class="fw-bold mb-1 mt-1">${m.nombre_establecimiento}</h6>
                <p class="small text-muted mb-1"><i class="bi bi-geo-alt"></i> ${m.parroquia}</p>
                <p class="small mb-2"><strong>Lácteos:</strong> ${m.productos_lacteos || 'Varios'}</p>
                <div class="d-grid gap-1">
                    <a href="/establecimientos/${m.id}" class="btn btn-primary btn-sm text-white">Ver Ficha</a>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=${m.latitud},${m.longitud}" target="_blank" class="btn btn-outline-secondary btn-sm">Cómo llegar</a>
                </div>
            </div>
        `;
        marker.bindPopup(popupContent);

        // Click en marcador actualiza panel lateral
        marker.on('click', () => {
            mostrarDetalleLateral(m);
        });

        markersGroup.addLayer(marker);
    });

    map.addLayer(markersGroup);

    // Función para mostrar detalle en panel lateral derecho
    function mostrarDetalleLateral(m) {
        const detailDiv = document.getElementById('quickDetail');
        const color = colorPorTipo[m.tipo_establecimiento] || '#0d6efd';
        
        detailDiv.innerHTML = `
            <div class="animate__animated animate__fadeIn">
                <span class="badge mb-2" style="background-color: ${color}; font-size: 0.8rem;">${m.tipo_establecimiento}</span>
                <h5 class="fw-bold mb-1">${m.nombre_establecimiento}</h5>
                <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger"></i> ${m.parroquia} • ${m.barrio_sector || ''}</p>
                
                <div class="alert alert-light border small py-2 px-3 mb-2">
                    <div><strong>Propietario:</strong> ${m.nombre_propietario || 'No registrado'}</div>
                    <div><strong>Recepción:</strong> <span class="badge ${m.recepcion_proveedores === 'Recibe propuestas' ? 'bg-success' : 'bg-secondary'}">${m.recepcion_proveedores}</span></div>
                    <div><strong>Refrigeración:</strong> ${m.tiene_refrigeracion ? 'Sí' : 'No cuenta'}</div>
                    <div><strong>Productos:</strong> ${m.productos_lacteos || 'General'}</div>
                </div>

                <div class="d-grid gap-2">
                    <a href="/establecimientos/${m.id}" class="btn btn-primary btn-sm">
                        <i class="bi bi-box-arrow-in-up-right me-1"></i> Ficha Completa
                    </a>
                    ${m.telefono ? `
                        <a href="https://wa.me/593${m.telefono.replace(/^0/, '')}" target="_blank" class="btn btn-success btn-sm">
                            <i class="bi bi-whatsapp me-1"></i> Contactar por WhatsApp
                        </a>
                    ` : ''}
                    <a href="https://www.google.com/maps/dir/?api=1&destination=${m.latitud},${m.longitud}" target="_blank" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-sign-turn-right me-1"></i> Cómo llegar (Google Maps)
                    </a>
                </div>
            </div>
        `;
    }

    // Alternar entre Mapa y Listado
    function cambiarVista(vista) {
        document.getElementById('inputVista').value = vista;
        if (vista === 'mapa') {
            document.getElementById('vistaMapa').style.display = 'block';
            document.getElementById('vistaListado').style.display = 'none';
            setTimeout(() => { map.invalidateSize(); }, 200);
        } else {
            document.getElementById('vistaMapa').style.display = 'none';
            document.getElementById('vistaListado').style.display = 'block';
        }
    }
</script>
@endpush
