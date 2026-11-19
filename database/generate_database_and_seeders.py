import csv
import json
import re

def clean_val(v):
    if v is None:
        return ''
    return v.strip()

def sql_escape(s):
    if s is None:
        return 'NULL'
    s = str(s).replace('\\', '\\\\').replace("'", "''")
    return f"'{s}'"

def parse_bool(v):
    if not v:
        return 0
    v_clean = str(v).lower().strip()
    return 1 if v_clean in ['si', 'sí', 'true', '1', 'yes'] else 0

def normalize_lat(lat_str):
    try:
        val = float(lat_str)
        if val > 0:
            val = -val # Ecuador / Latacunga is negative latitude
        return round(val, 7)
    except:
        return None

def normalize_lng(lng_str):
    try:
        val = float(lng_str)
        return round(val, 7)
    except:
        return None

def normalize_recepcion(interes_nuevos):
    if parse_bool(interes_nuevos):
        return 'Recibe propuestas'
    return 'No recibe por ahora'

def main():
    with open('database/establecimientos_raw.csv', 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        raw_rows = list(reader)

    print(f"Leídos {len(raw_rows)} establecimientos")

    sql_lines = []
    sql_lines.append("-- ========================================================")
    sql_lines.append("-- SISTEMA DE INFORMACION GEOGRAFICA DE LACTEOS (SIG-LACTEOS)")
    sql_lines.append("-- CANTON LATACUNGA - PROVINCIA DE COTOPAXI")
    sql_lines.append("-- Base de datos unificada con 162 establecimientos encuestados")
    sql_lines.append("-- Compatible con MySQL 5.7+ y MariaDB 10+")
    sql_lines.append("-- ========================================================\n")
    sql_lines.append("CREATE DATABASE IF NOT EXISTS `gis_lacteos` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;")
    sql_lines.append("USE `gis_lacteos`;\n")
    sql_lines.append("SET FOREIGN_KEY_CHECKS=0;\n")
    sql_lines.append("DROP TABLE IF EXISTS `reportes_establecimiento`;")
    sql_lines.append("DROP TABLE IF EXISTS `propuestas`;")
    sql_lines.append("DROP TABLE IF EXISTS `oportunidades`;")
    sql_lines.append("DROP TABLE IF EXISTS `productos`;")
    sql_lines.append("DROP TABLE IF EXISTS `categorias`;")
    sql_lines.append("DROP TABLE IF EXISTS `establecimientos`;")
    sql_lines.append("DROP TABLE IF EXISTS `users`;\n")
    sql_lines.append("SET FOREIGN_KEY_CHECKS=1;\n")

    # 1. USERS
    sql_lines.append("""
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(191) NOT NULL,
  `email` VARCHAR(191) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'emprendedor', 'comercio') NOT NULL DEFAULT 'emprendedor',
  `telefono` VARCHAR(50) NULL,
  `nombre_emprendimiento` VARCHAR(191) NULL,
  `parroquia` VARCHAR(100) NULL,
  `direccion` VARCHAR(255) NULL,
  `remember_token` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
""")

    # 2. CATEGORIAS
    sql_lines.append("""
CREATE TABLE `categorias` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL UNIQUE,
  `descripcion` TEXT NULL,
  `icono` VARCHAR(50) NULL DEFAULT 'bi-box-seam',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
""")

    # 3. ESTABLECIMIENTOS (162 records + extensions for full spec)
    sql_lines.append("""
CREATE TABLE `establecimientos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero_encuesta` INT NULL,
  `nombre_establecimiento` VARCHAR(191) NOT NULL,
  `nombre_propietario` VARCHAR(191) NULL,
  `tipo_establecimiento` VARCHAR(100) NOT NULL, -- Tienda, Minimarket, Supermercado, Panaderia, Distribuidor
  `direccion_completa` VARCHAR(255) NULL,
  `provincia` VARCHAR(100) NOT NULL DEFAULT 'Cotopaxi',
  `canton` VARCHAR(100) NOT NULL DEFAULT 'Latacunga',
  `parroquia` VARCHAR(100) NOT NULL,
  `barrio_sector` VARCHAR(150) NULL,
  `telefono` VARCHAR(50) NULL,
  `correo_electronico` VARCHAR(150) NULL,
  `horario_atencion` VARCHAR(150) NULL,
  `horario_proveedores` VARCHAR(150) NULL DEFAULT 'Lunes a Viernes 08:00 a 12:00',
  `clientes_por_dia` INT NULL,
  `comercializa_lacteos` TINYINT(1) NOT NULL DEFAULT 1,
  `productos_lacteos` VARCHAR(255) NULL,
  `marcas_vende` TEXT NULL,
  `vende_productores_locales` TINYINT(1) NOT NULL DEFAULT 0,
  `producto_mayor_demanda` VARCHAR(150) NULL,
  `volumen_compra_semanal` VARCHAR(150) NULL,
  `frecuencia_abastecimiento` VARCHAR(100) NULL,
  `principal_proveedor` VARCHAR(150) NULL,
  `tiene_refrigeracion` TINYINT(1) NOT NULL DEFAULT 1,
  `capacidad_refrigeracion` VARCHAR(150) NULL,
  `recepcion_proveedores` VARCHAR(50) NOT NULL DEFAULT 'Recibe propuestas', -- Recibe propuestas / No recibe por ahora / Por confirmar
  `interes_nuevos_proveedores` TINYINT(1) NOT NULL DEFAULT 1,
  `interes_artesanales` TINYINT(1) NOT NULL DEFAULT 1,
  `requisitos_ingreso` TEXT NULL,
  `condiciones_compra` VARCHAR(255) NULL DEFAULT 'Contado y entrega contra pedido',
  `tipo_decision` ENUM('sucursal', 'oficina_central') NOT NULL DEFAULT 'sucursal',
  `contacto_decision` VARCHAR(191) NULL,
  `verificado` TINYINT(1) NOT NULL DEFAULT 1,
  `reportado` TINYINT(1) NOT NULL DEFAULT 0,
  `tipo_local` VARCHAR(100) NULL, -- Propio, Arrendado
  `observaciones` TEXT NULL,
  `latitud` DECIMAL(10, 7) NOT NULL,
  `longitud` DECIMAL(10, 7) NOT NULL,
  `fecha_verificacion` DATE NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_parroquia` (`parroquia`),
  KEY `idx_tipo` (`tipo_establecimiento`),
  KEY `idx_coords` (`latitud`, `longitud`),
  CONSTRAINT `fk_est_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
""")

    # 4. PRODUCTOS (Catálogo Emprendedor)
    sql_lines.append("""
CREATE TABLE `productos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `nombre` VARCHAR(191) NOT NULL,
  `categoria_id` BIGINT UNSIGNED NULL,
  `categoria_nombre` VARCHAR(100) NOT NULL,
  `descripcion` TEXT NULL,
  `presentacion` VARCHAR(100) NOT NULL, -- ej: 1 Litro, Tarrina 500g, Bloque 1kg
  `precio_comercio` DECIMAL(10, 2) NOT NULL,
  `precio_sugerido` DECIMAL(10, 2) NOT NULL,
  `pedido_minimo` VARCHAR(100) NOT NULL, -- ej: 10 unidades, 1 jabita
  `capacidad_semanal` VARCHAR(100) NOT NULL, -- ej: 200 unidades/semana
  `vida_util_dias` INT NOT NULL DEFAULT 21,
  `requiere_refrigeracion` TINYINT(1) NOT NULL DEFAULT 1,
  `temperatura_conservacion` VARCHAR(50) NULL DEFAULT '4°C a 7°C',
  `zonas_entrega` VARCHAR(255) NOT NULL DEFAULT 'Latacunga, Salache, Tanicuchi, Guaytacama, Mulalo, Lasso',
  `frecuencia_entrega` VARCHAR(100) NOT NULL DEFAULT 'Semanal (Lunes y Jueves)',
  `registro_sanitario` VARCHAR(100) NULL,
  `bpm_disponible` TINYINT(1) NOT NULL DEFAULT 1,
  `ruc_disponible` TINYINT(1) NOT NULL DEFAULT 1,
  `tabla_nutricional` TINYINT(1) NOT NULL DEFAULT 1,
  `imagen_url` VARCHAR(255) NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_prod_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_prod_cat` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
""")

    # 5. OPORTUNIDADES (CRM Pipeline Emprendedor -> Comercio)
    sql_lines.append("""
CREATE TABLE `oportunidades` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `establecimiento_id` BIGINT UNSIGNED NOT NULL,
  `producto_id` BIGINT UNSIGNED NULL,
  `estado` ENUM(
    'por_contactar',
    'contactado',
    'muestra_entregada',
    'en_negociacion',
    'primer_pedido',
    'punto_activo',
    'no_concretada'
  ) NOT NULL DEFAULT 'por_contactar',
  `motivo_cierre` VARCHAR(255) NULL,
  `notas_privadas` TEXT NULL,
  `fecha_proxima_gestion` DATE NULL,
  `condiciones_acordadas` TEXT NULL,
  `monto_primer_pedido` DECIMAL(10, 2) NULL,
  `fecha_primer_pedido` DATE NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_est_prod` (`user_id`, `establecimiento_id`, `producto_id`),
  CONSTRAINT `fk_op_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_op_est` FOREIGN KEY (`establecimiento_id`) REFERENCES `establecimientos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_op_prod` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
""")

    # 6. PROPUESTAS COMERCIALES
    sql_lines.append("""
CREATE TABLE `propuestas` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `establecimiento_id` BIGINT UNSIGNED NOT NULL,
  `oportunidad_id` BIGINT UNSIGNED NULL,
  `mensaje` TEXT NOT NULL,
  `productos_json` JSON NULL,
  `estado` ENUM('enviada', 'me_interesa', 'solicito_info', 'agendar_visita', 'no_interesado') NOT NULL DEFAULT 'enviada',
  `respuesta_comercio` TEXT NULL,
  `fecha_visita_propuesta` DATETIME NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_prop_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_prop_est` FOREIGN KEY (`establecimiento_id`) REFERENCES `establecimientos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_prop_op` FOREIGN KEY (`oportunidad_id`) REFERENCES `oportunidades` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
""")

    # 7. REPORTES DE ESTABLECIMIENTO
    sql_lines.append("""
CREATE TABLE `reportes_establecimiento` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NULL,
  `establecimiento_id` BIGINT UNSIGNED NOT NULL,
  `tipo_error` VARCHAR(100) NOT NULL, -- Telefono incorrecto, Negocio cerrado, Ya no recibe lacteos, Ubicacion errada
  `descripcion` TEXT NOT NULL,
  `estado` ENUM('pendiente', 'revisado', 'corregido', 'descartado') NOT NULL DEFAULT 'pendiente',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_rep_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_rep_est` FOREIGN KEY (`establecimiento_id`) REFERENCES `establecimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
""")

    # INSERTS: USERS
    # Passwords hashed with bcrypt: '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' is 'password' in standard Laravel
    sql_lines.append("""
-- ========================================================
-- USUARIOS INICIALES (Contraseña de todos: 'password')
-- ========================================================
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `telefono`, `nombre_emprendimiento`, `parroquia`, `direccion`) VALUES
(1, 'Administrador del Sistema', 'admin@siglacteos.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '0991234567', 'Dirección Distrital Cotopaxi - SIG', 'Latacunga', 'Centro de Latacunga, Calle Sánchez de Orellana'),
(2, 'Carlos Mendoza (Emprendedor)', 'emprendedor@lacteosandinos.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'emprendedor', '0987654321', 'Lácteos Artesanales del Cotopaxi', 'Tanicuchi', 'Vía a Lasso Km 2, Sector San Pedro'),
(3, 'María Toapanta (Comercio)', 'maria.toapanta28@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'comercio', '0921819600', 'Abastos Don Manuel', 'Lasso', 'Barrio Chilcapamba, vía a Lasso km 4');
""")

    # INSERTS: CATEGORIAS
    sql_lines.append("""
-- ========================================================
-- CATEGORIAS DE PRODUCTOS LACTEOS
-- ========================================================
INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `icono`) VALUES
(1, 'Leche Entera y Pasteurizada', 'Leche cruda de hacienda, pasteurizada en funda, UHT y descremada', 'bi-cup-straw'),
(2, 'Quesos Frescos y Mozzarella', 'Queso de mesa fresco de hoja, mozzarella hilado y quesillo artesanal', 'bi-shield-check'),
(3, 'Quesos Madurados y Especiales', 'Queso andino madurado, holandés, gouda, dambo y provolone artesanal', 'bi-award'),
(4, 'Yogurt Bebible y Griego', 'Yogurt natural con pulpa de frutas (mora, durazno, frutilla) y yogurt griego', 'bi-droplet-half'),
(5, 'Mantequilla y Crema', 'Mantequilla pura de leche pasteurizada con y sin sal, crema de leche pura', 'bi-sun'),
(6, 'Postres Lácteos y Manjar', 'Dulce de leche tradicional de paila, manjar con trozos y leche condensada', 'bi-stars');
""")

    # INSERTS: PRODUCTOS DE PRUEBA
    sql_lines.append("""
-- ========================================================
-- PRODUCTOS DEL EMPRENDEDOR (Catálogo inicial)
-- ========================================================
INSERT INTO `productos` (`id`, `user_id`, `nombre`, `categoria_id`, `categoria_nombre`, `descripcion`, `presentacion`, `precio_comercio`, `precio_sugerido`, `pedido_minimo`, `capacidad_semanal`, `vida_util_dias`, `requiere_refrigeracion`, `temperatura_conservacion`, `zonas_entrega`, `frecuencia_entrega`, `registro_sanitario`, `bpm_disponible`, `ruc_disponible`, `tabla_nutricional`, `imagen_url`, `activo`) VALUES
(1, 2, 'Queso Andino Artesanal', 2, 'Quesos Frescos y Mozzarella', 'Queso fresco semiduro elaborado con leche 100% de vaca de pasturas del volcán Cotopaxi, textura suave y bajo en sal.', 'Bloque al vacío 500g', 2.30, 3.00, '10 unidades', '250 unidades/semana', 30, 1, '2°C a 6°C', 'Latacunga, Lasso, Tanicuchi, Mulalo, Guaytacama, Salache', 'Martes y Viernes', '1589-ALN-0819', 1, 1, 1, '/images/productos/queso_artesanal.jpg', 1),
(2, 2, 'Yogurt Natural Probiótico con Mora', 4, 'Yogurt Bebible y Griego', 'Yogurt bebible cremoso con cultivos vivos y fruta natural de productores locales, sin preservantes artificiales.', 'Botella 1 Litro', 1.40, 1.85, '12 unidades (1 canastilla)', '300 litros/semana', 25, 1, '4°C a 7°C', 'Latacunga, Lasso, Tanicuchi, Belisario Quevedo, Salache', 'Lunes, Miércoles y Sábados', '2045-ALN-1020', 1, 1, 1, '/images/productos/yogurt_mora.jpg', 1),
(3, 2, 'Mantequilla Tradicional de Altura', 5, 'Mantequilla y Crema', 'Mantequilla batida artesanalmente con crema fresca de leche entera, textura untable y aroma lácteo característico.', 'Barra envuelta 250g', 1.60, 2.10, '15 unidades', '150 kg/semana', 45, 1, '2°C a 5°C', 'Latacunga, Toacaso, Lasso, Mulalo', 'Semanal', '0984-ALN-0321', 1, 1, 1, '/images/productos/mantequilla_campo.jpg', 1),
(4, 2, 'Leche Entera Pasteurizada de Campo', 1, 'Leche Entera y Pasteurizada', 'Leche fresca recién ordeñada y pasteurizada a baja temperatura para conservar nutrientes y sabor original.', 'Funda 1 Litro', 0.68, 0.90, '20 fundas (1 jabilla)', '500 litros/semana', 10, 1, '3°C a 6°C', 'Latacunga, Salache, Tanicuchi, Guaytacama', 'Diario', '3310-ALN-1222', 1, 1, 1, '/images/productos/leche_campo.jpg', 1);
""")

    # INSERTS: 162 ESTABLECIMIENTOS
    sql_lines.append("-- ========================================================")
    sql_lines.append("-- 162 ESTABLECIMIENTOS COMERCIALIZADORES DE LACTEOS (COTOPAXI)")
    sql_lines.append("-- ========================================================")
    sql_lines.append("INSERT INTO `establecimientos` (`id`, `numero_encuesta`, `nombre_establecimiento`, `nombre_propietario`, `tipo_establecimiento`, `direccion_completa`, `provincia`, `canton`, `parroquia`, `barrio_sector`, `telefono`, `correo_electronico`, `horario_atencion`, `horario_proveedores`, `clientes_por_dia`, `comercializa_lacteos`, `productos_lacteos`, `marcas_vende`, `vende_productores_locales`, `producto_mayor_demanda`, `volumen_compra_semanal`, `frecuencia_abastecimiento`, `principal_proveedor`, `tiene_refrigeracion`, `capacidad_refrigeracion`, `recepcion_proveedores`, `interes_nuevos_proveedores`, `interes_artesanales`, `requisitos_ingreso`, `condiciones_compra`, `tipo_decision`, `contacto_decision`, `verificado`, `reportado`, `tipo_local`, `observaciones`, `latitud`, `longitud`, `fecha_verificacion`) VALUES")

    insert_values = []
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

        # Special conditions for supermarket / decisions
        tipo_decision = 'oficina_central' if tipo.lower() in ['supermercado', 'distribuidor'] else 'sucursal'
        recepcion = 'Recibe propuestas' if int_nuevos == 1 else 'No recibe por ahora'
        requisitos = 'Factura o nota de venta RUC/RISE, Registro Sanitario vigente, Muestra comercial previa' if tipo.lower() in ['supermercado', 'minimarket'] else 'Muestra previa y acuerdo de entrega semanal'
        condiciones = 'Crédito a 15 días o consignación inicial' if tipo.lower() == 'supermercado' else 'Pago contra entrega (contado)'
        contacto_dec = prop if prop else f'Administración {nombre}'

        val_tuple = f"({est_id}, {num_enc}, {sql_escape(nombre)}, {sql_escape(prop)}, {sql_escape(tipo)}, {sql_escape(direccion)}, {sql_escape(prov)}, {sql_escape(canton)}, {sql_escape(parroquia)}, {sql_escape(barrio)}, {sql_escape(telefono)}, {sql_escape(correo)}, {sql_escape(horario)}, 'Lunes a Viernes 08:00 a 12:00', {clientes}, {comercializa}, {sql_escape(prods)}, {sql_escape(marcas)}, {vende_local}, {sql_escape(demanda)}, {sql_escape(volumen)}, {sql_escape(frecuencia)}, {sql_escape(proveedor)}, {refrig}, {sql_escape(cap_refrig)}, {sql_escape(recepcion)}, {int_nuevos}, {int_art}, {sql_escape(requisitos)}, {sql_escape(condiciones)}, '{tipo_decision}', {sql_escape(contacto_dec)}, 1, 0, {sql_escape(tipo_local)}, {sql_escape(obs)}, {lat}, {lng}, '2026-11-15')"
        insert_values.append(val_tuple)

    sql_lines.append(",\n".join(insert_values) + ";\n")

    # INSERTS: OPORTUNIDADES INICIALES DEL EMPRENDEDOR (DEMO CRM PIPELINE)
    sql_lines.append("""
-- ========================================================
-- OPORTUNIDADES INICIALES (Pipeline CRM de Demostración)
-- ========================================================
INSERT INTO `oportunidades` (`user_id`, `establecimiento_id`, `producto_id`, `estado`, `notas_privadas`, `fecha_proxima_gestion`, `condiciones_acordadas`, `monto_primer_pedido`, `fecha_primer_pedido`) VALUES
(2, 14, 1, 'punto_activo', 'Punto de venta consolidado. Compra 20 quesos cada martes.', '2026-11-25', 'Pago al contado contra entrega, pedido semanal regular.', 46.00, '2026-10-10'),
(2, 15, 1, 'primer_pedido', 'Aceptó primer pedido de prueba de 15 quesos y 12 yogurts.', '2026-11-22', 'Consignación por 1 semana, luego pago a 8 días.', 51.30, '2026-11-18'),
(2, 18, 2, 'en_negociacion', 'Se entregó muestra a doña Carmen. Le gustó el yogurt de mora.', '2026-11-20', 'Negociando descuento del 5% por pedido mayor a 50 litros.', NULL, NULL),
(2, 21, 1, 'muestra_entregada', 'Se dejó catálogo impreso y 2 muestras de queso fresco al señor Manuel.', '2026-11-21', 'Pendiente de degustación con sus clientes habituales.', NULL, NULL),
(2, 26, 2, 'contactado', 'Llamada telefónica a Elena Toaquiza. Interesada en productos de Tanicuchi.', '2026-11-23', 'Agendada visita comercial para el jueves en la mañana.', NULL, NULL),
(2, 27, 1, 'por_contactar', 'Supermercado con alta rotación en Centro Tanicuchi. Requiere cita.', '2026-11-24', 'Llevar portafolio con registro sanitario y BPM.', NULL, NULL);
""")

    # Write SQL to file
    with open('database/sig_lacteos.sql', 'w', encoding='utf-8') as f:
        f.write("\n".join(sql_lines))

    print(f"Archivo 'database/sig_lacteos.sql' generado exitosamente con {len(raw_rows)} establecimientos y estructura completa.")

if __name__ == '__main__':
    main()
