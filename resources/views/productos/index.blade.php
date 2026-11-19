@extends('layouts.app')

@section('title', 'Mi Catálogo de Productos Lácteos')

@section('content')
<div class="container py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-box-seam text-primary me-2"></i>Catálogo de Productos del Emprendedor</h3>
            <p class="text-muted small mb-0">Gestiona tu oferta láctea, genera fichas técnicas y encuentra comercios compatibles para colocación en percha.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('productos.catalogo-publico', auth()->id() ?? 2) }}" target="_blank" class="btn btn-outline-secondary">
                <i class="bi bi-share me-1"></i> Compartir Catálogo
            </a>
            <a href="{{ route('productos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Nuevo Producto
            </a>
        </div>
    </div>

    <div class="row g-4">
        @forelse($productos as $prod)
            <div class="col-12 col-md-6 col-lg-6 col-xl-3">
                <div class="card card-custom h-100">
                    <div class="card-body p-3 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">
                                {{ $prod->categoria_nombre }}
                            </span>
                            @if($prod->requiere_refrigeracion)
                                <span class="badge bg-info-subtle text-info border border-info-subtle small" title="Requiere refrigeración">
                                    <i class="bi bi-snow"></i> {{ $prod->temperatura_conservacion }}
                                </span>
                            @endif
                        </div>

                        <h5 class="fw-bold mb-1">{{ $prod->nombre }}</h5>
                        <p class="text-muted small mb-2">{{ $prod->presentacion }}</p>

                        <div class="bg-light p-2 rounded mb-3 small flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Precio Comercio:</span>
                                <strong class="text-success">${{ number_format($prod->precio_comercio, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">PVP Sugerido:</span>
                                <span>${{ number_format($prod->precio_sugerido, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Pedido Mínimo:</span>
                                <span>{{ $prod->pedido_minimo }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Capacidad Semanal:</span>
                                <span>{{ $prod->capacidad_semanal }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Vida Útil:</span>
                                <span>{{ $prod->vida_util_dias }} días</span>
                            </div>
                        </div>

                        <!-- Botón Clave de Vinculación (Objetivo 5) -->
                        <div class="d-grid gap-2">
                            <a href="{{ route('productos.compatibles', $prod->id) }}" class="btn btn-warning fw-bold text-dark">
                                <i class="bi bi-compass me-1"></i> Buscar dónde ofrecerlo
                            </a>
                            <a href="{{ route('productos.ficha-pdf', $prod->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Ficha Comercial (PDF)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-box2 display-3 text-muted"></i>
                <h5 class="mt-3">Aún no has registrado productos en tu catálogo</h5>
                <p class="text-muted">Registra tus quesos, leches, yogurts o mantequillas para encontrar tiendas y supermercados compatibles.</p>
                <a href="{{ route('productos.create') }}" class="btn btn-primary">Registrar mi primer producto</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
