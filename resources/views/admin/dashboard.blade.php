@extends('layouts.app')

@section('title', 'Panel de Administración — SIG Lácteos')

@push('styles')
<style>
    .admin-hero {
        background: linear-gradient(135deg, #1e2d40 0%, #2d4057 50%, #1a3a52 100%);
        padding: 1.5rem 1.5rem 4rem;
        position: relative; overflow: hidden;
    }
    .admin-hero::after {
        content:''; position:absolute; bottom:-1px; left:0; right:0;
        height:40px; background:var(--bg);
        clip-path: ellipse(55% 100% at 50% 100%);
    }
    .hero-title { font-family:'Poppins',sans-serif; font-weight:800; font-size:1.5rem; color:#fff; margin-bottom:.3rem; }
    .hero-sub   { color:rgba(255,255,255,.65); font-size:.88rem; }
    .hero-pill  { display:inline-flex;align-items:center;gap:.4rem;background:rgba(255,255,255,.12);color:#fff;border-radius:20px;padding:.25rem .75rem;font-size:.75rem;font-weight:600; }

    /* ---- KPI GRID ---- */
    .kpi-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:1rem; margin-bottom:1.5rem; }
    @media(min-width:768px){ .kpi-grid { grid-template-columns:repeat(4,1fr); } }

    .kpi-box {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 1.1rem 1.2rem;
        box-shadow: var(--shadow-sm);
    }
    .kpi-box-label { font-size: .75rem; color: var(--text-muted); font-weight: 600; margin-bottom: .25rem; }
    .kpi-box-value { font-size: 1.75rem; font-weight: 800; line-height: 1; }
    .kpi-box-sub   { font-size: .72rem; color: var(--text-muted); margin-top: .2rem; }

    /* ---- ADMIN CARD ---- */
    .admin-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .admin-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: .9rem 1.25rem .75rem;
        border-bottom: 1px solid var(--border);
        background: #fafcfb;
    }
    .admin-card-title {
        font-weight: 700;
        font-size: .97rem;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .admin-card-icon {
        width: 30px; height: 30px;
        border-radius: 7px;
        display: flex; align-items: center; justify-content: center;
        font-size: .9rem;
    }

    /* ---- TABLE ---- */
    .admin-table { width: 100%; border-collapse: collapse; }
    .admin-table thead tr { background: #f8fafc; }
    .admin-table thead th {
        font-size: .75rem; font-weight: 700; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: .4px;
        padding: .6rem 1rem;
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }
    .admin-table tbody tr { border-bottom: 1px solid #f0f4f2; transition: background .15s; }
    .admin-table tbody tr:hover { background: #fafcfb; }
    .admin-table tbody td { padding: .7rem 1rem; font-size: .85rem; vertical-align: middle; }
    .admin-table tbody tr:last-child { border-bottom: none; }

    /* ---- ACTION BUTTONS ---- */
    .btn-tbl {
        display: inline-flex; align-items: center; gap: .3rem;
        border-radius: 8px; padding: .32rem .7rem;
        font-weight: 600; font-size: .78rem;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all .15s;
    }
    .btn-tbl-secondary  { background:#f1f5f9;color:var(--text); }
    .btn-tbl-secondary:hover { background:#e2e8f0; color:var(--text); }
    .btn-tbl-primary    { background:var(--primary);color:#fff; }
    .btn-tbl-primary:hover { background:var(--primary-dk);color:#fff; }
    .btn-tbl-success    { background:#22c55e;color:#fff; }
    .btn-tbl-success:hover { background:#16a34a;color:#fff; }
    .btn-tbl-warning    { background:#f59e0b;color:#fff; }

    /* ---- BADGE ---- */
    .badge-error { background:#fef9c3;color:#ca8a04;border-radius:20px;padding:.2em .6em;font-size:.72rem;font-weight:700; }

    /* ---- MODAL ---- */
    .modal-content { border-radius: var(--radius-md); border: none; box-shadow: var(--shadow-lg); }

    /* ---- EMPTY ---- */
    .table-empty { text-align: center; padding: 2.5rem 1rem; color: var(--text-muted); }
    .table-empty .bi { font-size: 2.5rem; opacity: .2; display: block; margin-bottom: .5rem; }
</style>
@endpush

@section('content')

<!-- ===== HERO ===== -->
<div class="admin-hero">
    <div class="container px-3 px-lg-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <span class="hero-pill"><i class="bi bi-shield-lock-fill"></i> Panel Admin</span>
                    <span class="hero-pill"><i class="bi bi-geo-fill"></i> Cantón Latacunga</span>
                </div>
                <h1 class="hero-title">Panel de Administración y Control</h1>
                <p class="hero-sub">Gestión de calidad geográfica, verificación de comercios y auditoría del SIG Lácteos.</p>
            </div>
            <button class="btn" style="background:rgba(255,255,255,.12);color:#fff;border:1.5px solid rgba(255,255,255,.3);border-radius:10px;padding:.5rem 1rem;font-weight:600;font-size:.85rem;"
                    data-bs-toggle="modal" data-bs-target="#modalDuplicados">
                <i class="bi bi-copy me-1"></i> Detector de Duplicados
            </button>
        </div>
    </div>
</div>

<div class="container px-3 px-lg-4">

    <!-- ===== KPIs ===== -->
    <div class="kpi-grid" style="margin-top:-1.5rem; position:relative; z-index:10;">
        <div class="kpi-box">
            <div class="kpi-box-label">Establecimientos Totales</div>
            <div class="kpi-box-value" style="color:var(--primary);">{{ $metricas['total_establecimientos'] }}</div>
            <div class="kpi-box-sub">100% georreferenciados</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-box-label">Comercios Verificados</div>
            <div class="kpi-box-value" style="color:#15803d;">{{ $metricas['verificados'] }}</div>
            <div class="kpi-box-sub">Confirmados en campo</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-box-label">Comercios Reportados</div>
            <div class="kpi-box-value" style="color:#dc2626;">{{ $metricas['reportados'] }}</div>
            <div class="kpi-box-sub">Por usuarios/emprendedores</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-box-label">Reportes Pendientes</div>
            <div class="kpi-box-value" style="color:#d97706;">{{ $metricas['reportes_pendientes'] }}</div>
            <div class="kpi-box-sub">Requieren revisión</div>
        </div>
    </div>

    <!-- ===== BARRA DE CALIDAD ===== -->
    @php
        $pct = $metricas['total_establecimientos'] > 0
            ? round(($metricas['verificados'] / $metricas['total_establecimientos']) * 100)
            : 0;
    @endphp
    <div class="admin-card mb-4">
        <div class="admin-card-header">
            <div class="admin-card-title">
                <div class="admin-card-icon" style="background:#dcfce7;color:#15803d;"><i class="bi bi-graph-up-arrow"></i></div>
                Calidad del Dato Geográfico
            </div>
            <span style="font-size:.85rem;font-weight:700;color:#15803d;">{{ $pct }}% verificado</span>
        </div>
        <div style="padding:.85rem 1.25rem;">
            <div style="background:#f1f5f9;border-radius:4px;height:10px;overflow:hidden;">
                <div style="height:100%;width:{{ $pct }}%;background:linear-gradient(90deg,#15803d,#22c55e);border-radius:4px;transition:width .6s ease;"></div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:.74rem;color:var(--text-muted);margin-top:.4rem;">
                <span>{{ $metricas['verificados'] }} verificados</span>
                <span>{{ $metricas['total_establecimientos'] - $metricas['verificados'] }} pendientes</span>
            </div>
        </div>
    </div>

    <!-- ===== REPORTES PENDIENTES ===== -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">
                <div class="admin-card-icon" style="background:#fce7f3;color:#be185d;"><i class="bi bi-flag-fill"></i></div>
                Reportes de Información Incorrecta
            </div>
            <span style="background:#fce7f3;color:#be185d;border-radius:20px;padding:.2em .75em;font-size:.78rem;font-weight:700;">
                {{ $reportes->total() }} pendiente{{ $reportes->total() != 1 ? 's' : '' }}
            </span>
        </div>

        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Establecimiento</th>
                        <th>Parroquia</th>
                        <th>Motivo</th>
                        <th>Detalle</th>
                        <th>Fecha</th>
                        <th style="text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportes as $rep)
                        <tr>
                            <td>
                                <div style="font-weight:700;">{{ $rep->establecimiento->nombre_establecimiento }}</div>
                                <div style="font-size:.72rem;color:var(--text-muted);">#{{ $rep->establecimiento->id }} · {{ $rep->establecimiento->tipo_establecimiento }}</div>
                            </td>
                            <td>{{ $rep->establecimiento->parroquia }}</td>
                            <td><span class="badge-error">{{ $rep->tipo_error }}</span></td>
                            <td style="max-width:200px;">
                                <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;font-size:.82rem;">{{ $rep->descripcion }}</span>
                            </td>
                            <td style="font-size:.8rem;white-space:nowrap;">{{ $rep->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div style="display:flex;justify-content:flex-end;gap:.4rem;flex-wrap:wrap;">
                                    <a href="{{ route('establecimientos.show', $rep->establecimiento_id) }}" target="_blank"
                                       class="btn-tbl btn-tbl-secondary">Ver</a>
                                    <button class="btn-tbl btn-tbl-primary"
                                            data-bs-toggle="modal" data-bs-target="#modalCorregir{{ $rep->establecimiento_id }}">
                                        <i class="bi bi-geo-alt"></i> GPS
                                    </button>
                                    <form method="POST" action="{{ route('admin.resolver-reporte', $rep->id) }}" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="estado" value="corregido">
                                        <button type="submit" class="btn-tbl btn-tbl-success">
                                            <i class="bi bi-check-lg"></i> Resolver
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-empty">
                                <i class="bi bi-check2-circle" style="color:#22c55e;"></i>
                                <strong style="color:var(--text);font-size:.95rem;">¡Todo en orden!</strong>
                                <p style="margin:.3rem 0 0;font-size:.82rem;">No hay reportes pendientes de revisión.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODALES GPS -->
    @foreach($reportes as $rep)
        <div class="modal fade" id="modalCorregir{{ $rep->establecimiento_id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.corregir-ubicacion', $rep->establecimiento_id) }}">
                        @csrf
                        <div class="modal-header">
                            <div class="fw-bold">📍 Corregir Coordenadas GPS</div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:1rem;">
                                Corrige la geolocalización de <strong>{{ $rep->establecimiento->nombre_establecimiento }}</strong>.
                            </p>
                            <div class="row g-3">
                                <div class="col-6">
                                    <label style="font-size:.78rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:.3rem;">Latitud</label>
                                    <input type="text" name="latitud" class="form-control" style="border-radius:10px;"
                                           value="{{ $rep->establecimiento->latitud }}" required>
                                </div>
                                <div class="col-6">
                                    <label style="font-size:.78rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:.3rem;">Longitud</label>
                                    <input type="text" name="longitud" class="form-control" style="border-radius:10px;"
                                           value="{{ $rep->establecimiento->longitud }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn-tbl btn-tbl-primary" style="font-size:.88rem;padding:.5rem 1.1rem;">
                                <i class="bi bi-check-circle me-1"></i>Guardar Ubicación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

</div>

<!-- ===== MODAL: DETECTOR DUPLICADOS ===== -->
<div class="modal fade" id="modalDuplicados" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="fw-bold fs-6"><i class="bi bi-copy me-1"></i>Detector Inteligente de Duplicados</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p style="font-size:.85rem;color:var(--text-muted);">Ingresa el nombre o teléfono para verificar si ya fue registrado en la base de datos.</p>
                <div class="d-flex gap-2 mb-3">
                    <input type="text" id="busqDuplicado" class="form-control" style="border-radius:10px;"
                           placeholder="Ej: Tienda San José, 0982000121…">
                    <button class="btn-tbl btn-tbl-primary" style="font-size:.88rem;padding:.5rem 1.1rem;white-space:nowrap;"
                            onclick="buscarDuplicados()">
                        <i class="bi bi-search me-1"></i> Comprobar
                    </button>
                </div>
                <div id="resultadoDuplicados" style="font-size:.85rem;">
                    <div style="text-align:center;color:var(--text-muted);padding:1.5rem;">
                        <i class="bi bi-search" style="font-size:2rem;opacity:.2;display:block;margin-bottom:.5rem;"></i>
                        Ingresa un término para buscar coincidencias.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function buscarDuplicados() {
        const val = document.getElementById('busqDuplicado').value;
        if (!val) return;

        const resDiv = document.getElementById('resultadoDuplicados');
        resDiv.innerHTML = `<div style="text-align:center;padding:1rem;">
            <div class="spinner-border spinner-border-sm" style="color:var(--primary);"></div>
            <span style="margin-left:.5rem;color:var(--text-muted);">Comprobando base de datos…</span>
        </div>`;

        fetch('/establecimientos/detectar-duplicados?nombre=' + encodeURIComponent(val) + '&telefono=' + encodeURIComponent(val), {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.hay_duplicados) {
                let html = `<div style="background:#fef9c3;border:1px solid #fde68a;border-radius:8px;padding:.6rem 1rem;margin-bottom:.75rem;font-size:.85rem;">
                    ⚠️ <strong>Atención:</strong> Se encontraron posibles comercios duplicados:
                </div><div style="display:flex;flex-direction:column;gap:.4rem;">`;
                data.establecimientos.forEach(e => {
                    html += `<div style="display:flex;justify-content:space-between;align-items:center;background:var(--bg);border-radius:8px;padding:.5rem .9rem;border:1px solid var(--border);">
                        <div>
                            <strong>${e.nombre_establecimiento}</strong>
                            <span style="color:var(--text-muted);margin-left:.5rem;font-size:.78rem;">${e.tipo_establecimiento} · ${e.parroquia}</span>
                        </div>
                        <a href="/establecimientos/${e.id}" target="_blank" class="btn-tbl btn-tbl-primary" style="font-size:.78rem;">Ver</a>
                    </div>`;
                });
                html += '</div>';
                resDiv.innerHTML = html;
            } else {
                resDiv.innerHTML = `<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:.75rem 1rem;font-size:.85rem;">
                    ✅ No se detectaron duplicados con ese nombre o teléfono. El registro es único.
                </div>`;
            }
        })
        .catch(() => {
            resDiv.innerHTML = `<div style="background:#fff5f5;border:1px solid #fed7d7;border-radius:8px;padding:.75rem 1rem;font-size:.85rem;color:#dc2626;">
                ❌ Error al consultar el servicio de duplicados.
            </div>`;
        });
    }
</script>
@endpush
