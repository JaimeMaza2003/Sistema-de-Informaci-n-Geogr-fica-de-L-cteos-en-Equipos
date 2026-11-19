@extends('layouts.app')

@section('title', 'Comercios Compatibles para ' . $producto->nombre)

@section('content')
<div class="container py-3">
    <!-- Encabezado con datos del producto seleccionado -->
    <div class="card card-custom bg-light border-0 mb-4 p-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <span class="badge bg-primary mb-1">Producto Seleccionado</span>
                <h4 class="fw-bold mb-0 text-dark">{{ $producto->nombre }} ({{ $producto->presentacion }})</h4>
                <p class="small text-muted mb-0">
                    Categoría: <strong>{{ $producto->categoria_nombre }}</strong> • Cobertura: <strong>{{ $producto->zonas_entrega }}</strong> • Frecuencia: <strong>{{ $producto->frecuencia_entrega }}</strong>
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Volver al Catálogo
                </a>
                <a href="{{ route('mapa.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-map me-1"></i> Ver en el Mapa
                </a>
            </div>
        </div>
    </div>

    <!-- Advertencia requerida por la especificación (Objetivo 5) -->
    <div class="alert alert-warning d-flex align-items-center mb-4 shadow-sm" role="alert">
        <i class="bi bi-shield-exclamation fs-4 me-3"></i>
        <div class="small">
            <strong>Nota de compatibilidad:</strong> El sistema analiza establecimientos compatibles considerando la categoría del producto, zona de entrega, recepción de proveedores y condiciones de frío. <em>La compatibilidad es una sugerencia algorítmica de prospección comercial y no constituye garantía de aceptación por parte del establecimiento.</em>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-primary mb-0">
            <i class="bi bi-building-check me-2"></i>{{ count($resultados) }} Establecimientos con Alta Afinidad Comercial
        </h5>
        <span class="text-muted small">Ordenados por nivel de coincidencia</span>
    </div>

    <div class="row g-3">
        @forelse($resultados as $item)
            @php 
                $est = $item['establecimiento'];
                $pts = $item['puntaje'];
                $exp = $item['explicacion'];
                $badgeScore = $pts >= 80 ? 'bg-success' : ($pts >= 60 ? 'bg-primary' : 'bg-warning text-dark');
            @endphp
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card card-custom h-100 border p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge badge-{{ strtolower($est->tipo_establecimiento) }} me-1">
                                {{ $est->tipo_establecimiento }}
                            </span>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-geo-alt text-danger"></i> {{ $est->parroquia }}
                            </span>
                        </div>
                        <span class="badge {{ $badgeScore }} px-2 py-1 fs-6" title="Puntaje de afinidad">
                            {{ $pts }}% Coincidencia
                        </span>
                    </div>

                    <h5 class="fw-bold mb-1">{{ $est->nombre_establecimiento }}</h5>
                    <p class="text-muted small mb-2">
                        {{ $est->direccion_completa ?? $est->barrio_sector }}
                        @if($est->nombre_propietario)
                            • Propietario: <strong>{{ $est->nombre_propietario }}</strong>
                        @endif
                    </p>

                    <!-- Explicación de la coincidencia (Requisito Objetivo 5) -->
                    <div class="p-2 bg-success-subtle border border-success-subtle rounded small mb-3 text-dark">
                        <i class="bi bi-check2-circle text-success fw-bold me-1"></i>
                        <strong>¿Por qué coincide?</strong> {{ $exp }}
                    </div>

                    <div class="small bg-light p-2 rounded mb-3">
                        <div class="row g-1">
                            <div class="col-6"><strong>Recepción:</strong> {{ $est->recepcion_proveedores }}</div>
                            <div class="col-6"><strong>Frío:</strong> {{ $est->tiene_refrigeracion ? 'Cuenta con frío' : 'Sin frío' }}</div>
                            <div class="col-6"><strong>Mayor Demanda:</strong> {{ $est->producto_mayor_demanda ?? 'Lácteos' }}</div>
                            <div class="col-6"><strong>Frecuencia:</strong> {{ $est->frecuencia_abastecimiento ?? 'Semanal' }}</div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-auto">
                        <a href="{{ route('establecimientos.show', $est->id) }}" class="btn btn-outline-primary btn-sm flex-grow-1">
                            <i class="bi bi-card-text me-1"></i> Ver Ficha
                        </a>

                        @if($est->telefono)
                            <a href="{{ $est->url_whatsapp }}" target="_blank" class="btn btn-success btn-sm flex-grow-1">
                                <i class="bi bi-whatsapp me-1"></i> Contactar
                            </a>
                        @endif

                        <form method="POST" action="{{ route('establecimientos.guardar-oportunidad', $est->id) }}" class="flex-grow-1">
                            @csrf
                            <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                            <input type="hidden" name="notas_privadas" value="Añadido desde búsqueda de compatibilidad para {{ $producto->nombre }} ({{ $pts }}% coincidencia).">
                            <button type="submit" class="btn btn-outline-dark btn-sm w-100">
                                <i class="bi bi-bookmark-plus me-1"></i> Guardar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-search display-3 text-muted"></i>
                <h5 class="mt-3">No se encontraron establecimientos compatibles actualmente</h5>
                <p class="text-muted">Prueba ampliando las zonas de entrega en la ficha de tu producto.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
