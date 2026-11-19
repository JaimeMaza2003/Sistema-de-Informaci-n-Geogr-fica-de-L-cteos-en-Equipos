# Sistema de Información Geográfica de Lácteos (SIG-Lácteos) — Cotopaxi

Aplicación web de geolocalización y prospección comercial que conecta a emprendedores y productores lácteos con tiendas de barrio, minimercados y supermercados en el **Cantón Latacunga (Provincia de Cotopaxi, Ecuador)**.

Incluye la **base de datos unificada de los 162 establecimientos encuestados en campo** (Mulaló, Guaytacama, Lasso, Tanicuchi, Belisario Quevedo, Salache, Toacaso y Pastocalle).

---

## 🚀 Inicio Rápido

### 1. Base de Datos (MySQL / phpMyAdmin)
Se incluye el archivo SQL listo para importar en MySQL/phpMyAdmin:
- **Ruta:** [`database/sig_lacteos.sql`](file:///c:/Users/apila/Sistema-de-Informaci-n-Geogr-fica-de-L-cteos-en-Equipos/database/sig_lacteos.sql)
- **Importación:**
  1. Inicia **Apache** y **MySQL** en el panel de control de XAMPP.
  2. Abre en tu navegador [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
  3. Crea una base de datos llamada `gis_lacteos` (o el script la crea automáticamente).
  4. Ve a la pestaña **Importar**, selecciona [`database/sig_lacteos.sql`](file:///c:/Users/apila/Sistema-de-Informaci-n-Geogr-fica-de-L-cteos-en-Equipos/database/sig_lacteos.sql) y pulsa **Continuar**.

> **Nota:** También se incluye una base de datos SQLite preconfigurada en [`database/database.sqlite`](file:///c:/Users/apila/Sistema-de-Informaci-n-Geogr-fica-de-L-cteos-en-Equipos/database/database.sqlite) que funciona de inmediato sin necesidad de encender MySQL.

### 2. Ejecutar la Aplicación

#### Opción A: Ejecución Directa Inmediata (Servidor PHP integrado)
Puedes iniciar la aplicación directamente desde la terminal con el ejecutable de PHP de XAMPP:
```powershell
& "C:\xampp\php\php.exe" -S localhost:8000 -t public
```
Abre en tu navegador: **[http://localhost:8000](http://localhost:8000)**

#### Opción B: Con Composer y Laravel Artisan
```powershell
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

---

## 👥 Usuarios de Demostración (Contraseña: `password`)

| Rol | Correo Electrónico | Descripción |
| :--- | :--- | :--- |
| **Administrador** | `admin@siglacteos.com` | Auditoría, verificación de comercios, resolución de reportes |
| **Emprendedor** | `emprendedor@lacteosandinos.com` | Catálogo de lácteos, búsqueda de compatibilidad, CRM de oportunidades |
| **Comercio** | `maria.toapanta28@gmail.com` | Dueña de *Abastos Don Manuel* (Lasso), recepción de propuestas |

---

## 📋 Cumplimiento de los 10 Objetivos del Sistema

### 1. Tipos de Usuarios y Roles
- **Emprendedor:** Gestión de catálogo propio, prospección en mapa, envío de propuestas comerciales y seguimiento en CRM.
- **Comercio:** Perfil del establecimiento, requerimientos de lácteos, recepción de proveedores y respuesta a propuestas.
- **Administrador:** Panel de auditoría, verificación de comercios, detector de duplicados y revisión de reportes.

### 2. Pantalla Principal: Mapa de Oportunidades (`/mapa`)
- **Marcadores diferenciados:** Colores e íconos específicos para *Tienda*, *Minimarket*, *Supermercado*, *Panadería* y *Distribuidor*.
- **Agrupamiento inteligente:** Clusterización mediante Leaflet / Google Maps API para navegar con fluidez por los 162 puntos de Latacunga.
- **Filtros avanzados:** Por parroquia, tipo de local, categoría de lácteo, recepción de proveedores e infraestructura de frío.
- **Alternador de vista:** Cambio dinámico entre vista de **Mapa Interactivo** y vista de **Listado en Tarjetas**.
- **Acceso directo:** Botón "Ver Ficha" y botón **"Cómo llegar"** conectado directamente a Google Maps Navigation.

### 3. Ficha Completa del Establecimiento (`/establecimientos/{id}`)
- Nombre comercial, fotografía, tipología y verificación oficial.
- Dirección detallada y mapa de punto exacto.
- Horarios de atención al público y horario exclusivo para atención a proveedores.
- Requisitos de ingreso comercial (muestras, facturación, registro).
- Condiciones de compra (contado, crédito, consignación) y capacidad de almacenamiento refrigerado.
- Indication para supermercados si la decisión es en *Sucursal* o en *Oficina Central / Matriz*.
- Botón directo de **Contacto por WhatsApp** con mensaje predeterminado y botón de llamada telefónica.

### 4. Perfil del Emprendedor y Catálogo (`/productos`)
- Registro de productos: Nombre, categoría, presentación, precio comercio, PVP sugerido, pedido mínimo, capacidad semanal, vida útil y cadena de frío.
- Documentos disponibles: Registro sanitario, BPM, RUC y tabla nutricional.
- **Ficha Comercial en PDF:** Vista técnica lista para imprimir o descargar en PDF (`/productos/{id}/ficha-pdf`).
- **Compartir Catálogo:** Enlace público accesible para comerciantes (`/catalogo-publico/{userId}`).

### 5. Algoritmo de Vinculación y Compatibilidad (`/productos/{id}/compatibles`)
Botón **"Buscar dónde ofrecerlo"** en cada producto lácteo:
- Calcula afinidad considerando:
  1. *Categoría:* Si el comercio vende o tiene demanda de ese producto (leche, queso, yogurt, mantequilla).
  2. *Zona de cobertura:* Si el comercio está dentro de las rutas del emprendedor.
  3. *Recepción:* Si el comercio confirmó interés en nuevos proveedores y productos artesanales.
  4. *Cadena de frío:* Si el producto requiere refrigeración y el local cuenta con equipos.
- **Explicación clara de coincidencia:** Muestra un badge explicativo (ej. *"Vende productos de tu categoría, cuenta con refrigeración adecuada, está en tu zona y recibe propuestas"*).
- Advertencia explícita: La compatibilidad es sugerencia algorítmica sin garantía de aceptación.

### 6. Contacto y Presentación de Propuestas
- **Guardar oportunidad:** Agrega el establecimiento al embudo de ventas del emprendedor.
- **Contactar al comercio:** Vía WhatsApp directo o llamada.
- **Enviar propuesta:** Modal para seleccionar productos del catálogo, indicar volúmenes y redactar mensaje comercial.
- **Respuestas del comercio:** Opciones integradas: *Me interesa*, *Solicito información*, *Agendar visita*, *No interesado por ahora*.

### 7. Seguimiento de Oportunidades CRM (`/mis-oportunidades`)
Tablero visual tipo Kanban con las 7 etapas del ciclo comercial:
1. `Por contactar`
2. `Contactado`
3. `Muestra entregada`
4. `En negociación`
5. `Primer pedido`
6. `Punto de venta activo`
7. `No concretada` (con registro de motivo de cierre)
- Métricas clave en cabecera: Total de oportunidades, primeros pedidos conseguidos, puntos de venta activos y montos facturados.
- Notas privadas del emprendedor, fechas de próxima gestión y condiciones pactadas.

### 8. Registro y Calidad Geográfica
- **Detector de Duplicados:** Valida por nombre similar, dirección o número de teléfono antes de registrar.
- **Botón "Reportar información incorrecta":** Modal en cada comercio para reportar teléfonos erróneos, cierres o traslados.
- **Corrección en Mapa:** El administrador puede ajustar coordenadas de latitud y longitud directamente.
- **Distintivo de verificación:** Identifica datos verificados en campo con fecha de actualización.

### 9. Condiciones Técnicas
- **Framework:** Laravel (con soporte PHP 7.4, 8.0, 8.1, 8.2).
- **Frontend:** Bootstrap 5, Bootstrap Icons, Leaflet.js con OpenStreetMap y soporte para Google Maps API.
- **PWA:** Archivo [`public/manifest.json`](file:///c:/Users/apila/Sistema-de-Informaci-n-Geogr-fica-de-L-cteos-en-Equipos/public/manifest.json) configurado para instalación en dispositivos móviles.
- **Responsive:** Adaptable a celulares, tablets y computadoras de escritorio.

---

## 📁 Estructura del Proyecto

```text
├── app/
│   ├── Http/Controllers/
│   │   ├── AdminController.php             # Auditoría y verificación
│   │   ├── EstablecimientoController.php   # Mapa y fichas
│   │   ├── OportunidadController.php       # CRM y embudo de ventas
│   │   ├── ProductoController.php          # Catálogo y algoritmo de compatibilidad
│   │   └── PropuestaController.php         # Propuestas comerciales
│   └── Models/
│       ├── Categoria.php
│       ├── Establecimiento.php             # 162 registros y cálculo Haversine
│       ├── Oportunidad.php                 # Estados del pipeline
│       ├── Producto.php                    # Algoritmo de vinculación
│       ├── Propuesta.php
│       ├── ReporteEstablecimiento.php
│       └── User.php                        # Roles y autenticación
├── database/
│   ├── sig_lacteos.sql                     # Base de datos completa para MySQL / phpMyAdmin
│   ├── database.sqlite                     # Base de datos SQLite funcional de respaldo
│   ├── establecimientos_raw.csv            # 162 comercios originales en CSV
│   ├── generate_database_and_seeders.py    # Script generador
│   └── seeders/
│       └── EstablecimientoSeeder.php       # Seeder para Laravel artisan
├── public/
│   ├── index.php                           # Front Controller híbrido
│   └── manifest.json                       # Configuración PWA móvil
├── resources/
│   └── views/
│       ├── admin/dashboard.blade.php       # Panel de administración
│       ├── establecimientos/show.blade.php # Ficha detallada del comercio
│       ├── layouts/app.blade.php           # Layout principal responsive
│       ├── mapa/index.blade.php            # Pantalla principal con mapa y filtros
│       ├── oportunidades/index.blade.php   # CRM Mis Oportunidades (Kanban)
│       └── productos/                      # Catálogo, PDF y algoritmo
└── routes/
    ├── web.php                             # Rutas web de la aplicación
    └── api.php                             # Endpoints JSON para el mapa
```
