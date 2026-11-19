<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIG Lácteos Cotopaxi') — Conexión Comercial</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

    <style>
        /* ============================================
           DESIGN SYSTEM — SIG LÁCTEOS COTOPAXI
        ============================================ */
        :root {
            --primary:    #0f7a5a;   /* Verde bosque andino */
            --primary-dk: #095c43;
            --primary-lt: #e6f7f2;
            --accent:     #f5a623;   /* Naranja lácteo */
            --accent-lt:  #fff8ed;
            --sky:        #1a7fc1;
            --sky-lt:     #e8f4fd;
            --danger:     #e53e3e;
            --success:    #2f855a;
            --bg:         #f4f7f6;
            --surface:    #ffffff;
            --text:       #1a2c26;
            --text-muted: #6b7c76;
            --border:     #dde8e3;
            --shadow-sm:  0 1px 4px rgba(0,0,0,.06);
            --shadow-md:  0 4px 16px rgba(0,0,0,.08);
            --shadow-lg:  0 8px 32px rgba(0,0,0,.12);
            --radius-sm:  8px;
            --radius-md:  14px;
            --radius-lg:  20px;
        }

        * { box-sizing: border-box; }

        body {
            background-color: var(--bg);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text);
            font-size: 0.925rem;
            line-height: 1.6;
        }

        /* ---- NAVBAR ---- */
        .navbar-sig {
            background: linear-gradient(135deg, var(--primary-dk) 0%, var(--primary) 60%, #1a9970 100%);
            box-shadow: 0 2px 20px rgba(0,0,0,.18);
            padding: .6rem 1rem;
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .navbar-brand-wrap {
            display: flex;
            align-items: center;
            gap: .6rem;
            text-decoration: none;
        }
        .navbar-logo-box {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,.18);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }
        .navbar-brand-text strong {
            display: block;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: #fff;
            line-height: 1.1;
        }
        .navbar-brand-text span {
            font-size: .65rem;
            color: rgba(255,255,255,.65);
            letter-spacing: .4px;
        }

        .nav-link-sig {
            color: rgba(255,255,255,.82) !important;
            font-weight: 500;
            font-size: .88rem;
            padding: .45rem .75rem !important;
            border-radius: 8px;
            transition: all .2s ease;
            display: flex;
            align-items: center;
            gap: .35rem;
        }
        .nav-link-sig:hover,
        .nav-link-sig.active {
            color: #fff !important;
            background: rgba(255,255,255,.18);
        }
        .nav-link-sig.active {
            background: rgba(255,255,255,.22);
            font-weight: 600;
        }

        .navbar-badge {
            background: var(--accent);
            color: var(--text);
            font-size: .72rem;
            font-weight: 700;
            padding: .25rem .6rem;
            border-radius: 20px;
        }

        /* ---- CARDS ---- */
        .card-sig {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            transition: transform .22s ease, box-shadow .22s ease;
            overflow: hidden;
        }
        .card-sig:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        /* ---- STAT CARDS ---- */
        .stat-card {
            background: var(--surface);
            border-radius: var(--radius-md);
            padding: 1.1rem 1.25rem;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }
        .stat-label { font-size: .78rem; color: var(--text-muted); font-weight: 500; }
        .stat-value { font-size: 1.5rem; font-weight: 700; line-height: 1; color: var(--text); }

        /* ---- SECTION HEADER ---- */
        .section-header {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.15rem;
            color: var(--text);
            margin-bottom: .5rem;
        }
        .section-header-icon {
            width: 34px;
            height: 34px;
            background: var(--primary-lt);
            color: var(--primary);
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        /* ---- BADGE TYPES ---- */
        .badge-tipo {
            font-size: .72rem;
            font-weight: 600;
            padding: .3em .7em;
            border-radius: 20px;
            letter-spacing: .2px;
        }
        .badge-tienda       { background: #dbeafe; color: #1d4ed8; }
        .badge-minimarket   { background: #dcfce7; color: #15803d; }
        .badge-supermercado { background: #ede9fe; color: #7c3aed; }
        .badge-panaderia    { background: #ffedd5; color: #c2410c; }
        .badge-panadería    { background: #ffedd5; color: #c2410c; }
        .badge-distribuidor { background: #fce7f3; color: #be185d; }

        /* ---- BUTTONS ---- */
        .btn-primary-sig {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: .5rem 1.1rem;
            font-weight: 600;
            font-size: .88rem;
            transition: background .2s, transform .15s;
        }
        .btn-primary-sig:hover {
            background: var(--primary-dk);
            color: #fff;
            transform: translateY(-1px);
        }
        .btn-accent-sig {
            background: var(--accent);
            color: var(--text);
            border: none;
            border-radius: 10px;
            padding: .5rem 1.1rem;
            font-weight: 700;
            font-size: .88rem;
            transition: filter .2s, transform .15s;
        }
        .btn-accent-sig:hover {
            filter: brightness(1.07);
            transform: translateY(-1px);
        }
        .btn-ghost-sig {
            background: transparent;
            color: var(--primary);
            border: 1.5px solid var(--primary);
            border-radius: 10px;
            padding: .5rem 1.1rem;
            font-weight: 600;
            font-size: .88rem;
            transition: all .2s;
        }
        .btn-ghost-sig:hover {
            background: var(--primary-lt);
        }

        /* ---- FILTER PANEL ---- */
        .filter-panel-sig {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 1rem 1.25rem;
            box-shadow: var(--shadow-sm);
        }
        .filter-panel-sig .form-control,
        .filter-panel-sig .form-select {
            border-radius: 10px;
            border-color: var(--border);
            font-size: .875rem;
            background: var(--bg);
        }
        .filter-panel-sig .form-control:focus,
        .filter-panel-sig .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(15,122,90,.12);
        }

        /* ---- ALERTS ---- */
        .alert-sig-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-left: 4px solid var(--success);
            border-radius: var(--radius-sm);
            color: #14532d;
        }
        .alert-sig-danger {
            background: #fff5f5;
            border: 1px solid #feb2b2;
            border-left: 4px solid var(--danger);
            border-radius: var(--radius-sm);
            color: #742a2a;
        }

        /* ---- FOOTER ---- */
        .footer-sig {
            background: var(--text);
            color: rgba(255,255,255,.65);
            font-size: .8rem;
            padding: 1rem 1.5rem;
            margin-top: 2rem;
            text-align: center;
        }
        .footer-sig a { color: rgba(255,255,255,.8); text-decoration: none; }

        /* ---- PROGRESS BAR CUSTOM ---- */
        .progress-sig {
            height: 6px;
            border-radius: 3px;
            background: var(--border);
        }
        .progress-sig .progress-bar {
            border-radius: 3px;
            background: var(--primary);
        }

        /* ---- PAGE HERO ---- */
        .page-hero {
            background: linear-gradient(135deg, var(--primary-dk) 0%, var(--primary) 70%, #1a9970 100%);
            color: #fff;
            padding: 1.4rem 1.5rem 1.2rem;
            border-radius: 0;
            margin-bottom: 1.5rem;
        }
        .page-hero h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: .2rem;
        }
        .page-hero p { color: rgba(255,255,255,.75); font-size: .9rem; margin-bottom: 0; }

        /* ---- UTILITY ---- */
        .text-primary-sig { color: var(--primary) !important; }
        .bg-primary-sig { background: var(--primary) !important; }
        .bg-primary-lt  { background: var(--primary-lt) !important; }
        .rounded-sig    { border-radius: var(--radius-md) !important; }

        .leaflet-popup-content-wrapper {
            border-radius: 12px !important;
            box-shadow: var(--shadow-lg) !important;
        }
        .leaflet-popup-content { margin: 0 !important; padding: 0 !important; }

        /* Scrollbar sutil */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c1d4cc; border-radius: 4px; }

        /* Transition page */
        main { animation: fadeInUp .35s ease both; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar navbar-expand-lg navbar-sig">
        <div class="container-fluid px-3 px-lg-5">

            <a class="navbar-brand-wrap" href="{{ route('mapa.index') }}">
                <div class="navbar-logo-box">
                    <i class="bi bi-geo-alt-fill text-warning"></i>
                </div>
                <div class="navbar-brand-text">
                    <strong>SIG Lácteos</strong>
                    <span>Cotopaxi · Latacunga</span>
                </div>
            </a>

            <button class="navbar-toggler border-0 text-white" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navMain">
                <i class="bi bi-list fs-4"></i>
            </button>

            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav mx-auto gap-1 mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link-sig {{ request()->routeIs('mapa.*') ? 'active' : '' }}"
                           href="{{ route('mapa.index') }}">
                            <i class="bi bi-map-fill"></i> Mapa de Oportunidades
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-sig {{ request()->routeIs('productos.*') ? 'active' : '' }}"
                           href="{{ route('productos.index') }}">
                            <i class="bi bi-box-seam-fill"></i> Catálogo Emprendedor
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-sig {{ request()->routeIs('oportunidades.*') ? 'active' : '' }}"
                           href="{{ route('oportunidades.index') }}">
                            <i class="bi bi-kanban-fill"></i> Mis Oportunidades
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-sig {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                           href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-shield-lock-fill"></i> Panel Admin
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0">
                    <span class="navbar-badge">
                        <i class="bi bi-shop me-1"></i>162 Comercios
                    </span>
                    <div class="d-none d-lg-flex align-items-center gap-2 ms-1"
                         style="color: rgba(255,255,255,.7); font-size:.82rem;">
                        <i class="bi bi-person-circle fs-5"></i>
                        <span>Carlos Mendoza</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- ===== ALERTAS FLASH ===== -->
    @if(session('success'))
        <div class="container-fluid px-3 px-lg-5 mt-3">
            <div class="alert-sig-success p-3 d-flex align-items-center gap-2 alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="container-fluid px-3 px-lg-5 mt-3">
            <div class="alert-sig-danger p-3 d-flex align-items-center gap-2 alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- ===== CONTENIDO ===== -->
    <main class="pb-4">
        @yield('content')
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="footer-sig">
        <div class="d-flex flex-wrap justify-content-center gap-3 align-items-center">
            <span><i class="bi bi-geo-alt-fill text-warning me-1"></i><strong style="color:#fff">SIG Lácteos Cotopaxi</strong></span>
            <span>·</span>
            <span>Sistema de Información Geográfica Comercial</span>
            <span>·</span>
            <span>Cantón Latacunga © {{ date('Y') }}</span>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
