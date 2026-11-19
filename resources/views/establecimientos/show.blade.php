@extends('layouts.app')

@section('title', $establecimiento->nombre_establecimiento . ' — Ficha Comercial')

@push('styles')
<style>
    /* ---- HERO FICHA ---- */
    .ficha-hero {
        background: linear-gradient(135deg, #095c43 0%, #0f7a5a 60%, #1a9970 100%);
        padding: 1.5rem 1.5rem 4.5rem;
        position: relative; overflow: hidden;
    }
    .ficha-hero::after {
        content:''; position:absolute; bottom:-1px; left:0; right:0;
        height:40px; background:var(--bg);
        clip-path: ellipse(55% 100% at 50% 100%);
    }
    .ficha-breadcrumb { display:flex; gap:.5rem; align-items:center; margin-bottom:1rem; }
    .ficha-breadcrumb a { color:rgba(255,255,255,.65); font-size:.8rem; text-decoration:none; transition:color .15s; }
    .ficha-breadcrumb a:hover { color:#fff; }
    .ficha-breadcrumb span { color:rgba(255,255,255,.4); font-size:.8rem; }
    .ficha-breadcrumb .current { color:rgba(255,255,255,.9); font-size:.8rem; font-weight:600; }
    .ficha-title { font-family:'Poppins',sans-serif; font-weight:800; font-size:1.75rem; color:#fff; margin-bottom:.3rem; line-height:1.2; }
    .ficha-addr { color:rgba(255,255,255,.75); font-size:.9rem; display:flex; align-items:center; gap:.35rem; }

    /* ---- CONTENT CARDS ---- */
    .info-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        margin-bottom: 1.25rem;
        overflow: hidden;
    }
    .info-card-header {
        padding: .9rem 1.25rem .7rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: .5rem;
        background: #fafcfb;
    }
    .info-card-icon {
        width: 32px; height: 32px;
        border-radius: 8px;
        background: var(--primary-lt);
        color: var(--primary);
        display: flex; align-items: center; justify-content: center;
        font-size: .95rem;
    }
    .info-card-title { font-weight: 700; font-size: .97rem; color: var(--text); }
    .info-card-body { padding: 1rem 1.25rem; }

    /* ---- ACTION BAR ---- */
    .action-bar {
        display: flex;
        flex-wrap: wrap;
        gap: .6rem;
        padding: .9rem 1.25rem;
        background: var(--primary-lt);
        border-bottom: 1px solid var(--border);
    }

    /* ---- INFO TABLE ---- */
    .info-table { width: 100%; border-collapse: collapse; }
    .info-table tr { border-bottom: 1px solid #f0f4f2; }
    .info-table tr:last-child { border-bottom: none; }
    .info-table th {
        font-size: .78rem; font-weight: 700; color: var(--text-muted);
        padding: .6rem .75rem .6rem 0;
        text-align: left;
        width: 42%;
        vertical-align: top;
    }
    .info-table td {
        font-size: .85rem; font-weight: 500; color: var(--text);
        padding: .6rem 0 .6rem .4rem;
        vertical-align: top;
    }

    /* ---- DEMAND GRID ---- */
    .demand-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; }
    .demand-box {
        background: var(--bg);
        border-radius: 10px;
        padding: .7rem .9rem;
    }
    .demand-label { font-size: .72rem; color: var(--text-muted); font-weight: 600; margin-bottom: .2rem; text-transform: uppercase; letter-spacing: .3px; }
    .demand-val { font-size: .9rem; font-weight: 700; color: var(--text); }

    /* ---- CONTACT CARD ---- */
    .contact-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: .5rem;
        padding: .55rem 0;
        border-bottom: 1px solid #f0f4f2;
        font-size: .85rem;
    }
    .contact-row:last-child { border-bottom: none; }
    .contact-label { color: var(--text-muted); font-size: .75rem; font-weight: 600; white-space: nowrap; }
    .contact-val   { font-weight: 600; color: var(--text); text-align: right; }

    /* ---- MAP ---- */
    #miniMap { height: 220px; border-radius: 10px; }

    /* ---- STATUS BADGE ---- */
    .status-verified { background:#dcfce7; color:#15803d; border-radius:20px; padding:.3em .9em; font-size:.78rem; font-weight:700; }
    .status-pending  { background:#fef9c3; color:#ca8a04; border-radius:20px; padding:.3em .9em; font-size:.78rem; font-weight:700; }

    /* ---- BUTTONS ---- */
    .btn-action {
        display: inline-flex; align-items: center; gap: .4rem;
        border-radius: 10px; padding: .5rem 1rem;
        font-weight: 600; font-size: .85rem;
        text-decoration: none;
        transition: all .18s;
    }
    .btn-action-primary  { background: var(--primary); color: #fff; border: none; }
    .btn-action-primary:hover { background: var(--primary-dk); color: #fff; }
    .btn-action-success  { background: #22c55e; color: #fff; border: none; }
    .btn-action-success:hover { background: #16a34a; color: #fff; }
    .btn-action-dark     { background: var(--text); color: #fff; border: none; }
    .btn-action-dark:hover { background: #0f1f1a; color: #fff; }
    .btn-action-outline  { background: transparent; color: var(--text-muted); border: 1.5px solid var(--border); }
    .btn-action-outline:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-lt); }
    .btn-action-danger   { background: transparent; color: var(--danger); border: 1.5px solid #fed7d7; }
    .btn-action-danger:hover { background: #fff5f5; }
    .btn-action-send     { background: var(--accent); color: var(--text); border: none; }
    .btn-action-send:hover { filter: brightness(1.08); }
</style>
@endpush

@section('content')

<!-- ===== HERO ===== -->
<div class="ficha-hero">
    <div class="container px-3 px-lg-4">
        <div class="ficha-breadcrumb">
            <a href="{{ route('mapa.index') }}"><i class="bi bi-map me-1"></i>Mapa</a>
            <span>/</span>
            <a href="{{ route('mapa.index', ['parroquia' => $establecimiento->parroquia]) }}">{{ $establecimiento->parroquia }}</a>
            <span>/</span>
            <span class="current">{{ $establecimiento->nombre_establecimiento }}</span>
        </div>

        <div class="d-flex flex-wrap gap-2 mb-2">
            <span class="badge-tipo badge-{{ strtolower($establecimiento->tipo_establecimiento) }}"
                  style="font-size:.8rem;padding:.3em .9em;border-radius:20px;font-weight:700;">
                {{ $establecimiento->tipo_establecimiento }}
            </span>
            @if($establecimiento->verificado)
                <span class="status-verified"><i class="bi bi-patch-check-fill me-1"></i>Verificado</span>
            @else
                <span class="status-pending"><i class="bi bi-clock me-1"></i>Pendiente de verificación</span>
            @endif
        </div>

        <h1 class="ficha-title">{{ $establecimiento->nombre_establecimiento }}</h1>
        <p class="ficha-addr">
            <i class="bi bi-geo-alt-fill text-warning"></i>
            {{ $establecimiento->direccion_completa ?? $establecimiento->barrio_sector }},
            {{ $establecimiento->parroquia }}, {{ $establecimiento->canton }} ({{ $establecimiento->provincia }})
        </p>
    </div>
</div>

<div class="container px-3 px-lg-4">
    <div class="row g-4">

        <!-- ===== COLUMNA PRINCIPAL ===== -->
        <div class="col-12 col-lg-8">

            <!-- ACCIÓN RÁPIDA BAR -->
            <div class="info-card">
                <div class="action-bar">
                    @if(!$oportunidadExistente)
                        <form method="POST" action="{{ route('establecimientos.guardar-oportunidad', $establecimiento->id) }}">
                            @csrf
                            <button type="submit" class="btn-action btn-action-outline">
                                <i class="bi bi-bookmark-plus-fill"></i> Guardar Oportunidad
                            </button>
                        </form>
                    @else
                        <a href="{{ route('oportunidades.index') }}" class="btn-action btn-action-primary">
                            <i class="bi bi-check-all"></i> En Mis Oportunidades ({{ $oportunidadExistente->estado_label }})
                        </a>
                    @endif

                    @if($establecimiento->telefono)
                        <a href="{{ $establecimiento->url_whatsapp }}" target="_blank" class="btn-action btn-action-success">
                            <i class="bi bi-whatsapp"></i> WhatsApp
                        </a>
                        <a href="tel:{{ $establecimiento->telefono }}" class="btn-action btn-action-dark">
                            <i class="bi bi-telephone-fill"></i> Llamar
                        </a>
                    @endif

                    <button type="button" class="btn-action btn-action-send" data-bs-toggle="modal" data-bs-target="#modalPropuesta">
                        <i class="bi bi-send-fill"></i> Enviar Propuesta
                    </button>

                    <a href="{{ $establecimiento->url_como_llegar }}" target="_blank" class="btn-action btn-action-outline">
                        <i class="bi bi-sign-turn-right"></i> GPS
                    </a>

                    <button type="button" class="btn-action btn-action-danger ms-auto" data-bs-toggle="modal" data-bs-target="#modalReportar">
                        <i class="bi bi-flag"></i> Reportar error
                    </button>
                </div>
            </div>

            <!-- CONDICIONES COMERCIALES -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon"><i class="bi bi-clipboard2-data-fill"></i></div>
                    <div class="info-card-title">Condiciones de Comercialización</div>
                </div>
                <div class="info-card-body">
                    <table class="info-table">
                        <tr>
                            <th>Recepción de Proveedores</th>
                            <td>
                                @if($establecimiento->recepcion_proveedores == 'Recibe propuestas')
                                    <span style="background:#dcfce7;color:#15803d;border-radius:20px;padding:.2em .65em;font-size:.78rem;font-weight:700;">✅ Recibe propuestas</span>
                                @else
                                    <span style="background:#f1f5f9;color:#64748b;border-radius:20px;padding:.2em .65em;font-size:.78rem;font-weight:700;">⏳ Por confirmar</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Interés en Productos Artesanales</th>
                            <td>{{ $establecimiento->interes_artesanales ? '✅ Sí, interesado en productores locales' : '❌ No prioritario' }}</td>
                        </tr>
                        <tr>
                            <th>Horario Atención a Proveedores</th>
                            <td>{{ $establecimiento->horario_proveedores ?? '08:00 a 12:00 (Lunes a Viernes)' }}</td>
                        </tr>
                        <tr>
                            <th>Horario al Público</th>
                            <td>{{ $establecimiento->horario_atencion ?? '08:00 a 20:00' }}</td>
                        </tr>
                        <tr>
                            <th>Requisitos de Ingreso</th>
                            <td>{{ $establecimiento->requisitos_ingreso ?? 'Muestra previa y ficha técnica' }}</td>
                        </tr>
                        <tr>
                            <th>Condiciones de Compra</th>
                            <td>{{ $establecimiento->condiciones_compra ?? 'Pago contra entrega' }}</td>
                        </tr>
                        <tr>
                            <th>Infraestructura de Frío</th>
                            <td>
                                @if($establecimiento->tiene_refrigeracion)
                                    <span>❄️ Sí — {{ $establecimiento->capacidad_refrigeracion ?? 'disponible' }}</span>
                                @else
                                    <span>❌ No cuenta con refrigeración</span>
                                @endif
                            </td>
                        </tr>
                        @if(strtolower($establecimiento->tipo_establecimiento) === 'supermercado')
                        <tr>
                            <th style="color:var(--danger);">Toma de Decisiones</th>
                            <td>
                                <strong>{{ $establecimiento->tipo_decision === 'oficina_central' ? '🏢 Oficina Central / Matriz' : '🏪 En esta Sucursal' }}</strong>
                                <div style="font-size:.78rem;color:var(--text-muted);">Contacto: {{ $establecimiento->contacto_decision ?? 'Gerencia de Compras' }}</div>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- LÍNEAS Y DEMANDA -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon"><i class="bi bi-cart3"></i></div>
                    <div class="info-card-title">Líneas y Demanda Actual</div>
                </div>
                <div class="info-card-body">
                    <div class="demand-grid">
                        <div class="demand-box">
                            <div class="demand-label">Categorías lácteas</div>
                            <div class="demand-val">{{ $establecimiento->productos_lacteos ?: 'Leche, Queso, Yogurt, Mantequilla' }}</div>
                        </div>
                        <div class="demand-box" style="border:1.5px solid #bbf7d0;">
                            <div class="demand-label" style="color:#15803d;">Mayor Demanda</div>
                            <div class="demand-val" style="color:#15803d;">{{ $establecimiento->producto_mayor_demanda ?: 'Leche entera' }}</div>
                        </div>
                        <div class="demand-box">
                            <div class="demand-label">Marcas actuales</div>
                            <div class="demand-val" style="font-size:.82rem;">{{ $establecimiento->marcas_vende ?: 'Toni, Parmalat, Vita' }}</div>
                        </div>
                        <div class="demand-box">
                            <div class="demand-label">Frecuencia / Volumen</div>
                            <div class="demand-val" style="font-size:.82rem;">{{ $establecimiento->frecuencia_abastecimiento ?? 'Semanal' }} ({{ $establecimiento->volumen_compra_semanal ?? 'N/D' }})</div>
                        </div>
                    </div>

                    @if($establecimiento->observaciones)
                        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-left:4px solid #1a7fc1;border-radius:8px;padding:.75rem 1rem;font-size:.85rem;margin-top:1rem;">
                            <i class="bi bi-info-circle-fill" style="color:#1a7fc1;"></i>
                            <strong style="color:#1e40af;"> Observaciones:</strong> {{ $establecimiento->observaciones }}
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- ===== COLUMNA LATERAL ===== -->
        <div class="col-12 col-lg-4">

            <!-- CONTACTO -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon"><i class="bi bi-person-lines-fill"></i></div>
                    <div class="info-card-title">Contacto Comercial</div>
                </div>
                <div class="info-card-body">
                    <div class="contact-row">
                        <span class="contact-label">Responsable</span>
                        <span class="contact-val">{{ $establecimiento->nombre_propietario ?: 'Administrador del Local' }}</span>
                    </div>
                    <div class="contact-row">
                        <span class="contact-label">Teléfono / WhatsApp</span>
                        <span class="contact-val">{{ $establecimiento->telefono ?: 'No disponible' }}</span>
                    </div>
                    @if($establecimiento->correo_electronico)
                    <div class="contact-row">
                        <span class="contact-label">Correo</span>
                        <span class="contact-val" style="font-size:.8rem;">{{ $establecimiento->correo_electronico }}</span>
                    </div>
                    @endif
                    <div class="contact-row">
                        <span class="contact-label">Tipo de Local</span>
                        <span class="contact-val">{{ $establecimiento->tipo_local ?: 'Propio' }}</span>
                    </div>
                </div>
            </div>

            <!-- MAPA -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-card-icon" style="background:#fce7f3;color:#be185d;"><i class="bi bi-geo-alt-fill"></i></div>
                    <div class="info-card-title">Ubicación Exacta</div>
                </div>
                <div class="info-card-body p-2">
                    <div id="miniMap"></div>
                    <div style="text-align:center;font-size:.72rem;color:var(--text-muted);padding:.5rem;">
                        {{ $establecimiento->latitud }}, {{ $establecimiento->longitud }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ===== MODAL: PROPUESTA ===== -->
<div class="modal fade" id="modalPropuesta" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('propuestas.store') }}">
                @csrf
                <input type="hidden" name="establecimiento_id" value="{{ $establecimiento->id }}">
                <div class="modal-header">
                    <div>
                        <div class="modal-title"><i class="bi bi-send-fill text-warning me-1"></i>Enviar Propuesta Comercial</div>
                        <small class="text-muted">A: {{ $establecimiento->nombre_establecimiento }}</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label style="font-size:.8rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;" class="mb-2 d-block">Productos de tu catálogo</label>
                        @forelse($productos as $prod)
                            <div style="background:var(--bg);border-radius:8px;padding:.5rem .75rem;margin-bottom:.4rem;border:1px solid var(--border);display:flex;align-items:center;gap:.5rem;">
                                <input class="form-check-input m-0" type="checkbox" name="productos_seleccionados[]" value="{{ $prod->id }}" id="p{{ $prod->id }}" checked>
                                <label for="p{{ $prod->id }}" style="font-size:.85rem;cursor:pointer;flex:1;">
                                    <strong>{{ $prod->nombre }}</strong> ({{ $prod->presentacion }}) — <span style="color:var(--success);">${{ number_format($prod->precio_comercio, 2) }}</span>
                                </label>
                            </div>
                        @empty
                            <div style="background:#fef9c3;border:1px solid #fde68a;border-radius:8px;padding:.6rem .9rem;font-size:.82rem;">
                                ⚠️ No tienes productos registrados. <a href="{{ route('productos.create') }}">Registrar uno</a>.
                            </div>
                        @endforelse
                    </div>

                    <div class="mb-2">
                        <label style="font-size:.8rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;" class="mb-2 d-block">Mensaje de presentación</label>
                        <textarea name="mensaje" class="form-control" rows="4" required
                                  placeholder="Estimado/a, somos productores locales del Cotopaxi…"
                                  style="border-radius:10px;border-color:var(--border);font-size:.87rem;">Estimado/a {{ $establecimiento->nombre_propietario ?: 'administrador' }}, nos ponemos en contacto desde SIG Lácteos para presentarle nuestra línea de lácteos artesanales del Cantón Latacunga, con excelente margen comercial y entrega puntual en su local.</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-action btn-action-send">
                        <i class="bi bi-send-fill"></i> Enviar Propuesta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== MODAL: REPORTAR ===== -->
<div class="modal fade" id="modalReportar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('establecimientos.reportar', $establecimiento->id) }}">
                @csrf
                <div class="modal-header" style="background:#fff5f5;">
                    <div class="modal-title" style="color:var(--danger);"><i class="bi bi-flag-fill me-1"></i>Reportar Información Incorrecta</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label style="font-size:.8rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;" class="mb-2 d-block">Tipo de error</label>
                        <select name="tipo_error" class="form-select" style="border-radius:10px;font-size:.88rem;" required>
                            <option value="Telefono incorrecto o fuera de servicio">📞 Teléfono incorrecto o fuera de servicio</option>
                            <option value="Negocio cerrado definitivamente">🔒 Negocio cerrado definitivamente</option>
                            <option value="Ya no comercializa productos lácteos">🧴 Ya no comercializa productos lácteos</option>
                            <option value="Ubicación GPS en el mapa incorrecta">📍 Ubicación GPS incorrecta</option>
                            <option value="Cambio de propietario o nombre">👤 Cambio de propietario o nombre</option>
                            <option value="Otro">⚠️ Otro motivo</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label style="font-size:.8rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;" class="mb-2 d-block">Detalle de la corrección</label>
                        <textarea name="descripcion" class="form-control" rows="3" required
                                  placeholder="Describe la información correcta o los detalles…"
                                  style="border-radius:10px;font-size:.88rem;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger" style="border-radius:10px;">Enviar Reporte</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const lat = {{ $establecimiento->latitud }};
    const lng = {{ $establecimiento->longitud }};

    const miniMap = L.map('miniMap').setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(miniMap);

    const icon = L.divIcon({
        className: '',
        html: `<div style="width:36px;height:36px;border-radius:50%;background:#0f7a5a;display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;border:3px solid #fff;box-shadow:0 3px 8px rgba(0,0,0,.35);">
                 <i class="bi bi-shop-fill"></i>
               </div>`,
        iconSize: [36, 36],
        iconAnchor: [18, 36]
    });

    L.marker([lat, lng], { icon })
        .addTo(miniMap)
        .bindPopup(`<strong>{{ $establecimiento->nombre_establecimiento }}</strong><br><small>{{ $establecimiento->direccion_completa }}</small>`)
        .openPopup();
</script>
@endpush
