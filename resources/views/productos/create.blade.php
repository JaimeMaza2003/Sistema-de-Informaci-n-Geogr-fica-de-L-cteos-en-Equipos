@extends('layouts.app')

@section('title', 'Registrar Nuevo Producto Lácteo')

@section('content')
<div class="container py-3" style="max-width: 800px;">
    <div class="card card-custom">
        <div class="card-header bg-white border-0 pt-4 pb-0">
            <h4 class="fw-bold mb-1"><i class="bi bi-plus-circle text-primary me-2"></i>Registrar Producto en el Catálogo</h4>
            <p class="text-muted small">Ingresa las especificaciones comerciales y logísticas de tu producto lácteo.</p>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('productos.store') }}">
                @csrf
                
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold small">Nombre del Producto *</label>
                        <input type="text" name="nombre" class="form-control" required placeholder="Ej: Queso Mozzarella Artesanal">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Categoría *</label>
                        <select name="categoria_id" class="form-select" required>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Presentación *</label>
                        <input type="text" name="presentacion" class="form-control" required placeholder="Ej: Bloque 500g, Botella 1 Litro, Tarrina 250g">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Precio al Comercio ($) *</label>
                        <input type="number" step="0.01" name="precio_comercio" class="form-control" required placeholder="2.30">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">PVP Sugerido ($) *</label>
                        <input type="number" step="0.01" name="precio_sugerido" class="form-control" required placeholder="3.00">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small">Descripción del Producto</label>
                        <textarea name="descripcion" class="form-control" rows="3" placeholder="Describe ingredientes, proceso de elaboración, características sensoriales y beneficios..."></textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Pedido Mínimo *</label>
                        <input type="text" name="pedido_minimo" class="form-control" required placeholder="Ej: 10 unidades, 1 jabilla">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Capacidad Semanal *</label>
                        <input type="text" name="capacidad_semanal" class="form-control" required placeholder="Ej: 300 unidades / semana">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Vida Útil (Días) *</label>
                        <input type="number" name="vida_util_dias" class="form-control" required value="21">
                    </div>

                    <!-- Condiciones de Conservación -->
                    <div class="col-12 border-top pt-3 mt-3">
                        <h6 class="fw-bold text-primary"><i class="bi bi-snow me-1"></i>Conservación y Logística</h6>
                    </div>

                    <div class="col-md-4">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="requiere_refrigeracion" value="1" id="checkRef" checked>
                            <label class="form-check-label fw-bold small" for="checkRef">
                                Requiere Refrigeración
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold small">Temperatura de Conservación</label>
                        <input type="text" name="temperatura_conservacion" class="form-control" value="2°C a 6°C">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Zonas de Entrega (Parroquias/Sectores) *</label>
                        <input type="text" name="zonas_entrega" class="form-control" required value="Latacunga, Salache, Tanicuchi, Guaytacama, Mulalo, Lasso">
                        <small class="text-muted" style="font-size: 0.75rem;">Separa con comas las parroquias cubiertas.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Frecuencia de Entrega *</label>
                        <input type="text" name="frecuencia_entrega" class="form-control" required value="Semanal (Martes y Viernes)">
                    </div>

                    <!-- Documentación Disponible -->
                    <div class="col-12 border-top pt-3 mt-3">
                        <h6 class="fw-bold text-primary"><i class="bi bi-file-earmark-check me-1"></i>Documentación y Certificaciones Disponibles</h6>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Registro Sanitario / Notificación Sanitaria</label>
                        <input type="text" name="registro_sanitario" class="form-control" placeholder="Ej: 1589-ALN-0819">
                    </div>
                    <div class="col-md-6 d-flex align-items-center gap-3 pt-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bpm_disponible" value="1" id="bpmCheck" checked>
                            <label class="form-check-label small" for="bpmCheck">BPM</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="ruc_disponible" value="1" id="rucCheck" checked>
                            <label class="form-check-label small" for="rucCheck">RUC / Factura</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tabla_nutricional" value="1" id="tnCheck" checked>
                            <label class="form-check-label small" for="tnCheck">Tabla Nutricional</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
