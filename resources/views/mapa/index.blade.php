@extends('layouts.app')

@section('title', 'Mapa de Oportunidades Comerciales')

@push('styles')
<style>
    /* ---- PAGE HEADER ---- */
    .map-page-hero {
        background: linear-gradient(135deg, #095c43 0%, #0f7a5a 60%, #1a9970 100%);
        padding: 1.5rem 1.5rem 4.5rem;
        position: relative;
        overflow: hidden;
    }
    .map-page-hero::after {
        content: '';
        position: absolute;
        bottom: -1px; left: 0; right: 0;
        height: 40px;
        background: var(--bg);
        clip-path: ellipse(55% 100% at 50% 100%);
    }
    .map-page-hero .hero-title {
        font-family: 'Poppins', sans-serif;
        font-weight: 800;
        font-size: 1.6rem;
        color: #fff;
        margin-bottom: .3rem;
    }
    .map-page-hero .hero-sub {
        color: rgba(255,255,255,.75);
        font-size: .9rem;
    }
    .hero-pill {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        background: rgba(255,255,255,.18);
        color: #fff;
        border-radius: 20px;
        padding: .3rem .85rem;
        font-size: .78rem;
        font-weight: 600;
        backdrop-filter: blur(4px);
    }

    /* ---- FILTER CARD ---- */
    .filter-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-md);
        padding: 1.1rem 1.25rem .9rem;
        margin-top: -2.5rem;
        position: relative;
        z-index: 10;
    }
    .filter-card .form-control,
    .filter-card .form-select {
        border-radius: 10px;
        border-color: #dde8e3;
        background: #f4f7f6;
        font-size: .86rem;
        height: 2.4rem;
    }
    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(15,122,90,.12);
        background: #fff;
    }
    .input-icon-wrap { position: relative; }
    .input-icon-wrap .bi {
        position: absolute;
        left: .75rem; top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: .95rem;
    }
    .input-icon-wrap input { padding-left: 2rem; }

    .filter-sep {
        height: 1px;
        background: var(--border);
        margin: .75rem 0;
    }

    .vista-toggle .btn {
        border-radius: 8px;
        font-size: .82rem;
        font-weight: 600;
        padding: .35rem .85rem;
        border-color: var(--border);
        color: var(--text-muted);
    }
    .vista-toggle .btn.active,
    .vista-toggle .btn-check:checked + .btn {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }

    /* ---- MAP ---- */
    #mapContainer {
        height: calc(100vh - 230px);
        min-height: 480px;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-md);
        z-index: 1;
    }

    /* ---- SIDE PANEL ---- */
    .side-panel {
        background: #fff;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        height: calc(100vh - 230px);
        min-height: 480px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }
    .side-panel-header {
        padding: .9rem 1rem .6rem;
        border-bottom: 1px solid var(--border);
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 1;
    }
    .side-panel-title {
        font-weight: 700;
        font-size: .95rem;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: .4rem;
    }
    .side-panel-body { padding: .8rem 1rem; flex: 1; }

    .empty-state-side {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        gap: .5rem;
        color: var(--text-muted);
        text-align: center;
        padding: 2rem;
    }
    .empty-state-side .bi { font-size: 3rem; opacity: .25; }

    /* ---- STORE CARDS (Listado) ---- */
    .store-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 1rem;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: transform .2s, box-shadow .2s;
    }
    .store-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary);
    }
    .store-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: .6rem;
    }
    .store-card-name {
        font-weight: 700;
        font-size: .95rem;
        color: var(--text);
        margin-bottom: .15rem;
    }
    .store-card-loc {
        font-size: .78rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: .3rem;
    }
    .store-card-info {
        background: var(--bg);
        border-radius: 8px;
        padding: .6rem .8rem;
        font-size: .8rem;
        flex: 1;
        margin-bottom: .75rem;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: .2rem;
        gap: .4rem;
    }
    .info-label { color: var(--text-muted); }
    .info-value { font-weight: 600; color: var(--text); text-align: right; }
    .store-card-actions { display: flex; flex-direction: column; gap: .4rem; }

    .btn-card-primary {
        background: var(--primary);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: .45rem .8rem;
        font-size: .82rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        text-decoration: none;
        transition: background .18s;
    }
    .btn-card-primary:hover { background: var(--primary-dk); color: #fff; }

    .btn-card-row { display: flex; gap: .4rem; }
    .btn-card-sm {
        flex: 1;
        background: transparent;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        padding: .38rem .6rem;
        font-size: .78rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .3rem;
        text-decoration: none;
        color: var(--text-muted);
        transition: all .18s;
    }
    .btn-card-sm.green { border-color: #bbf7d0; color: #15803d; }
    .btn-card-sm.green:hover { background: #f0fdf4; }
    .btn-card-sm:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-lt); }

    /* ---- MAP POPUP ---- */
    .map-popup {
        min-width: 220px;
        font-family: 'Inter', sans-serif;
        padding: .6rem .8rem .5rem;
    }
    .popup-name { font-weight: 700; font-size: .97rem; margin: .3rem 0 .15rem; }
    .popup-loc { font-size: .78rem; color: var(--text-muted); margin-bottom: .5rem; }
    .popup-actions { display: flex; gap: .4rem; }
    .popup-btn {
        flex: 1;
        padding: .4rem;
        border-radius: 8px;
        font-size: .78rem;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
        display: flex; align-items: center; justify-content: center; gap: .25rem;
    }
    .popup-btn-primary { background: var(--primary); color: #fff; }
    .popup-btn-secondary { background: var(--bg); color: var(--text); border: 1px solid var(--border); }

    /* ---- CUSTOM MARKER ---- */
    .marker-wrap {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .marker-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        color: #fff;
        box-shadow: 0 3px 8px rgba(0,0,0,.3);
        border: 2.5px solid rgba(255,255,255,.8);
    }
    .marker-tail {
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        margin-top: -1px;
    }

    /* ---- EMPTY LISTADO ---- */
    .empty-listado {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem 2rem;
        color: var(--text-muted);
        text-align: center;
    }
    .empty-listado .bi { font-size: 4rem; opacity: .2; margin-bottom: 1rem; }

    /* ---- BADGE RECEPCION ---- */
    .badge-recibe { background: #dcfce7; color: #15803d; font-size: .7rem; font-weight: 600; padding: .25em .6em; border-radius: 20px; }
    .badge-pendiente { background: #f1f5f9; color: #64748b; font-size: .7rem; font-weight: 600; padding: .25em .6em; border-radius: 20px; }

    /* ---- DETALLE LATERAL (panel) ---- */
    .detail-section-title {
        font-size: .78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: var(--text-muted);
        margin: .8rem 0 .35rem;
    }
    .detail-row {
        display: flex;
        justify-content: space-between;
        font-size: .82rem;
        padding: .3rem 0;
        border-bottom: 1px solid #f1f5f1;
        gap: .5rem;
    }
    .detail-row:last-child { border-bottom: none; }
    .detail-label { color: var(--text-muted); }
    .detail-val { font-weight: 600; text-align: right; }

    /* ---- RESULTS PILL ---- */
    .results-pill {
        background: var(--primary-lt);
        color: var(--primary);
        border-radius: 20px;
        padding: .2rem .75rem;
        font-size: .8rem;
        font-weight: 700;
    }
</style>
@endpush

@section('content')

<!-- ===== PAGE HERO ===== -->
<div class="map-page-hero">
    <div class="container-fluid px-3 px-lg-5">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <span class="hero-pill"><i class="bi bi-geo-alt-fill text-warning"></i> Cantón Latacunga</span>
                    <span class="hero-pill"><i class="bi bi-shop"></i> {{ $totalEncontrados }} comercios</span>
                </div>
                <h1 class="hero-title">Mapa de Oportunidades Comerciales</h1>
                <p class="hero-sub">Encuentra tiendas, minimarkets y distribuidores listos para recibir tus productos lácteos artesanales.</p>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-5">

    <!-- ===== FILTER CARD ===== -->
    <div class="filter-card mb-4">
        <form method="GET" action="{{ route('mapa.index') }}" class="row g-2 align-items-end">
            <input type="hidden" name="vista" value="{{ $vista }}" id="inputVista">

            <!-- Búsqueda -->
            <div class="col-12 col-md-4 col-lg-3">
                <div class="input-icon-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" name="q" value="{{ request('q') }}"
                           class="form-control" placeholder="Buscar comercio, sector…">
                </div>
            </div>

            <!-- Parroquia -->
            <div class="col-6 col-md-2">
                <select name="parroquia" class="form-select">
                    <option value="">Todas las parroquias</option>
                    @foreach($parroquias as $p)
                        <option value="{{ $p }}" {{ request('parroquia') == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tipo -->
            <div class="col-6 col-md-2">
                <select name="tipo" class="form-select">
                    <option value="">Todos los tipos</option>
                    @foreach($tipos as $t)
                        <option value="{{ $t }}" {{ request('tipo') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Categoría producto -->
            <div class="col-6 col-md-2">
                <select name="categoria" class="form-select">
                    <option value="">Categoría producto</option>
                    <option value="Leche"       {{ request('categoria') == 'Leche'       ? 'selected' : '' }}>🥛 Leche</option>
                    <option value="Queso"       {{ request('categoria') == 'Queso'       ? 'selected' : '' }}>🧀 Queso</option>
                    <option value="Yogurt"      {{ request('categoria') == 'Yogurt'      ? 'selected' : '' }}>🍶 Yogurt</option>
                    <option value="Mantequilla" {{ request('categoria') == 'Mantequilla' ? 'selected' : '' }}>🧈 Mantequilla</option>
                </select>
            </div>

            <!-- Recepción -->
            <div class="col-6 col-md-2">
                <select name="recepcion" class="form-select">
                    <option value="">Recepción proveedores</option>
                    <option value="si"        {{ request('recepcion') == 'si'        ? 'selected' : '' }}>✅ Reciben propuestas</option>
                    <option value="artesanal" {{ request('recepcion') == 'artesanal' ? 'selected' : '' }}>⭐ Interés artesanales</option>
                </select>
            </div>

            <!-- Acciones -->
            <div class="col-12 col-md-auto d-flex gap-2 ms-auto">
                <button type="submit" class="btn-primary-sig d-flex align-items-center gap-1">
                    <i class="bi bi-funnel-fill"></i> Filtrar
                </button>
                <a href="{{ route('mapa.index') }}" class="btn-ghost-sig d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-counterclockwise"></i> Limpiar
                </a>
            </div>
        </form>

        <div class="filter-sep"></div>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="results-pill"><i class="bi bi-pin-map-fill me-1"></i>{{ $totalEncontrados }} resultados</span>
                @if(request('q') || request('parroquia') || request('tipo') || request('categoria') || request('recepcion'))
                    <span class="badge" style="background:#fef3c7;color:#92400e;border-radius:20px;font-size:.72rem;padding:.25em .7em;">
                        <i class="bi bi-funnel me-1"></i>Filtros activos
                    </span>
                @endif
            </div>

            <!-- Toggle Vista -->
            <div class="vista-toggle btn-group btn-group-sm" role="group">
                <button type="button"
                    class="btn {{ $vista == 'mapa' ? 'active' : '' }}"
                    onclick="cambiarVista('mapa')">
                    <i class="bi bi-map-fill me-1"></i>Mapa
                </button>
                <button type="button"
                    class="btn {{ $vista == 'listado' ? 'active' : '' }}"
                    onclick="cambiarVista('listado')">
                    <i class="bi bi-grid-fill me-1"></i>Listado
                </button>
            </div>
        </div>
    </div>

    <!-- ===== VISTA: MAPA ===== -->
    <div id="vistaMapa" style="{{ $vista == 'mapa' ? '' : 'display:none;' }}">
        <div class="row g-3">
            <div class="col-12 col-lg-9">
                <div id="mapContainer"></div>
            </div>
            <!-- Panel lateral -->
            <div class="col-12 col-lg-3">
                <div class="side-panel">
                    <div class="side-panel-header">
                        <div class="side-panel-title">
                            <i class="bi bi-cursor-fill text-primary-sig"></i>
                            Comercio Seleccionado
                        </div>
                        <div class="text-muted" style="font-size:.75rem;margin-top:.15rem;">Haz clic en un marcador del mapa</div>
                    </div>
                    <div class="side-panel-body" id="quickDetail">
                        <div class="empty-state-side">
                            <i class="bi bi-pin-map"></i>
                            <p class="mb-0" style="font-size:.85rem;">Selecciona un comercio en el mapa para ver su ficha rápida, contacto y condiciones comerciales.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== VISTA: LISTADO ===== -->
    <div id="vistaListado" style="{{ $vista == 'listado' ? '' : 'display:none;' }}">
        <div class="row g-3">
            @forelse($establecimientos as $est)
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="store-card">
                        <div class="store-card-top">
                            <span class="badge-tipo badge-{{ strtolower($est->tipo_establecimiento) }}">
                                {{ $est->tipo_establecimiento }}
                            </span>
                            @if($est->recepcion_proveedores == 'Recibe propuestas')
                                <span class="badge-recibe"><i class="bi bi-check-circle-fill me-1"></i>Recibe propuestas</span>
                            @else
                                <span class="badge-pendiente">Por confirmar</span>
                            @endif
                        </div>

                        <div class="store-card-name">{{ $est->nombre_establecimiento }}</div>
                        <div class="store-card-loc mb-2">
                            <i class="bi bi-geo-alt text-danger"></i>
                            {{ $est->parroquia }}@if($est->barrio_sector) · {{ $est->barrio_sector }}@endif
                        </div>

                        <div class="store-card-info">
                            <div class="info-row">
                                <span class="info-label">Lácteos</span>
                                <span class="info-value">{{ Str::limit($est->productos_lacteos ?? 'Varios', 28) }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Refrigeración</span>
                                <span class="info-value">
                                    @if($est->tiene_refrigeracion)
                                        <i class="bi bi-snow text-info"></i> Sí
                                    @else
                                        No
                                    @endif
                                </span>
                            </div>
                            @if($est->nombre_propietario)
                            <div class="info-row">
                                <span class="info-label">Contacto</span>
                                <span class="info-value">{{ $est->nombre_propietario }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="store-card-actions">
                            <a href="{{ route('establecimientos.show', $est->id) }}" class="btn-card-primary">
                                <i class="bi bi-card-text"></i> Ver Ficha Completa
                            </a>
                            <div class="btn-card-row">
                                @if($est->telefono)
                                    <a href="{{ $est->url_whatsapp }}" target="_blank" class="btn-card-sm green">
                                        <i class="bi bi-whatsapp"></i> WhatsApp
                                    </a>
                                @endif
                                <a href="{{ $est->url_como_llegar }}" target="_blank" class="btn-card-sm">
                                    <i class="bi bi-sign-turn-right"></i> GPS
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-listado">
                        <i class="bi bi-shop-window"></i>
                        <h5 class="fw-bold" style="color:var(--text);">Sin resultados</h5>
                        <p class="mb-3">No se encontraron establecimientos con los filtros seleccionados.</p>
                        <a href="{{ route('mapa.index') }}" class="btn-primary-sig">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Restablecer filtros
                        </a>
                    </div>
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
    const marcadoresData = @json($marcadores);

    // ---- MAPA ----
    const map = L.map('mapContainer').setView([-0.78119, -78.57534], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors • SIG Lácteos Cotopaxi'
    }).addTo(map);

    const colorPorTipo = {
        'Tienda':        '#1d4ed8',
        'Minimarket':    '#15803d',
        'Supermercado':  '#7c3aed',
        'Panaderia':     '#c2410c',
        'Panadería':     '#c2410c',
        'Distribuidor':  '#be185d'
    };
    const iconoPorTipo = {
        'Tienda':        'bi-shop',
        'Minimarket':    'bi-basket-fill',
        'Supermercado':  'bi-cart4',
        'Panaderia':     'bi-cake2-fill',
        'Panadería':     'bi-cake2-fill',
        'Distribuidor':  'bi-truck'
    };

    const markersGroup = L.markerClusterGroup({ maxClusterRadius: 45, spiderfyOnMaxZoom: true });

    marcadoresData.forEach(m => {
        if (!m.latitud || !m.longitud) return;
        const color = colorPorTipo[m.tipo_establecimiento] || '#1d4ed8';
        const icono = iconoPorTipo[m.tipo_establecimiento] || 'bi-shop';

        const customIcon = L.divIcon({
            className: '',
            html: `<div style="display:flex;flex-direction:column;align-items:center;">
                     <div style="width:36px;height:36px;border-radius:50%;background:${color};display:flex;align-items:center;justify-content:center;font-size:15px;color:#fff;box-shadow:0 3px 8px rgba(0,0,0,.35);border:2.5px solid rgba(255,255,255,.85);">
                       <i class="bi ${icono}"></i>
                     </div>
                     <div style="width:0;height:0;border-left:6px solid transparent;border-right:6px solid transparent;border-top:8px solid ${color};margin-top:-1px;filter:drop-shadow(0 2px 2px rgba(0,0,0,.2));"></div>
                   </div>`,
            iconSize: [36, 50],
            iconAnchor: [18, 50]
        });

        const popupHtml = `
            <div class="map-popup">
                <span style="display:inline-block;background:${color};color:#fff;border-radius:20px;padding:.2em .65em;font-size:.72rem;font-weight:700;">${m.tipo_establecimiento}</span>
                <div class="popup-name">${m.nombre_establecimiento}</div>
                <div class="popup-loc"><i class="bi bi-geo-alt text-danger"></i> ${m.parroquia}${m.barrio_sector ? ' · '+m.barrio_sector : ''}</div>
                <div style="font-size:.78rem;color:#6b7c76;margin-bottom:.5rem;">
                    <span><i class="bi bi-box-seam me-1"></i>${m.productos_lacteos || 'Varios lácteos'}</span>
                </div>
                <div class="popup-actions">
                    <a href="/establecimientos/${m.id}" class="popup-btn popup-btn-primary"><i class="bi bi-card-text"></i> Ficha</a>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=${m.latitud},${m.longitud}" target="_blank" class="popup-btn popup-btn-secondary"><i class="bi bi-sign-turn-right"></i> GPS</a>
                </div>
            </div>`;

        const marker = L.marker([m.latitud, m.longitud], { icon: customIcon });
        marker.bindPopup(popupHtml, { maxWidth: 260, className: 'popup-clean' });
        marker.on('click', () => mostrarDetalleLateral(m));
        markersGroup.addLayer(marker);
    });

    map.addLayer(markersGroup);

    // ---- PANEL LATERAL ----
    function mostrarDetalleLateral(m) {
        const color = colorPorTipo[m.tipo_establecimiento] || '#1d4ed8';
        const icono = iconoPorTipo[m.tipo_establecimiento] || 'bi-shop';

        document.getElementById('quickDetail').innerHTML = `
            <div style="animation:fadeIn .3s ease">
                <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.6rem;">
                    <div style="width:38px;height:38px;border-radius:10px;background:${color};display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.1rem;flex-shrink:0;">
                        <i class="bi ${icono}"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:.95rem;line-height:1.2;">${m.nombre_establecimiento}</div>
                        <span style="display:inline-block;background:${color}22;color:${color};border-radius:20px;padding:.1em .55em;font-size:.7rem;font-weight:700;">${m.tipo_establecimiento}</span>
                    </div>
                </div>

                <div class="detail-section-title">Ubicación</div>
                <div class="detail-row"><span class="detail-label">Parroquia</span><span class="detail-val">${m.parroquia}</span></div>
                <div class="detail-row"><span class="detail-label">Sector</span><span class="detail-val">${m.barrio_sector || '—'}</span></div>

                <div class="detail-section-title">Comercial</div>
                <div class="detail-row"><span class="detail-label">Propietario</span><span class="detail-val">${m.nombre_propietario || 'No registrado'}</span></div>
                <div class="detail-row"><span class="detail-label">Recepción</span>
                    <span class="detail-val">
                        ${m.recepcion_proveedores === 'Recibe propuestas'
                            ? '<span style="background:#dcfce7;color:#15803d;border-radius:20px;padding:.15em .55em;font-size:.72rem;font-weight:700;">✅ Recibe</span>'
                            : '<span style="background:#f1f5f9;color:#64748b;border-radius:20px;padding:.15em .55em;font-size:.72rem;font-weight:700;">Por confirmar</span>'}
                    </span>
                </div>
                <div class="detail-row"><span class="detail-label">Refrigeración</span><span class="detail-val">${m.tiene_refrigeracion ? '❄️ Sí' : 'No'}</span></div>
                <div class="detail-row"><span class="detail-label">Productos</span><span class="detail-val" style="font-size:.78rem;">${m.productos_lacteos || 'General'}</span></div>

                <div style="display:flex;flex-direction:column;gap:.4rem;margin-top:1rem;">
                    <a href="/establecimientos/${m.id}" class="btn-card-primary">
                        <i class="bi bi-box-arrow-in-up-right"></i> Ficha Completa
                    </a>
                    ${m.telefono ? `
                    <a href="https://wa.me/593${m.telefono.replace(/^0/, '')}" target="_blank"
                       style="background:#22c55e;color:#fff;border:none;border-radius:8px;padding:.42rem .8rem;font-size:.82rem;font-weight:600;display:flex;align-items:center;justify-content:center;gap:.35rem;text-decoration:none;">
                        <i class="bi bi-whatsapp"></i> Contactar por WhatsApp
                    </a>` : ''}
                    <a href="https://www.google.com/maps/dir/?api=1&destination=${m.latitud},${m.longitud}" target="_blank"
                       style="background:transparent;color:var(--text-muted);border:1.5px solid var(--border);border-radius:8px;padding:.38rem .8rem;font-size:.78rem;font-weight:600;display:flex;align-items:center;justify-content:center;gap:.35rem;text-decoration:none;">
                        <i class="bi bi-sign-turn-right"></i> Cómo llegar
                    </a>
                </div>
            </div>`;
    }

    // ---- TOGGLE VISTA ----
    function cambiarVista(vista) {
        document.getElementById('inputVista').value = vista;
        if (vista === 'mapa') {
            document.getElementById('vistaMapa').style.display = 'block';
            document.getElementById('vistaListado').style.display = 'none';
            setTimeout(() => map.invalidateSize(), 200);
        } else {
            document.getElementById('vistaMapa').style.display = 'none';
            document.getElementById('vistaListado').style.display = 'block';
        }
    }
</script>
@endpush
