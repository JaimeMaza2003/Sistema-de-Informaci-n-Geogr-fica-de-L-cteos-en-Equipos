<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIG Lácteos Cotopaxi') - Conexión Comercial</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Leaflet CSS & JS en HEAD -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    
    <style>
        :root {
            --sig-primary: #1e5f74;
            --sig-secondary: #133b5c;
            --sig-accent: #fcdab7;
            --sig-success: #28a745;
            --sig-bg: #f8fafc;
        }
        body {
            background-color: var(--sig-bg);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #2c3e50;
        }
        .navbar-sig {
            background: linear-gradient(135deg, #133b5c 0%, #1e5f74 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .nav-link {
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .nav-link:hover, .nav-link.active {
            color: #fcdab7 !important;
        }
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card-custom:hover {
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        }
        .badge-tienda { background-color: #0d6efd; }
        .badge-minimarket { background-color: #198754; }
        .badge-supermercado { background-color: #6f42c1; }
        .badge-panaderia { background-color: #fd7e14; }
        .badge-distribuidor { background-color: #d63384; }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Barra de Navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-sig sticky-top">
        <div class="container-fluid px-3 px-lg-4">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('mapa.index') }}">
                <i class="bi bi-geo-alt-fill text-warning me-2 fs-4"></i>
                <div>
                    <span class="fs-5">SIG Lácteos</span>
                    <small class="d-block text-white-50" style="font-size: 0.65rem; margin-top: -3px;">Cotopaxi • Latacunga</small>
                </div>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mapa.*') ? 'active' : '' }}" href="{{ route('mapa.index') }}">
                            <i class="bi bi-map me-1"></i> Mapa de Oportunidades
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}" href="{{ route('productos.index') }}">
                            <i class="bi bi-box-seam me-1"></i> Catálogo Emprendedor
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('oportunidades.*') ? 'active' : '' }}" href="{{ route('oportunidades.index') }}">
                            <i class="bi bi-kanban me-1"></i> Mis Oportunidades
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-shield-lock me-1"></i> Panel Admin
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center text-white-50 small">
                    <span class="badge bg-warning text-dark me-2">162 Establecimientos</span>
                    <span class="d-none d-md-inline"><i class="bi bi-person-circle me-1"></i> Carlos Mendoza (Emprendedor)</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Alertas Flash -->
    <div class="container-fluid px-3 px-lg-4 mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Contenido Principal -->
    <main class="py-2">
        @yield('content')
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS & MarkerCluster -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    @stack('scripts')
</body>
</html>
