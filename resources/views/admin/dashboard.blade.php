@extends('layouts.app')

@section('title', 'Panel de Administración - SIG Lácteos')

@section('content')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-shield-lock-fill text-primary me-2"></i>Panel de Administración y Control</h3>
            <p class="text-muted small mb-0">Gestión de calidad geográfica, verificación de comercios, revisión de reportes y auditoría del cantón Latacunga.</p>
        </div>
        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalDuplicados">
            <i class="bi bi-copy me-1"></i> Detector de Duplicados
        </button>
    </div>

    <!-- Indicadores de Calidad del Dato -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card card-custom p-3 bg-white">
                <small class="text-muted d-block">Establecimientos Totales</small>
                <h4 class="fw-bold mb-0 text-primary">{{ $metricas['total_establecimientos'] }}</h4>
                <small class="text-muted" style="font-size: 0.75rem;">100% georreferenciados</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-custom p-3 bg-white">
                <small class="text-muted d-block">Comercios Verificados</small>
                <h4 class="fw-bold mb-0 text-success">{{ $metricas['verificados'] }}</h4>
                <small class="text-success" style="font-size: 0.75rem;">Confirmados en campo</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-custom p-3 bg-white">
                <small class="text-muted d-block">Comercios Reportados</small>
                <h4 class="fw-bold mb-0 text-danger">{{ $metricas['reportados'] }}</h4>
                <small class="text-muted" style="font-size: 0.75rem;">Por usuarios/emprendedores</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-custom p-3 bg-white">
                <small class="text-muted d-block">Reportes Pendientes</small>
                <h4 class="fw-bold mb-0 text-warning">{{ $metricas['reportes_pendientes'] }}</h4>
                <small class="text-muted" style="font-size: 0.75rem;">Requieren revisión</small>
            </div>
        </div>
    </div>

    <!-- Sección de Reportes Pendientes de Información Incorrecta (Objetivo 8) -->
    <div class="card card-custom mb-4">
        <div class="card-header bg-white border-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-flag-fill text-danger me-2"></i>Reportes de Información Incorrecta Pendientes</h5>
            <span class="badge bg-danger rounded-pill">{{ $reportes->total() }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Establecimiento</th>
                            <th>Parroquia</th>
                            <th>Motivo del Reporte</th>
                            <th>Detalle Reportado</th>
                            <th>Fecha</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse($reportes as $rep)
                            <tr>
                                <td>
                                    <strong>{{ $rep->establecimiento->nombre_establecimiento }}</strong>
                                    <div class="text-muted" style="font-size: 0.75rem;">ID: #{{ $rep->establecimiento->id }}</div>
                                </td>
                                <td>{{ $rep->establecimiento->parroquia }}</td>
                                <td><span class="badge bg-warning text-dark">{{ $rep->tipo_error }}</span></td>
                                <td>{{ $rep->descripcion }}</td>
                                <td>{{ $rep->created_at->format('d/m/Y') }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('establecimientos.show', $rep->establecimiento_id) }}" target="_blank" class="btn btn-outline-secondary">Ver Ficha</a>
                                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalCorregir{{ $rep->establecimiento_id }}">Corregir GPS</button>
                                        <form method="POST" action="{{ route('admin.resolver-reporte', $rep->id) }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="estado" value="corregido">
                                            <button type="submit" class="btn btn-success">Resolver</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-check2-circle text-success fs-3 d-block mb-1"></i>
                                    No hay reportes pendientes de revisión.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modales para Corrección de Coordenadas (Objetivo 8) -->
    @foreach($reportes as $rep)
        <div class="modal fade" id="modalCorregir{{ $rep->establecimiento_id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.corregir-ubicacion', $rep->establecimiento_id) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Corregir Coordenadas GPS</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="small text-muted mb-3">
                                Corrige el punto de geolocalización para <strong>{{ $rep->establecimiento->nombre_establecimiento }}</strong>.
                            </p>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Latitud (GPS)</label>
                                    <input type="text" name="latitud" class="form-control" value="{{ $rep->establecimiento->latitud }}" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Longitud (GPS)</label>
                                    <input type="text" name="longitud" class="form-control" value="{{ $rep->establecimiento->longitud }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Nueva Ubicación</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- MODAL: DETECTOR DE DUPLICADOS (Objetivo 8) -->
<div class="modal fade" id="modalDuplicados" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-copy me-2"></i>Detector Inteligente de Posibles Duplicados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3">
                    Ingresa el nombre o teléfono del establecimiento para verificar si ya fue registrado en la base de datos de Cotopaxi.
                </p>
                <div class="input-group mb-3">
                    <input type="text" id="busqDuplicado" class="form-control" placeholder="Ej: Tienda San José, 0982000121...">
                    <button class="btn btn-primary" onclick="buscarDuplicados()">Comprobar</button>
                </div>
                <div id="resultadoDuplicados" class="small">
                    <div class="text-center text-muted py-3">Ingresa un término para comprobar coincidencias.</div>
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
        resDiv.innerHTML = '<div class="text-center py-2"><span class="spinner-border spinner-border-sm"></span> Comprobando base de datos...</div>';

        fetch('/establecimientos/detectar-duplicados?nombre=' + encodeURIComponent(val) + '&telefono=' + encodeURIComponent(val), {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.hay_duplicados) {
                let html = '<div class="alert alert-warning mb-2"><strong>Atención:</strong> Se encontraron posibles comercios duplicados:</div><ul class="list-group">';
                data.establecimientos.forEach(e => {
                    html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                <div><strong>${e.nombre_establecimiento}</strong> (${e.tipo_establecimiento}) - ${e.parroquia}</div>
                                <a href="/establecimientos/${e.id}" target="_blank" class="btn btn-sm btn-outline-primary">Ver</a>
                             </li>`;
                });
                html += '</ul>';
                resDiv.innerHTML = html;
            } else {
                resDiv.innerHTML = '<div class="alert alert-success">✅ No se detectaron duplicados con ese nombre o teléfono. El registro es único.</div>';
            }
        })
        .catch(e => {
            resDiv.innerHTML = '<div class="alert alert-danger">Error al consultar el servicio de duplicados.</div>';
        });
    }
</script>
@endpush
