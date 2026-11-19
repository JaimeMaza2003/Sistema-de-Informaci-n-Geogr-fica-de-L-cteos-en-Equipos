import sqlite3
import csv
import json

def clean_val(v):
    if v is None:
        return ''
    return v.strip()

def parse_bool(v):
    if not v:
        return 0
    v_clean = str(v).lower().strip()
    return 1 if v_clean in ['si', 'sí', 'true', '1', 'yes'] else 0

def normalize_lat(lat_str):
    try:
        val = float(lat_str)
        if val > 0:
            val = -val
        return round(val, 7)
    except:
        return -0.7811922

def normalize_lng(lng_str):
    try:
        val = float(lng_str)
        return round(val, 7)
    except:
        return -78.5753411

def create_sqlite(db_path):
    conn = sqlite3.connect(db_path)
    cur = conn.cursor()

    cur.executescript("""
    DROP TABLE IF EXISTS reportes_establecimiento;
    DROP TABLE IF EXISTS propuestas;
    DROP TABLE IF EXISTS oportunidades;
    DROP TABLE IF EXISTS productos;
    DROP TABLE IF EXISTS categorias;
    DROP TABLE IF EXISTS establecimientos;
    DROP TABLE IF EXISTS users;

    CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL DEFAULT 'emprendedor',
        telefono TEXT,
        nombre_emprendimiento TEXT,
        parroquia TEXT,
        direccion TEXT,
        remember_token TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE categorias (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nombre TEXT NOT NULL UNIQUE,
        descripcion TEXT,
        icono TEXT DEFAULT 'bi-box-seam',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE establecimientos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        numero_encuesta INTEGER,
        nombre_establecimiento TEXT NOT NULL,
        nombre_propietario TEXT,
        tipo_establecimiento TEXT NOT NULL,
        direccion_completa TEXT,
        provincia TEXT DEFAULT 'Cotopaxi',
        canton TEXT DEFAULT 'Latacunga',
        parroquia TEXT NOT NULL,
        barrio_sector TEXT,
        telefono TEXT,
        correo_electronico TEXT,
        horario_atencion TEXT,
        horario_proveedores TEXT DEFAULT 'Lunes a Viernes 08:00 a 12:00',
        clientes_por_dia INTEGER,
        comercializa_lacteos INTEGER DEFAULT 1,
        productos_lacteos TEXT,
        marcas_vende TEXT,
        vende_productores_locales INTEGER DEFAULT 0,
        producto_mayor_demanda TEXT,
        volumen_compra_semanal TEXT,
        frecuencia_abastecimiento TEXT,
        principal_proveedor TEXT,
        tiene_refrigeracion INTEGER DEFAULT 1,
        capacidad_refrigeracion TEXT,
        recepcion_proveedores TEXT DEFAULT 'Recibe propuestas',
        interes_nuevos_proveedores INTEGER DEFAULT 1,
        interes_artesanales INTEGER DEFAULT 1,
        requisitos_ingreso TEXT,
        condiciones_compra TEXT DEFAULT 'Contado y entrega contra pedido',
        tipo_decision TEXT DEFAULT 'sucursal',
        contacto_decision TEXT,
        verificado INTEGER DEFAULT 1,
        reportado INTEGER DEFAULT 0,
        tipo_local TEXT,
        observaciones TEXT,
        latitud REAL NOT NULL,
        longitud REAL NOT NULL,
        fecha_verificacion DATE,
        user_id INTEGER,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    );

    CREATE TABLE productos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        nombre TEXT NOT NULL,
        categoria_id INTEGER,
        categoria_nombre TEXT NOT NULL,
        descripcion TEXT,
        presentacion TEXT NOT NULL,
        precio_comercio REAL NOT NULL,
        precio_sugerido REAL NOT NULL,
        pedido_minimo TEXT NOT NULL,
        capacidad_semanal TEXT NOT NULL,
        vida_util_dias INTEGER DEFAULT 21,
        requiere_refrigeracion INTEGER DEFAULT 1,
        temperatura_conservacion TEXT DEFAULT '4°C a 7°C',
        zonas_entrega TEXT DEFAULT 'Latacunga, Salache, Tanicuchi, Guaytacama, Mulalo, Lasso',
        frecuencia_entrega TEXT DEFAULT 'Semanal (Lunes y Jueves)',
        registro_sanitario TEXT,
        bpm_disponible INTEGER DEFAULT 1,
        ruc_disponible INTEGER DEFAULT 1,
        tabla_nutricional INTEGER DEFAULT 1,
        imagen_url TEXT,
        activo INTEGER DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
    );

    CREATE TABLE oportunidades (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        establecimiento_id INTEGER NOT NULL,
        producto_id INTEGER,
        estado TEXT NOT NULL DEFAULT 'por_contactar',
        motivo_cierre TEXT,
        notas_privadas TEXT,
        fecha_proxima_gestion DATE,
        condiciones_acordadas TEXT,
        monto_primer_pedido REAL,
        fecha_primer_pedido DATE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (establecimiento_id) REFERENCES establecimientos(id) ON DELETE CASCADE,
        FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE SET NULL
    );

    CREATE TABLE propuestas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        establecimiento_id INTEGER NOT NULL,
        oportunidad_id INTEGER,
        mensaje TEXT NOT NULL,
        productos_json TEXT,
        estado TEXT NOT NULL DEFAULT 'enviada',
        respuesta_comercio TEXT,
        fecha_visita_propuesta DATETIME,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (establecimiento_id) REFERENCES establecimientos(id) ON DELETE CASCADE,
        FOREIGN KEY (oportunidad_id) REFERENCES oportunidades(id) ON DELETE SET NULL
    );

    CREATE TABLE reportes_establecimiento (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        establecimiento_id INTEGER NOT NULL,
        tipo_error TEXT NOT NULL,
        descripcion TEXT NOT NULL,
        estado TEXT DEFAULT 'pendiente',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY (establecimiento_id) REFERENCES establecimientos(id) ON DELETE CASCADE
    );
    """)

    # Seed users
    cur.execute("""
    INSERT INTO users (id, name, email, password, role, telefono, nombre_emprendimiento, parroquia, direccion) VALUES
    (1, 'Administrador del Sistema', 'admin@siglacteos.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '0991234567', 'Dirección Distrital Cotopaxi - SIG', 'Latacunga', 'Centro de Latacunga, Calle Sánchez de Orellana'),
    (2, 'Carlos Mendoza (Emprendedor)', 'emprendedor@lacteosandinos.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'emprendedor', '0987654321', 'Lácteos Artesanales del Cotopaxi', 'Tanicuchi', 'Vía a Lasso Km 2, Sector San Pedro'),
    (3, 'María Toapanta (Comercio)', 'maria.toapanta28@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'comercio', '0921819600', 'Abastos Don Manuel', 'Lasso', 'Barrio Chilcapamba, vía a Lasso km 4')
    """)

    # Seed categorias
    categorias = [
        (1, 'Leche Entera y Pasteurizada', 'Leche cruda de hacienda, pasteurizada en funda, UHT y descremada', 'bi-cup-straw'),
        (2, 'Quesos Frescos y Mozzarella', 'Queso de mesa fresco de hoja, mozzarella hilado y quesillo artesanal', 'bi-shield-check'),
        (3, 'Quesos Madurados y Especiales', 'Queso andino madurado, holandés, gouda, dambo y provolone artesanal', 'bi-award'),
        (4, 'Yogurt Bebible y Griego', 'Yogurt natural con pulpa de frutas (mora, durazno, frutilla) y yogurt griego', 'bi-droplet-half'),
        (5, 'Mantequilla y Crema', 'Mantequilla pura de leche pasteurizada con y sin sal, crema de leche pura', 'bi-sun'),
        (6, 'Postres Lácteos y Manjar', 'Dulce de leche tradicional de paila, manjar con trozos y leche condensada', 'bi-stars')
    ]
    cur.executemany("INSERT INTO categorias (id, nombre, descripcion, icono) VALUES (?, ?, ?, ?)", categorias)

    # Seed productos
    productos = [
        (1, 2, 'Queso Andino Artesanal', 2, 'Quesos Frescos y Mozzarella', 'Queso fresco semiduro elaborado con leche 100% de vaca de pasturas del volcán Cotopaxi, textura suave y bajo en sal.', 'Bloque al vacío 500g', 2.30, 3.00, '10 unidades', '250 unidades/semana', 30, 1, '2°C a 6°C', 'Latacunga, Lasso, Tanicuchi, Mulalo, Guaytacama, Salache', 'Martes y Viernes', '1589-ALN-0819', 1, 1, 1, '/images/productos/queso_artesanal.jpg', 1),
        (2, 2, 'Yogurt Natural Probiótico con Mora', 4, 'Yogurt Bebible y Griego', 'Yogurt bebible cremoso con cultivos vivos y fruta natural de productores locales, sin preservantes artificiales.', 'Botella 1 Litro', 1.40, 1.85, '12 unidades (1 canastilla)', '300 litros/semana', 25, 1, '4°C a 7°C', 'Latacunga, Lasso, Tanicuchi, Belisario Quevedo, Salache', 'Lunes, Miércoles y Sábados', '2045-ALN-1020', 1, 1, 1, '/images/productos/yogurt_mora.jpg', 1),
        (3, 2, 'Mantequilla Tradicional de Altura', 5, 'Mantequilla y Crema', 'Mantequilla batida artesanalmente con crema fresca de leche entera, textura untable y aroma lácteo característico.', 'Barra envuelta 250g', 1.60, 2.10, '15 unidades', '150 kg/semana', 45, 1, '2°C a 5°C', 'Latacunga, Toacaso, Lasso, Mulalo', 'Semanal', '0984-ALN-0321', 1, 1, 1, '/images/productos/mantequilla_campo.jpg', 1),
        (4, 2, 'Leche Entera Pasteurizada de Campo', 1, 'Leche Entera y Pasteurizada', 'Leche fresca recién ordeñada y pasteurizada a baja temperatura para conservar nutrientes y sabor original.', 'Funda 1 Litro', 0.68, 0.90, '20 fundas (1 jabilla)', '500 litros/semana', 10, 1, '3°C a 6°C', 'Latacunga, Salache, Tanicuchi, Guaytacama', 'Diario', '3310-ALN-1222', 1, 1, 1, '/images/productos/leche_campo.jpg', 1)
    ]
    cur.executemany("INSERT INTO productos (id, user_id, nombre, categoria_id, categoria_nombre, descripcion, presentacion, precio_comercio, precio_sugerido, pedido_minimo, capacidad_semanal, vida_util_dias, requiere_refrigeracion, temperatura_conservacion, zonas_entrega, frecuencia_entrega, registro_sanitario, bpm_disponible, ruc_disponible, tabla_nutricional, imagen_url, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", productos)

    # Seed 162 establecimientos
    with open('database/establecimientos_raw.csv', 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        raw_rows = list(reader)

    est_records = []
    for i, r in enumerate(raw_rows):
        est_id = i + 1
        num_enc = int(r.get('N°', est_id))
        nombre = clean_val(r.get('Nombre del establecimiento', f'Establecimiento {est_id}'))
        prop = clean_val(r.get('Nombre del propietario/administrador', ''))
        tipo = clean_val(r.get('Tipo de establecimiento', 'Tienda'))
        direccion = clean_val(r.get('Direccion completa', ''))
        prov = clean_val(r.get('Provincia', 'Cotopaxi')) or 'Cotopaxi'
        canton = clean_val(r.get('Canton', 'Latacunga')) or 'Latacunga'
        parroquia = clean_val(r.get('Parroquia', 'Latacunga')) or 'Latacunga'
        barrio = clean_val(r.get('Barrio o sector', ''))
        telefono = clean_val(r.get('Telefono de contacto', ''))
        if telefono and not telefono.startswith('0') and len(telefono) == 9:
            telefono = '0' + telefono
        correo = clean_val(r.get('Correo electronico', ''))
        if 'no dispone' in correo.lower() or 'no tiene' in correo.lower():
            correo = ''
        horario = clean_val(r.get('Horario de atencion', '08:00 - 20:00'))
        
        try:
            clientes = int(float(clean_val(r.get('N° aprox. clientes/dia', '20')) or 20))
        except:
            clientes = 20

        comercializa = parse_bool(r.get('Comercializa lacteos', 'Si'))
        prods = clean_val(r.get('Productos lacteos que comercializa', ''))
        marcas = clean_val(r.get('Marcas que vende actualmente', ''))
        vende_local = parse_bool(r.get('Vende de productores locales', 'No'))
        demanda = clean_val(r.get('Producto lacteo con mayor demanda', ''))
        volumen = clean_val(r.get('Volumen promedio compra semanal', ''))
        frecuencia = clean_val(r.get('Frecuencia de abastecimiento', 'Semanal'))
        proveedor = clean_val(r.get('Principal proveedor', ''))
        refrig = parse_bool(r.get('Cuenta con refrigeracion', 'Si'))
        cap_refrig = clean_val(r.get('Capacidad almacenamiento refrigerado', ''))
        int_nuevos = parse_bool(r.get('Interes en nuevos proveedores', 'Si'))
        int_art = parse_bool(r.get('Interes en productos artesanales/asociaciones', 'Si'))
        obs = clean_val(r.get('Observaciones', ''))
        tipo_local = clean_val(r.get('Local propio o arrendado', 'Propio'))

        lat = normalize_lat(clean_val(r.get('Latitud (GPS)', '-0.7811922')))
        lng = normalize_lng(clean_val(r.get('Longitud (GPS)', '-78.5753411')))

        tipo_decision = 'oficina_central' if tipo.lower() in ['supermercado', 'distribuidor'] else 'sucursal'
        recepcion = 'Recibe propuestas' if int_nuevos == 1 else 'No recibe por ahora'
        requisitos = 'Factura o nota de venta RUC/RISE, Registro Sanitario vigente, Muestra comercial previa' if tipo.lower() in ['supermercado', 'minimarket'] else 'Muestra previa y acuerdo de entrega semanal'
        condiciones = 'Crédito a 15 días o consignación inicial' if tipo.lower() == 'supermercado' else 'Pago contra entrega (contado)'
        contacto_dec = prop if prop else f'Administración {nombre}'

        est_records.append((
            est_id, num_enc, nombre, prop, tipo, direccion, prov, canton, parroquia, barrio,
            telefono, correo, horario, 'Lunes a Viernes 08:00 a 12:00', clientes, comercializa,
            prods, marcas, vende_local, demanda, volumen, frecuencia, proveedor, refrig,
            cap_refrig, recepcion, int_nuevos, int_art, requisitos, condiciones, tipo_decision,
            contacto_dec, 1, 0, tipo_local, obs, lat, lng, '2026-11-15'
        ))

    cur.executemany("""
    INSERT INTO establecimientos (
        id, numero_encuesta, nombre_establecimiento, nombre_propietario, tipo_establecimiento, direccion_completa, provincia, canton, parroquia, barrio_sector,
        telefono, correo_electronico, horario_atencion, horario_proveedores, clientes_por_dia, comercializa_lacteos,
        productos_lacteos, marcas_vende, vende_productores_locales, producto_mayor_demanda, volumen_compra_semanal, frecuencia_abastecimiento, principal_proveedor, tiene_refrigeracion,
        capacidad_refrigeracion, recepcion_proveedores, interes_nuevos_proveedores, interes_artesanales, requisitos_ingreso, condiciones_compra, tipo_decision,
        contacto_decision, verificado, reportado, tipo_local, observaciones, latitud, longitud, fecha_verificacion
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    """, est_records)

    # Seed oportunidades
    oportunidades = [
        (2, 14, 1, 'punto_activo', 'Punto de venta consolidado. Compra 20 quesos cada martes.', '2026-11-25', 'Pago al contado contra entrega, pedido semanal regular.', 46.00, '2026-10-10'),
        (2, 15, 1, 'primer_pedido', 'Aceptó primer pedido de prueba de 15 quesos y 12 yogurts.', '2026-11-22', 'Consignación por 1 semana, luego pago a 8 días.', 51.30, '2026-11-18'),
        (2, 18, 2, 'en_negociacion', 'Se entregó muestra a doña Carmen. Le gustó el yogurt de mora.', '2026-11-20', 'Negociando descuento del 5% por pedido mayor a 50 litros.', None, None),
        (2, 21, 1, 'muestra_entregada', 'Se dejó catálogo impreso y 2 muestras de queso fresco al señor Manuel.', '2026-11-21', 'Pendiente de degustación con sus clientes habituales.', None, None),
        (2, 26, 2, 'contactado', 'Llamada telefónica a Elena Toaquiza. Interesada en productos de Tanicuchi.', '2026-11-23', 'Agendada visita comercial para el jueves en la mañana.', None, None),
        (2, 27, 1, 'por_contactar', 'Supermercado con alta rotación en Centro Tanicuchi. Requiere cita.', '2026-11-24', 'Llevar portafolio con registro sanitario y BPM.', None, None)
    ]
    cur.executemany("""
    INSERT INTO oportunidades (user_id, establecimiento_id, producto_id, estado, notas_privadas, fecha_proxima_gestion, condiciones_acordadas, monto_primer_pedido, fecha_primer_pedido)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    """, oportunidades)

    conn.commit()
    conn.close()
    print(f"Base de datos SQLite generada exitosamente en {db_path} con {len(est_records)} establecimientos.")

if __name__ == '__main__':
    create_sqlite('database/database.sqlite')
    try:
        create_sqlite('C:/xampp/htdocs/gis-lacteos/database/database.sqlite')
    except Exception as e:
        print("Aviso copia xampp:", e)
