@extends('layouts.app')

@section('title', 'Mis Oportunidades Comerciales (CRM)')

@push('styles')
<style>
    .kanban-col {
        background-color: #f1f5f9;
        border-radius: 10px;
        padding: 12px;
        min-width: 280px;
        max-width: 320px;
        flex-shrink: 0;
    }
    .kanban-header {
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding-bottom: 8px;
        margin-bottom: 12px;
        border-bottom: 2px solid;
    }
    .kanban-card {
        background: white;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .kanban-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        border-color: #cbd5e1;
    }
    .kanban-scroll {
        display: flex;
        overflow-x: auto;
        gap: 16px;
        padding-bottom: 16px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-lg-4 py-2">
    <!-- Métricas Clave de Éxito (Requisito Objetivo 7 y 10) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card card-custom p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle p-3 bg-primary-subtle text-primary me-3">
                        <i class="bi bi-kanban fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Oportunidades Totales</small>
                        <h4 class="fw-bold mb-0">{{ $metricas['total_gestiones'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-custom p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle p-3 bg-warning-subtle text-warning me-3">
                        <i class="bi bi-cart-check fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Primeros Pedidos</small>
                        <h4 class="fw-bold mb-0 text-warning">{{ $metricas['primeros_pedidos'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-custom p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle p-3 bg-success-subtle text-success me-3">
                        <i class="bi bi-shop fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Puntos de Venta Activos</small>
                        <h4 class="fw-bold mb-0 text-success">{{ $metricas['puntos_venta_activos'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-custom p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle p-3 bg-info-subtle text-info me-3">
                        <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Monto Facturado</small>
                        <h4 class="fw-bold mb-0 text-info">${{ number_format($metricas['total_ventas_pedidos'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Encabezado de la sección -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-funnel-fill text-primary me-2"></i>Embudo Comercial (CRM)</h4>
            <p class="text-muted small mb-0">Haz clic en cualquier tarjeta para actualizar notas privadas, fechas de seguimiento y condiciones acordadas.</p>
        </div>
        <a href="{{ route('mapa.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Explorar Más Comercios en el Mapa
        </a>
    </div>

    <!-- Tablero Kanban de Estados (Objetivo 7) -->
    <div class="kanban-scroll">
        @php
            $estadosConfig = [
                'por_contactar'     => ['titulo' => 'Por Contactar',     'color' => '#64748b', 'badge' => 'bg-secondary'],
                'contactado'        => ['titulo' => 'Contactado',        'color' => '#0284c7', 'badge' => 'bg-info'],
                'muestra_entregada' => ['titulo' => 'Muestra Entregada', 'color' => '#4f46e5', 'badge' => 'bg-primary'],
                'en_negociacion'    => ['titulo' => 'En Negociación',    'color' => '#d97706', 'badge' => 'bg-warning'],
                'primer_pedido'     => ['titulo' => 'Primer Pedido',     'color' => '#16a34a', 'badge' => 'bg-success'],
                'punto_activo'      => ['titulo' => 'Punto Venta Activo','color' => '#059669', 'badge' => 'bg-success'],
                'no_concretada'     => ['titulo' => 'No Concretada',     'color' => '#dc2626', 'badge' => 'bg-danger'],
            ];
        @endphp

        @foreach($estadosConfig as $slug => $cfg)
            @php $lista = $porEstado[$slug] ?? collect(); @endphp
            <div class="kanban-col">
                <div class="kanban-header d-flex justify-content-between align-items-center" style="border-color: {{ $cfg['color'] }}; color: {{ $cfg['color'] }};">
                    <span>{{ $cfg['titulo'] }}</span>
                    <span class="badge {{ $cfg['badge'] }} rounded-pill">{{ $lista->count() }}</span>
                </div>

                @forelse($lista as $op)
                    <div class="kanban-card" data-bs-toggle="modal" data-bs-target="#modalOp{{ $op->id }}">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <strong class="text-dark">{{ $op->establecimiento->nombre_establecimiento }}</strong>
                        </div>
                        <div class="small text-muted mb-2">
                            <i class="bi bi-geo-alt"></i> {{ $op->establecimiento->parroquia }} • {{ $op->establecimiento->tipo_establecimiento }}
                        </div>
                        
                        @if($op->producto)
                            <div class="badge bg-light text-dark border mb-2 text-wrap text-start">
                                <i class="bi bi-box me-1"></i> {{ $op->producto->nombre }}
                            </div>
                        @endif

                        @if($op->notas_privadas)
                            <p class="small text-secondary mb-2 fst-italic text-truncate" style="max-height: 40px;">
                                "{{ $op->notas_privadas }}"
                            </p>
                        @endif

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top small text-muted">
                            @if($op->fecha_proxima_gestion)
                                <span class="text-primary"><i class="bi bi-calendar-event"></i> {{ $op->fecha_proxima_gestion->format('d/m') }}</span>
                            @else
                                <span><i class="bi bi-calendar"></i> Sin fecha</span>
                            @endif

                            @if($op->monto_primer_pedido)
                                <span class="fw-bold text-success">${{ number_format($op->monto_primer_pedido, 2) }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- MODAL DE GESTIÓN DE OPORTUNIDAD -->
                    <div class="modal fade" id="modalOp{{ $op->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('oportunidades.update', $op->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <div>
                                            <h5 class="modal-title fw-bold mb-0">{{ $op->establecimiento->nombre_establecimiento }}</h5>
                                            <small class="text-muted">{{ $op->establecimiento->parroquia }} • Contacto: {{ $op->establecimiento->telefono ?? 'N/D' }}</small>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Estado del Pipeline *</label>
                                            <select name="estado" class="form-select" id="selectEstado{{ $op->id }}">
                                                <option value="por_contactar" {{ $op->estado == 'por_contactar' ? 'selected' : '' }}>Por contactar</option>
                                                <option value="contactado" {{ $op->estado == 'contactado' ? 'selected' : '' }}>Contactado</option>
                                                <option value="muestra_entregada" {{ $op->estado == 'muestra_entregada' ? 'selected' : '' }}>Muestra entregada</option>
                                                <option value="en_negociacion" {{ $op->estado == 'en_negociacion' ? 'selected' : '' }}>En negociación</option>
                                                <option value="primer_pedido" {{ $op->estado == 'primer_pedido' ? 'selected' : '' }}>Primer pedido</option>
                                                <option value="punto_activo" {{ $op->estado == 'punto_activo' ? 'selected' : '' }}>Punto de venta activo</option>
                                                <option value="no_concretada" {{ $op->estado == 'no_concretada' ? 'selected' : '' }}>No concretada</option>
                                            </select>
                                        </div>

                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <label class="form-label fw-bold small">Próxima Gestión / Seguimiento</label>
                                                <input type="date" name="fecha_proxima_gestion" class="form-control" value="{{ $op->fecha_proxima_gestion ? $op->fecha_proxima_gestion->toDateString() : '' }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-bold small">Monto 1er Pedido ($)</label>
                                                <input type="number" step="0.01" name="monto_primer_pedido" class="form-control" value="{{ $op->monto_primer_pedido }}" placeholder="0.00">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Condiciones Acordadas</label>
                                            <input type="text" name="condiciones_acordadas" class="form-control" value="{{ $op->condiciones_acordadas }}" placeholder="Ej: Pago contado, entrega cada martes, 20 quesos">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Notas Privadas del Emprendedor</label>
                                            <textarea name="notas_privadas" class="form-control" rows="3" placeholder="Anotaciones sobre preferencias del comerciante, respuesta a muestras, etc...">{{ $op->notas_privadas }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Motivo de Cierre (Si aplica "No Concretada")</label>
                                            <input type="text" name="motivo_cierre" class="form-control" value="{{ $op->motivo_cierre }}" placeholder="Ej: Ya tiene proveedor exclusivo, precio alto, sin frío...">
                                        </div>
                                    </div>
                                    <div class="modal-footer d-flex justify-content-between">
                                        <div>
                                            <a href="{{ route('establecimientos.show', $op->establecimiento_id) }}" class="btn btn-outline-secondary btn-sm">Ver Ficha</a>
                                            @if($op->establecimiento->telefono)
                                                <a href="{{ $op->establecimiento->url_whatsapp }}" target="_blank" class="btn btn-success btn-sm"><i class="bi bi-whatsapp"></i></a>
                                            @endif
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm">Guardar Cambios</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted small">
                        Sin oportunidades aquí
                    </div>
                @endforelse
            </div>
        @endforeach
    </div>
</div>
@endsection
