@extends('layouts.app')

@section('title', 'Catálogo Comercial - ' . ($usuario->nombre_emprendimiento ?? $usuario->name))

@section('content')
<div class="container py-4">
    <!-- Banner de Presentación del Emprendedor -->
    <div class="card card-custom bg-white border-0 p-4 mb-4 text-center">
        <span class="badge bg-warning text-dark mx-auto mb-2 px-3 py-1">Catálogo Comercial Oficial</span>
        <h2 class="fw-bold mb-1">{{ $usuario->nombre_emprendimiento ?? 'Lácteos Artesanales del Cotopaxi' }}</h2>
        <p class="text-muted mb-2"><i class="bi bi-geo-alt text-danger me-1"></i> {{ $usuario->parroquia ?? 'Latacunga' }}, Cotopaxi, Ecuador</p>
        <div class="d-flex justify-content-center gap-3 small text-muted">
            @if($usuario->telefono)
                <span><i class="bi bi-telephone text-primary me-1"></i> {{ $usuario->telefono }}</span>
            @endif
            <span><i class="bi bi-envelope text-primary me-1"></i> {{ $usuario->email }}</span>
        </div>
    </div>

    <div class="row g-4">
        @forelse($productos as $prod)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card card-custom h-100 p-3 border">
                    <span class="badge bg-primary-subtle text-primary w-auto mb-2 align-self-start">{{ $prod->categoria_nombre }}</span>
                    <h5 class="fw-bold mb-1">{{ $prod->nombre }}</h5>
                    <p class="text-muted small mb-2">{{ $prod->presentacion }}</p>
                    <p class="small text-secondary flex-grow-1">{{ $prod->descripcion }}</p>
                    <div class="bg-light p-2 rounded mb-3 small">
                        <div class="d-flex justify-content-between">
                            <span>Precio al comercio:</span>
                            <strong class="text-success">${{ number_format($prod->precio_comercio, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>PVP sugerido:</span>
                            <span>${{ number_format($prod->precio_sugerido, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Pedido mínimo:</span>
                            <span>{{ $prod->pedido_minimo }}</span>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('productos.ficha-pdf', $prod->id) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Ver Ficha Comercial
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">No hay productos disponibles actualmente en este catálogo.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
