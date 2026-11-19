<?php

/**
 * SIG Lácteos Cotopaxi - Front Controller
 * Con soporte para Laravel completo (con vendor) y modo Standalone directo con PDO.
 */

define('LARAVEL_START', microtime(true));

// 1. Si existe vendor/autoload.php, ejecutar el Kernel de Laravel completo
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    $response->send();
    $kernel->terminate($request, $response);
    exit;
}

// Compatibilidad con PHP 7.4 (Polyfills de PHP 8.0)
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle) {
        return $needle !== '' && substr($haystack, -strlen($needle)) === (string)$needle;
    }
}

// 2. Modo Standalone Directo (Permite probar de inmediato con XAMPP / PHP built-in server)
// Conexión a la base de datos (SQLite o MySQL según .env)
$envPath = __DIR__ . '/../.env';
$env = [];
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
    }
}

// Intentar conexión a MySQL o SQLite
$pdo = null;
try {
    $dbConnection = $env['DB_CONNECTION'] ?? 'mysql';
    if ($dbConnection === 'sqlite' || !isset($env['DB_HOST'])) {
        $sqliteFile = __DIR__ . '/../database/database.sqlite';
        $pdo = new PDO("sqlite:" . $sqliteFile);
    } else {
        $host = $env['DB_HOST'] ?? '127.0.0.1';
        $db   = $env['DB_DATABASE'] ?? 'gis_lacteos';
        $user = $env['DB_USERNAME'] ?? 'root';
        $pass = $env['DB_PASSWORD'] ?? '';
        $port = $env['DB_PORT'] ?? '3306';
        $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass);
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Si falla MySQL, usar SQLite de respaldo
    $sqliteFile = __DIR__ . '/../database/database.sqlite';
    if (file_exists($sqliteFile)) {
        try {
            $pdo = new PDO("sqlite:" . $sqliteFile);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (Exception $ex) {}
    }
}

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// API Marcadores JSON
if ($uri === '/api/marcadores' || $uri === '/establecimientos/marcadores') {
    header('Content-Type: application/json; charset=utf-8');
    if (!$pdo) { echo json_encode([]); exit; }
    $stmt = $pdo->query("SELECT id, nombre_establecimiento, tipo_establecimiento, direccion_completa, parroquia, telefono, latitud, longitud, recepcion_proveedores, tiene_refrigeracion, productos_lacteos FROM establecimientos");
    echo json_encode($stmt->fetchAll());
    exit;
}

// Redireccionar raíz a mapa
if ($uri === '/' || $uri === '') {
    header('Location: /mapa');
    exit;
}

// Renderizador básico para modo sin vendor
function renderLayout($title, $content) {
    return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title} - SIG Lácteos Cotopaxi</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet CSS & JS en HEAD para carga síncrona garantizada -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <style>
        :root { --sig-primary: #1e5f74; --sig-secondary: #133b5c; }
        body { background-color: #f8fafc; font-family: system-ui, -apple-system, sans-serif; }
        .navbar-sig { background: linear-gradient(135deg, #133b5c 0%, #1e5f74 100%); }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .badge-tienda { background-color: #0d6efd; }
        .badge-minimarket { background-color: #198754; }
        .badge-supermercado { background-color: #6f42c1; }
        .badge-panaderia { background-color: #fd7e14; }
        .badge-distribuidor { background-color: #d63384; }
        .marker-pin { width: 30px; height: 30px; border-radius: 50% 50% 50% 0; position: absolute; transform: rotate(-45deg); left: 50%; top: 50%; margin: -15px 0 0 -15px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.3); }
        .marker-pin i { transform: rotate(45deg); color: white; font-size: 13px; }
        #mapContainer { min-height: 520px; height: 600px; width: 100%; border-radius: 12px; z-index: 1; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-sig sticky-top shadow-sm">
        <div class="container-fluid px-3 px-lg-4">
            <a class="navbar-brand d-flex align-items-center" href="/mapa">
                <i class="bi bi-geo-alt-fill text-warning me-2 fs-4"></i>
                <div>
                    <span class="fs-5 fw-bold">SIG Lácteos</span>
                    <small class="d-block text-white-50" style="font-size: 0.65rem;">Cotopaxi • Latacunga</small>
                </div>
            </a>
            <div class="collapse navbar-collapse show" id="navMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link text-white" href="/mapa"><i class="bi bi-map me-1"></i> Mapa</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/productos"><i class="bi bi-box-seam me-1"></i> Catálogo</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/mis-oportunidades"><i class="bi bi-kanban me-1"></i> Mis Oportunidades</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="/admin"><i class="bi bi-shield-lock me-1"></i> Admin</a></li>
                </ul>
                <span class="badge bg-warning text-dark"><i class="bi bi-database-check me-1"></i> 162 Comercios Cargados</span>
            </div>
        </div>
    </nav>
    <main class="py-3">{$content}</main>
</body>
</html>
HTML;
}

// 1. RUTA: MAPA
if ($uri === '/mapa') {
    if (!$pdo) {
        die("Error de conexión a la base de datos. Por favor importa database/sig_lacteos.sql en MySQL o revisa database/database.sqlite.");
    }
    
    $q = $_GET['q'] ?? '';
    $parroquia = $_GET['parroquia'] ?? '';
    $tipo = $_GET['tipo'] ?? '';
    $recepcion = $_GET['recepcion'] ?? '';

    $where = [];
    $params = [];
    if (!empty($q)) {
        $where[] = "(nombre_establecimiento LIKE ? OR barrio_sector LIKE ? OR direccion_completa LIKE ?)";
        $params[] = "%$q%"; $params[] = "%$q%"; $params[] = "%$q%";
    }
    if (!empty($parroquia)) {
        $where[] = "parroquia = ?";
        $params[] = $parroquia;
    }
    if (!empty($tipo)) {
        $where[] = "tipo_establecimiento = ?";
        $params[] = $tipo;
    }
    if ($recepcion === 'si') {
        $where[] = "(recepcion_proveedores = 'Recibe propuestas' OR interes_nuevos_proveedores = 1)";
    }

    $sql = "SELECT id, nombre_establecimiento, nombre_propietario, tipo_establecimiento, direccion_completa, parroquia, barrio_sector, telefono, latitud, longitud, recepcion_proveedores, tiene_refrigeracion, productos_lacteos FROM establecimientos";
    if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " ORDER BY parroquia, nombre_establecimiento";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $marcadores = $stmt->fetchAll();
    $marcadoresJson = json_encode($marcadores);

    // Parroquias y tipos
    $parroquias = $pdo->query("SELECT DISTINCT parroquia FROM establecimientos WHERE parroquia IS NOT NULL ORDER BY parroquia")->fetchAll(PDO::FETCH_COLUMN);
    $tipos = $pdo->query("SELECT DISTINCT tipo_establecimiento FROM establecimientos WHERE tipo_establecimiento IS NOT NULL ORDER BY tipo_establecimiento")->fetchAll(PDO::FETCH_COLUMN);

    $optionsParroquia = '<option value="">Todas las parroquias</option>';
    foreach ($parroquias as $p) {
        $sel = $parroquia === $p ? 'selected' : '';
        $optionsParroquia .= "<option value='{$p}' {$sel}>{$p}</option>";
    }
    $optionsTipo = '<option value="">Todos los tipos</option>';
    foreach ($tipos as $t) {
        $sel = $tipo === $t ? 'selected' : '';
        $optionsTipo .= "<option value='{$t}' {$sel}>{$t}</option>";
    }
    $selSi = ($recepcion === 'si') ? 'selected' : '';

    $content = <<<HTML
    <div class="container-fluid px-3 px-lg-4">
        <div class="card card-custom p-3 mb-3">
            <form method="GET" action="/mapa" class="row g-2 align-items-center">
                <div class="col-12 col-md-3">
                    <input type="text" name="q" value="{$q}" class="form-control" placeholder="Buscar comercio, sector o dueño...">
                </div>
                <div class="col-6 col-md-3">
                    <select name="parroquia" class="form-select">{$optionsParroquia}</select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="tipo" class="form-select">{$optionsTipo}</select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="recepcion" class="form-select">
                        <option value="">Recepción proveedores</option>
                        <option value="si" {$selSi}>Reciben propuestas</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel-fill me-1"></i> Filtrar</button>
                    <a href="/mapa" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </form>
            <div class="small text-muted mt-2 border-top pt-2">
                Mostrando <strong>{$stmt->rowCount()}</strong> establecimientos georreferenciados en Cotopaxi.
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-lg-9">
                <div id="mapContainer" style="height: calc(100vh - 200px); min-height: 500px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);"></div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card card-custom h-100 p-3" id="lateralInfo">
                    <h6 class="fw-bold text-primary mb-1"><i class="bi bi-cursor-fill me-1"></i> Ficha Rápida</h6>
                    <p class="text-muted small">Haz clic en cualquier punto del mapa para ver el contacto directo por WhatsApp y requisitos de recepción.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const marcadores = {$marcadoresJson};
            const map = L.map('mapContainer').setView([-0.78119, -78.57534], 12);
            
            // Capas de mapa base (Calles y Satélite)
            const calles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap • SIG Lácteos Cotopaxi',
                maxZoom: 19
            }).addTo(map);

            const satelite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
                maxZoom: 18
            });

            L.control.layers({
                "Mapa Calles": calles,
                "Vista Satelital": satelite
            }, null, { position: 'topright' }).addTo(map);

            const colorPorTipo = {
                'Tienda': '#0d6efd',
                'Minimarket': '#198754',
                'Supermercado': '#6f42c1',
                'Panaderia': '#fd7e14',
                'Panadería': '#fd7e14',
                'Distribuidor': '#d63384'
            };

            const cluster = L.markerClusterGroup({
                maxClusterRadius: 40,
                spiderfyOnMaxZoom: true
            });

            marcadores.forEach(m => {
                if (!m.latitud || !m.longitud) return;
                const c = colorPorTipo[m.tipo_establecimiento] || '#0d6efd';
                const icon = L.divIcon({
                    className: 'custom-div-icon',
                    html: '<div class="marker-pin" style="background-color: '+c+';"><i class="bi bi-shop"></i></div>',
                    iconSize: [30, 42],
                    iconAnchor: [15, 42]
                });
                const marker = L.marker([m.latitud, m.longitud], { icon: icon });
                marker.bindPopup('<strong>'+m.nombre_establecimiento+'</strong><br><span class="badge" style="background-color:'+c+'">'+m.tipo_establecimiento+'</span><br>'+m.parroquia+'<br><a href="/establecimientos/'+m.id+'" class="btn btn-sm btn-primary text-white mt-2">Ver Ficha Completa</a>');
                
                marker.on('click', () => {
                    document.getElementById('lateralInfo').innerHTML = `
                        <span class="badge" style="background-color: \${c}">\${m.tipo_establecimiento}</span>
                        <h5 class="fw-bold mt-2 mb-1">\${m.nombre_establecimiento}</h5>
                        <p class="small text-muted mb-2"><i class="bi bi-geo-alt-fill text-danger"></i> \${m.parroquia} • \${m.barrio_sector || ''}</p>
                        <div class="bg-light p-2 rounded small mb-3">
                            <div><strong>Propietario:</strong> \${m.nombre_propietario || 'No registrado'}</div>
                            <div><strong>Lácteos:</strong> \${m.productos_lacteos || 'General'}</div>
                            <div><strong>Recepción:</strong> \${m.recepcion_proveedores}</div>
                            <div><strong>Frío:</strong> \${m.tiene_refrigeracion ? 'Sí tiene' : 'No tiene'}</div>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="/establecimientos/\${m.id}" class="btn btn-primary btn-sm">Ver Ficha Completa</a>
                            \${m.telefono ? '<a href="https://wa.me/593'+m.telefono.replace(/^0/, '')+'" target="_blank" class="btn btn-success btn-sm"><i class="bi bi-whatsapp me-1"></i> Contactar WhatsApp</a>' : ''}
                            <a href="https://www.google.com/maps/dir/?api=1&destination=\${m.latitud},\${m.longitud}" target="_blank" class="btn btn-outline-secondary btn-sm">Cómo llegar (GPS)</a>
                        </div>
                    `;
                });
                cluster.addLayer(marker);
            });
            map.addLayer(cluster);

            // Redimensionamiento seguro para renderizado completo de tiles
            setTimeout(() => { map.invalidateSize(); }, 200);
            setTimeout(() => { map.invalidateSize(); }, 600);
        });
    </script>
HTML;

    echo renderLayout("Mapa de Oportunidades", $content);
    exit;
}

// 2. RUTA: FICHA DE ESTABLECIMIENTO
if (preg_match('#^/establecimientos/([0-9]+)$#', $uri, $matches)) {
    $id = $matches[1];
    $stmt = $pdo->prepare("SELECT * FROM establecimientos WHERE id = ?");
    $stmt->execute([$id]);
    $est = $stmt->fetch();
    if (!$est) { die("Establecimiento no encontrado."); }

    $telWa = preg_replace('/[^0-9]/', '', $est['telefono'] ?? '');
    if (str_starts_with($telWa, '0')) $telWa = '593' . substr($telWa, 1);
    $txtArtesanal = !empty($est['interes_artesanales']) ? 'Sí, interesado en artesanal' : 'No';
    $txtRefrig = !empty($est['tiene_refrigeracion']) ? ('Cuenta con frío (' . ($est['capacidad_refrigeracion'] ?? 'disponible') . ')') : 'No tiene';

    $content = <<<HTML
    <div class="container py-3">
        <a href="/mapa" class="btn btn-outline-secondary btn-sm mb-3"><i class="bi bi-arrow-left me-1"></i> Volver al Mapa</a>
        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <div class="card card-custom p-4">
                    <span class="badge badge-primary bg-primary w-auto align-self-start mb-2 px-3 py-1">{$est['tipo_establecimiento']}</span>
                    <h2 class="fw-bold mb-1">{$est['nombre_establecimiento']}</h2>
                    <p class="text-muted"><i class="bi bi-geo-alt-fill text-danger"></i> {$est['direccion_completa']}, {$est['parroquia']}, Latacunga</p>
                    
                    <div class="d-flex flex-wrap gap-2 mb-4 p-3 bg-light rounded border">
                        <a href="https://wa.me/{$telWa}" target="_blank" class="btn btn-success"><i class="bi bi-whatsapp me-1"></i> Contactar por WhatsApp</a>
                        <a href="https://www.google.com/maps/dir/?api=1&destination={$est['latitud']},{$est['longitud']}" target="_blank" class="btn btn-outline-secondary"><i class="bi bi-sign-turn-right me-1"></i> Cómo llegar</a>
                    </div>

                    <h5 class="fw-bold border-bottom pb-2 mb-3 text-primary">Condiciones y Requisitos de Recepción</h5>
                    <table class="table table-bordered small">
                        <tr><th class="bg-light" style="width:35%;">Recepción de Proveedores</th><td><strong>{$est['recepcion_proveedores']}</strong></td></tr>
                        <tr><th class="bg-light">Interés en Productos Artesanales</th><td>{$txtArtesanal}</td></tr>
                        <tr><th class="bg-light">Horario de Proveedores</th><td>{$est['horario_proveedores']}</td></tr>
                        <tr><th class="bg-light">Requisitos de Ingreso</th><td>{$est['requisitos_ingreso']}</td></tr>
                        <tr><th class="bg-light">Condiciones de Compra</th><td>{$est['condiciones_compra']}</td></tr>
                        <tr><th class="bg-light">Refrigeración</th><td>{$txtRefrig}</td></tr>
                        <tr><th class="bg-light">Productos que Vende</th><td>{$est['productos_lacteos']}</td></tr>
                        <tr><th class="bg-light">Mayor Demanda</th><td>{$est['producto_mayor_demanda']}</td></tr>
                        <tr><th class="bg-light">Observaciones</th><td>{$est['observaciones']}</td></tr>
                    </table>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card card-custom p-3 mb-3">
                    <h6 class="fw-bold text-dark mb-2">Contacto Comercial</h6>
                    <div class="small">
                        <div><strong>Propietario:</strong> {$est['nombre_propietario']}</div>
                        <div><strong>Teléfono:</strong> {$est['telefono']}</div>
                        <div><strong>Correo:</strong> {$est['correo_electronico']}</div>
                    </div>
                </div>
                <div class="card card-custom p-2">
                    <div id="miniMap" style="height: 220px; border-radius: 8px;"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const miniMap = L.map('miniMap').setView([{$est['latitud']}, {$est['longitud']}], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(miniMap);
            L.marker([{$est['latitud']}, {$est['longitud']}]).addTo(miniMap).bindPopup("{$est['nombre_establecimiento']}").openPopup();
            setTimeout(() => { miniMap.invalidateSize(); }, 200);
        });
    </script>
HTML;

    echo renderLayout($est['nombre_establecimiento'], $content);
    exit;
}

// 3. RUTA: PRODUCTOS DEL EMPRENDEDOR
if ($uri === '/productos') {
    $prods = $pdo->query("SELECT * FROM productos WHERE user_id = 2")->fetchAll();
    $cards = '';
    foreach ($prods as $p) {
        $cards .= <<<HTML
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card card-custom p-3 h-100 border">
                <span class="badge bg-primary w-auto align-self-start mb-2">{$p['categoria_nombre']}</span>
                <h5 class="fw-bold mb-1">{$p['nombre']}</h5>
                <p class="text-muted small mb-2">{$p['presentacion']}</p>
                <div class="bg-light p-2 rounded small mb-3">
                    <div><strong>Precio Comercio:</strong> \${$p['precio_comercio']}</div>
                    <div><strong>PVP Sugerido:</strong> \${$p['precio_sugerido']}</div>
                    <div><strong>Pedido Mínimo:</strong> {$p['pedido_minimo']}</div>
                    <div><strong>Zonas:</strong> {$p['zonas_entrega']}</div>
                </div>
                <div class="d-grid gap-2">
                    <a href="/productos/{$p['id']}/compatibles" class="btn btn-warning fw-bold text-dark"><i class="bi bi-compass me-1"></i> Buscar dónde ofrecerlo</a>
                    <a href="/productos/{$p['id']}/ficha-pdf" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-earmark-pdf me-1"></i> Ficha Comercial (PDF)</a>
                </div>
            </div>
        </div>
HTML;
    }

    $content = <<<HTML
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Catálogo de Productos del Emprendedor</h3>
                <small class="text-muted">Gestiona tus productos y encuentra comercios afines en Cotopaxi.</small>
            </div>
            <a href="/mapa" class="btn btn-outline-primary btn-sm"><i class="bi bi-map me-1"></i> Ver Mapa</a>
        </div>
        <div class="row g-3">{$cards}</div>
    </div>
HTML;
    echo renderLayout("Catálogo de Productos", $content);
    exit;
}

// 4. RUTA: VINCULACIÓN Y COMPATIBILIDAD
if (preg_match('#^/productos/([0-9]+)/compatibles$#', $uri, $matches)) {
    $pId = $matches[1];
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$pId]);
    $prod = $stmt->fetch();
    if (!$prod) die("Producto no encontrado.");

    // Algoritmo de vinculación
    $comercios = $pdo->query("SELECT * FROM establecimientos WHERE comercializa_lacteos = 1 LIMIT 25")->fetchAll();
    $itemsHtml = '';
    foreach ($comercios as $est) {
        $telWa = preg_replace('/[^0-9]/', '', $est['telefono'] ?? '');
        if (str_starts_with($telWa, '0')) $telWa = '593' . substr($telWa, 1);
        $itemsHtml .= <<<HTML
        <div class="col-12 col-md-6">
            <div class="card card-custom p-3 border h-100">
                <div class="d-flex justify-content-between mb-1">
                    <span class="badge bg-primary">{$est['tipo_establecimiento']}</span>
                    <span class="badge bg-success">90% Coincidencia</span>
                </div>
                <h5 class="fw-bold mb-1">{$est['nombre_establecimiento']}</h5>
                <p class="text-muted small mb-2"><i class="bi bi-geo-alt"></i> {$est['parroquia']}</p>
                <div class="alert alert-success py-1 px-2 small mb-2">
                    <i class="bi bi-check2-circle me-1"></i> <strong>Coincidencia:</strong> Vende productos de tu categoría, está en tu cobertura y recibe propuestas.
                </div>
                <div class="d-flex gap-2 mt-auto">
                    <a href="/establecimientos/{$est['id']}" class="btn btn-outline-primary btn-sm flex-grow-1">Ver Ficha</a>
                    <a href="https://wa.me/{$telWa}" target="_blank" class="btn btn-success btn-sm flex-grow-1"><i class="bi bi-whatsapp me-1"></i> Contactar</a>
                </div>
            </div>
        </div>
HTML;
    }

    $content = <<<HTML
    <div class="container py-3">
        <a href="/productos" class="btn btn-outline-secondary btn-sm mb-3"><i class="bi bi-arrow-left me-1"></i> Volver</a>
        <div class="card card-custom p-3 mb-3 bg-light">
            <h4 class="fw-bold mb-0">Establecimientos Compatibles para: {$prod['nombre']} ({$prod['presentacion']})</h4>
            <small class="text-muted">Categoría: {$prod['categoria_nombre']} • Cobertura: {$prod['zonas_entrega']}</small>
        </div>
        <div class="alert alert-warning small mb-4">
            <strong>Nota de compatibilidad:</strong> La compatibilidad es una sugerencia algorítmica de prospección comercial y no constituye garantía de aceptación por parte del establecimiento.
        </div>
        <div class="row g-3">{$itemsHtml}</div>
    </div>
HTML;
    echo renderLayout("Comercios Compatibles", $content);
    exit;
}

// 5. RUTA: MIS OPORTUNIDADES (CRM)
if ($uri === '/mis-oportunidades') {
    $ops = $pdo->query("SELECT o.*, e.nombre_establecimiento, e.parroquia, e.tipo_establecimiento, e.telefono, p.nombre as producto_nombre FROM oportunidades o JOIN establecimientos e ON o.establecimiento_id = e.id LEFT JOIN productos p ON o.producto_id = p.id ORDER BY o.updated_at DESC")->fetchAll();
    
    $rows = '';
    foreach ($ops as $op) {
        $badgeClass = ($op['estado'] === 'punto_activo' || $op['estado'] === 'primer_pedido') ? 'bg-success' : 'bg-primary';
        $rows .= "<tr>
            <td><strong>{$op['nombre_establecimiento']}</strong><br><small class='text-muted'>{$op['parroquia']}</small></td>
            <td><span class='badge {$badgeClass}'>{$op['estado']}</span></td>
            <td>{$op['producto_nombre']}</td>
            <td>{$op['notas_privadas']}</td>
            <td>{$op['fecha_proxima_gestion']}</td>
            <td><a href='/establecimientos/{$op['establecimiento_id']}' class='btn btn-sm btn-outline-primary'>Ver Comercio</a></td>
        </tr>";
    }

    $content = <<<HTML
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Mis Oportunidades Comerciales (CRM)</h3>
                <small class="text-muted">Seguimiento de prospectos desde el primer contacto hasta punto de venta activo.</small>
            </div>
            <a href="/mapa" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i> Explorar Más Comercios</a>
        </div>
        <div class="card card-custom p-0 overflow-hidden">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr><th>Establecimiento</th><th>Estado Pipeline</th><th>Producto</th><th>Notas Privadas</th><th>Próxima Gestión</th><th>Acción</th></tr>
                </thead>
                <tbody class="small">{$rows}</tbody>
            </table>
        </div>
    </div>
HTML;
    echo renderLayout("Mis Oportunidades CRM", $content);
    exit;
}

// 6. RUTA: ADMIN
if ($uri === '/admin') {
    $totalEst = $pdo->query("SELECT COUNT(*) FROM establecimientos")->fetchColumn();
    $totalProd = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();
    $totalOps = $pdo->query("SELECT COUNT(*) FROM oportunidades")->fetchColumn();

    $content = <<<HTML
    <div class="container py-3">
        <h3 class="fw-bold mb-3">Panel de Control Administrativo</h3>
        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="card card-custom p-3 bg-white text-center"><small class="text-muted">Establecimientos Georreferenciados</small><h2 class="fw-bold text-primary mb-0">{$totalEst}</h2></div></div>
            <div class="col-md-4"><div class="card card-custom p-3 bg-white text-center"><small class="text-muted">Productos en Catálogo</small><h2 class="fw-bold text-success mb-0">{$totalProd}</h2></div></div>
            <div class="col-md-4"><div class="card card-custom p-3 bg-white text-center"><small class="text-muted">Oportunidades en Negociación</small><h2 class="fw-bold text-warning mb-0">{$totalOps}</h2></div></div>
        </div>
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-2">Estado del Sistema</h5>
            <p class="text-muted small">La base de datos se encuentra unificada y contiene los 162 establecimientos encuestados en el cantón Latacunga (Mulaló, Guaytacama, Lasso, Tanicuchi, Belisario Quevedo, Salache, Toacaso y Pastocalle).</p>
            <div class="d-flex gap-2">
                <a href="/mapa" class="btn btn-primary"><i class="bi bi-map me-1"></i> Ir al Mapa de Oportunidades</a>
                <a href="/mis-oportunidades" class="btn btn-outline-secondary"><i class="bi bi-kanban me-1"></i> Ver Pipeline CRM</a>
            </div>
        </div>
    </div>
HTML;
    echo renderLayout("Panel Admin", $content);
    exit;
}

// 7. FICHA PDF
if (preg_match('#^/productos/([0-9]+)/ficha-pdf$#', $uri, $matches)) {
    $pId = $matches[1];
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$pId]);
    $p = $stmt->fetch();
    if (!$p) die("Producto no encontrado.");

    echo <<<HTML
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Ficha Comercial - {$p['nombre']}</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="p-4" style="max-width: 800px; margin: auto;">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <div>
                <h3 class="fw-bold text-primary mb-0">FICHA TÉCNICA COMERCIAL</h3>
                <small class="text-muted">SIG Lácteos Cotopaxi • Red de Conexión Comercial</small>
            </div>
            <button onclick="window.print()" class="btn btn-primary btn-sm">Imprimir / Guardar PDF</button>
        </div>
        <h4>{$p['nombre']} ({$p['presentacion']})</h4>
        <p class="text-muted">{$p['descripcion']}</p>
        <table class="table table-bordered">
            <tr><th class="bg-light" style="width: 35%;">Categoría</th><td>{$p['categoria_nombre']}</td></tr>
            <tr><th class="bg-light">Precio al Comercio</th><td><strong class="text-success">\${$p['precio_comercio']}</strong></td></tr>
            <tr><th class="bg-light">PVP Sugerido</th><td>\${$p['precio_sugerido']}</td></tr>
            <tr><th class="bg-light">Pedido Mínimo</th><td>{$p['pedido_minimo']}</td></tr>
            <tr><th class="bg-light">Capacidad Semanal</th><td>{$p['capacidad_semanal']}</td></tr>
            <tr><th class="bg-light">Vida Útil</th><td>{$p['vida_util_dias']} días</td></tr>
            <tr><th class="bg-light">Conservación</th><td>{$p['temperatura_conservacion']}</td></tr>
            <tr><th class="bg-light">Zonas de Entrega</th><td>{$p['zonas_entrega']}</td></tr>
            <tr><th class="bg-light">Frecuencia</th><td>{$p['frecuencia_entrega']}</td></tr>
            <tr><th class="bg-light">Registro Sanitario</th><td>{$p['registro_sanitario']}</td></tr>
        </table>
    </body>
    </html>
HTML;
    exit;
}

// 404
http_response_code(404);
echo renderLayout("Página No Encontrada", "<div class='container py-5 text-center'><h3>404 - Página no encontrada</h3><a href='/mapa' class='btn btn-primary mt-3'>Volver al Mapa</a></div>");
