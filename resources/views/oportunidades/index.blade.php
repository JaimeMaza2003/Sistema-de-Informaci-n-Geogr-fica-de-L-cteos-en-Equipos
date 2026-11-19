@extends('layouts.app')

@section('title', 'Mis Oportunidades Comerciales')

@push('styles')
<style>
    .ops-hero {
        background: linear-gradient(135deg, #095c43 0%, #0f7a5a 60%, #1a9970 100%);
        padding: 1.5rem 1.5rem 4rem;
        position: relative;
        overflow: hidden;
    }
    .ops-hero::after {
        content: ''; position: absolute; bottom: -1px; left: 0; right: 0;
        height: 40px; background: var(--bg);
        clip-path: ellipse(55% 100% at 50% 100%);
    }
    .hero-title { font-family:'Poppins',sans-serif; font-weight:800; font-size:1.5rem; color:#fff; margin-bottom:.3rem; }
    .hero-sub   { color:rgba(255,255,255,.75); font-size:.88rem; }
    .hero-pill  { display:inline-flex;align-items:center;gap:.4rem;background:rgba(255,255,255,.18);color:#fff;border-radius:20px;padding:.25rem .75rem;font-size:.75rem;font-weight:600; }

    /* ---- STAT CARDS ---- */
    .stat-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:1rem; margin-bottom:1.5rem; }
    @media(min-width:768px){ .stat-grid { grid-template-columns:repeat(4,1fr); } }

    .kpi-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 1rem 1.1rem;
        display: flex;
        align-items: center;
        gap: .85rem;
        box-shadow: var(--shadow-sm);
    }
    .kpi-icon {
        width: 46px; height: 46px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; flex-shrink: 0;
    }
    .kpi-label { font-size: .75rem; color: var(--text-muted); font-weight: 500; }
    .kpi-value { font-size: 1.5rem; font-weight: 800; line-height: 1; color: var(--text); }

    /* ---- KANBAN ---- */
    .kanban-scroll {
        display: flex;
        overflow-x: auto;
        gap: 1rem;
        padding-bottom: 1.5rem;
    }
    .kanban-scroll::-webkit-scrollbar { height: 5px; }
    .kanban-scroll::-webkit-scrollbar-thumb { background: #c1d4cc; border-radius: 4px; }

    .kanban-col {
        background: #f4f7f6;
        border-radius: var(--radius-md);
        padding: 1rem;
        min-width: 268px;
        max-width: 285px;
        flex-shrink: 0;
        border: 1px solid var(--border);
    }
    .kanban-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: .85rem;
        padding-bottom: .65rem;
        border-bottom: 2px solid;
    }
    .kanban-header-title {
        font-weight: 700;
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .kanban-count {
        width: 24px; height: 24px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .72rem; font-weight: 800;
        color: #fff;
    }

    .kanban-card {
        background: #fff;
        border-radius: 10px;
        padding: .9rem;
        margin-bottom: .7rem;
        border: 1px solid var(--border);
        cursor: pointer;
        transition: transform .15s, box-shadow .15s, border-color .15s;
    }
    .kanban-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: #c1d4cc;
    }
    .kanban-card-name {
        font-weight: 700;
        font-size: .88rem;
        color: var(--text);
        margin-bottom: .25rem;
        line-height: 1.3;
    }
    .kanban-card-loc {
        font-size: .75rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: .25rem;
        margin-bottom: .4rem;
    }
    .kanban-card-product {
        background: var(--bg);
        border-radius: 6px;
        padding: .2rem .55rem;
        font-size: .72rem;
        color: var(--text-muted);
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        margin-bottom: .4rem;
    }
    .kanban-card-note {
        font-size: .75rem;
        color: var(--text-muted);
        font-style: italic;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: .4rem;
    }
    .kanban-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: .45rem;
        border-top: 1px solid var(--border);
        font-size: .74rem;
    }
    .kanban-date { color: var(--sky); font-weight: 600; }
    .kanban-amount { color: var(--success); font-weight: 700; }

    .kanban-empty {
        text-align: center;
        padding: 1.5rem .5rem;
        color: var(--text-muted);
        font-size: .78rem;
    }
    .kanban-empty .bi { font-size: 1.8rem; opacity: .2; display: block; margin-bottom: .4rem; }

    /* ---- MODAL ---- */
    .modal-content { border-radius: var(--radius-md); border: none; box-shadow: var(--shadow-lg); }
    .modal-header { border-bottom: 1px solid var(--border); padding: 1.1rem 1.25rem .9rem; }
    .modal-title { font-weight: 700; font-size: 1rem; }
    .modal-body { padding: 1.1rem 1.25rem; }
    .modal-footer { border-top: 1px solid var(--border); padding: .8rem 1.25rem; }
    .form-label-sig { font-size: .8rem; font-weight: 700; color: var(--text-muted); margin-bottom: .3rem; text-transform: uppercase; letter-spacing: .4px; }
    .form-control-sig, .form-select-sig {
        border-radius: 10px;
        border-color: var(--border);
        font-size: .88rem;
        background: var(--bg);
    }
    .form-control-sig:focus, .form-select-sig:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(15,122,90,.12);
        background: #fff;
    }

    /* ---- SECTION HEADER ---- */
    .section-h {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    .section-h h4 {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 0;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .section-h-icon {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: .95rem;
    }
</style>
@endpush

@section('content')

<!-- ===== HERO ===== -->
<div class="ops-hero">
    <div class="container-fluid px-3 px-lg-5">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <span class="hero-pill"><i class="bi bi-kanban-fill"></i> CRM Comercial</span>
                    <span class="hero-pill"><i class="bi bi-graph-up-arrow"></i> Embudo de ventas</span>
                </div>
                <h1 class="hero-title">Mis Oportunidades Comerciales</h1>
                <p class="hero-sub">Gestiona tu pipeline de ventas, da seguimiento a cada comercio y registra el progreso de tus negociaciones.</p>
            </div>
            <div class="mt-1">
                <a href="{{ route('mapa.index') }}"
                   style="background:rgba(255,255,255,.18);color:#fff;border:1.5px solid rgba(255,255,255,.4);border-radius:10px;padding:.5rem 1.1rem;font-weight:600;font-size:.85rem;display:flex;align-items:center;gap:.4rem;text-decoration:none;">
                    <i class="bi bi-plus-circle-fill"></i> Agregar desde el Mapa
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-3 px-lg-5">

    <!-- ===== KPIs ===== -->
    <div class="stat-grid" style="margin-top: -1.5rem; position: relative; z-index: 10;">
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#ede9fe; color:#7c3aed;">
                <i class="bi bi-kanban-fill"></i>
            </div>
            <div>
                <div class="kpi-label">Total Oportunidades</div>
                <div class="kpi-value">{{ $metricas['total_gestiones'] }}</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#fef9c3; color:#ca8a04;">
                <i class="bi bi-cart-check-fill"></i>
            </div>
            <div>
                <div class="kpi-label">Primeros Pedidos</div>
                <div class="kpi-value" style="color:#ca8a04;">{{ $metricas['primeros_pedidos'] }}</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#dcfce7; color:#15803d;">
                <i class="bi bi-shop-fill"></i>
            </div>
            <div>
                <div class="kpi-label">Puntos Activos</div>
                <div class="kpi-value" style="color:#15803d;">{{ $metricas['puntos_venta_activos'] }}</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#e0f2fe; color:#0369a1;">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div>
                <div class="kpi-label">Monto Facturado</div>
                <div class="kpi-value" style="color:#0369a1; font-size:1.2rem;">${{ number_format($metricas['total_ventas_pedidos'], 0) }}</div>
            </div>
        </div>
    </div>

    <!-- ===== KANBAN ===== -->
    <div class="section-h">
        <h4>
            <span class="section-h-icon" style="background:var(--primary-lt); color:var(--primary);">
                <i class="bi bi-funnel-fill"></i>
            </span>
            Embudo Comercial
        </h4>
        <p class="text-muted mb-0" style="font-size:.8rem;">Haz clic en una tarjeta para actualizar el estado y notas.</p>
    </div>

    <div class="kanban-scroll">
        @php
            $estadosConfig = [
                'por_contactar'     => ['titulo' => 'Por Contactar',      'color' => '#64748b', 'emoji' => '📋'],
                'contactado'        => ['titulo' => 'Contactado',          'color' => '#0284c7', 'emoji' => '📞'],
                'muestra_entregada' => ['titulo' => 'Muestra Entregada',   'color' => '#7c3aed', 'emoji' => '📦'],
                'en_negociacion'    => ['titulo' => 'En Negociación',      'color' => '#d97706', 'emoji' => '🤝'],
                'primer_pedido'     => ['titulo' => 'Primer Pedido',       'color' => '#16a34a', 'emoji' => '🛒'],
                'punto_activo'      => ['titulo' => 'Punto Venta Activo',  'color' => '#059669', 'emoji' => '✅'],
                'no_concretada'     => ['titulo' => 'No Concretada',       'color' => '#dc2626', 'emoji' => '❌'],
            ];
        @endphp

        @foreach($estadosConfig as $slug => $cfg)
            @php $lista = $porEstado[$slug] ?? collect(); @endphp

            <div class="kanban-col">
                <div class="kanban-header" style="border-color: {{ $cfg['color'] }};">
                    <div class="kanban-header-title" style="color: {{ $cfg['color'] }};">
                        {{ $cfg['emoji'] }} {{ $cfg['titulo'] }}
                    </div>
                    <div class="kanban-count" style="background: {{ $cfg['color'] }};">{{ $lista->count() }}</div>
                </div>

                @forelse($lista as $op)
                    <div class="kanban-card" data-bs-toggle="modal" data-bs-target="#modalOp{{ $op->id }}">
                        <div class="kanban-card-name">{{ $op->establecimiento->nombre_establecimiento }}</div>
                        <div class="kanban-card-loc">
                            <i class="bi bi-geo-alt text-danger"></i>
                            {{ $op->establecimiento->parroquia }} · {{ $op->establecimiento->tipo_establecimiento }}
                        </div>

                        @if($op->producto)
                            <span class="kanban-card-product">
                                <i class="bi bi-box-seam"></i> {{ $op->producto->nombre }}
                            </span>
                        @endif

                        @if($op->notas_privadas)
                            <div class="kanban-card-note">"{{ $op->notas_privadas }}"</div>
                        @endif

                        <div class="kanban-card-footer">
                            <span class="kanban-date">
                                @if($op->fecha_proxima_gestion)
                                    <i class="bi bi-calendar-event me-1"></i>{{ $op->fecha_proxima_gestion->format('d/m') }}
                                @else
                                    <span class="text-muted"><i class="bi bi-calendar me-1"></i>Sin fecha</span>
                                @endif
                            </span>
                            @if($op->monto_primer_pedido)
                                <span class="kanban-amount">${{ number_format($op->monto_primer_pedido, 2) }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- MODAL DE GESTIÓN -->
                    <div class="modal fade" id="modalOp{{ $op->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('oportunidades.update', $op->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <div>
                                            <div class="modal-title">{{ $op->establecimiento->nombre_establecimiento }}</div>
                                            <small class="text-muted">{{ $op->establecimiento->parroquia }} · Tel: {{ $op->establecimiento->telefono ?? 'N/D' }}</small>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">

                                        <div class="mb-3">
                                            <label class="form-label-sig">Estado del Pipeline</label>
                                            <select name="estado" class="form-select form-select-sig">
                                                <option value="por_contactar"     {{ $op->estado == 'por_contactar'     ? 'selected' : '' }}>📋 Por contactar</option>
                                                <option value="contactado"        {{ $op->estado == 'contactado'        ? 'selected' : '' }}>📞 Contactado</option>
                                                <option value="muestra_entregada" {{ $op->estado == 'muestra_entregada' ? 'selected' : '' }}>📦 Muestra entregada</option>
                                                <option value="en_negociacion"    {{ $op->estado == 'en_negociacion'    ? 'selected' : '' }}>🤝 En negociación</option>
                                                <option value="primer_pedido"     {{ $op->estado == 'primer_pedido'     ? 'selected' : '' }}>🛒 Primer pedido</option>
                                                <option value="punto_activo"      {{ $op->estado == 'punto_activo'      ? 'selected' : '' }}>✅ Punto de venta activo</option>
                                                <option value="no_concretada"     {{ $op->estado == 'no_concretada'     ? 'selected' : '' }}>❌ No concretada</option>
                                            </select>
                                        </div>

                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <label class="form-label-sig">Próxima Gestión</label>
                                                <input type="date" name="fecha_proxima_gestion" class="form-control form-control-sig"
                                                       value="{{ $op->fecha_proxima_gestion ? $op->fecha_proxima_gestion->toDateString() : '' }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label-sig">Monto 1er Pedido ($)</label>
                                                <input type="number" step="0.01" name="monto_primer_pedido" class="form-control form-control-sig"
                                                       value="{{ $op->monto_primer_pedido }}" placeholder="0.00">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label-sig">Condiciones Acordadas</label>
                                            <input type="text" name="condiciones_acordadas" class="form-control form-control-sig"
                                                   value="{{ $op->condiciones_acordadas }}" placeholder="Ej: Pago contado, entrega cada martes, 20 quesos">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label-sig">Notas Privadas</label>
                                            <textarea name="notas_privadas" class="form-control form-control-sig" rows="3"
                                                      placeholder="Preferencias, respuesta a muestras, detalles del propietario…">{{ $op->notas_privadas }}</textarea>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label-sig">Motivo de Cierre (si no concretada)</label>
                                            <input type="text" name="motivo_cierre" class="form-control form-control-sig"
                                                   value="{{ $op->motivo_cierre }}" placeholder="Ej: Ya tiene proveedor exclusivo, precio alto…">
                                        </div>

                                    </div>
                                    <div class="modal-footer d-flex justify-content-between">
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('establecimientos.show', $op->establecimiento_id) }}"
                                               class="btn btn-sm btn-outline-secondary">Ver Ficha</a>
                                            @if($op->establecimiento->telefono)
                                                <a href="{{ $op->establecimiento->url_whatsapp }}" target="_blank"
                                                   class="btn btn-sm"
                                                   style="background:#22c55e;color:#fff;border:none;border-radius:8px;">
                                                    <i class="bi bi-whatsapp"></i>
                                                </a>
                                            @endif
                                        </div>
                                        <button type="submit" class="btn-primary-sig">
                                            <i class="bi bi-check-circle me-1"></i>Guardar Cambios
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                @empty
                    <div class="kanban-empty">
                        <i class="bi bi-inbox"></i>
                        Sin oportunidades aquí
                    </div>
                @endforelse
            </div>
        @endforeach
    </div>

</div>
@endsection
