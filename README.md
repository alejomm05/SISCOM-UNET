# SISCOM-UNET
### Sistema de Comunicación y Reporte para Seguridad Universitaria

SISCOM-UNET es una plataforma de software diseñada para digitalizar, centralizar y optimizar la gestión operativa y la comunicación del cuerpo de seguridad de la Universidad Nacional Experimental del Táchira (UNET). El sistema sustituye los esquemas de radiocomunicación tradicionales por una arquitectura distribuida en tiempo real que mejora la documentación de incidencias y minimiza el tiempo de respuesta ante emergencias en el campus.

---

## Módulos del Sistema

El panel administrativo web implementa las siguientes capacidades operativas:

*   **Comunicación en Tiempo Real:** Chat bidireccional e interactivo con soporte para transmisiones de texto y adjuntos multimedia (imágenes, audio y video) procesados mediante sockets.
*   **Gestión y Reporte de Incidentes:** Formulario de registro de situaciones irregulares que incluye captura de geolocalización, evidencias digitales y auditoría de estados de resolución.
*   **Control de Tareas y Checklists:** Asignación, seguimiento y verificación de hojas de ruta diarias (apertura de accesos, monitoreo de infraestructura) destinadas al personal de guardia.
*   **Protocolos de Emergencia:** Recepción prioritaria de alertas críticas emitidas desde dispositivos móviles y coordinación de enlace operativo con organismos de rescate públicos (Protección Civil).
*   **Administración de Personal:** Control de perfiles de usuario, roles, activación/desactivación de cuentas y asignación estructurada de turnos de vigilancia.

---

## Arquitectura y Stack Tecnológico

El proyecto ha sido estructurado bajo altos estándares de desacoplamiento, escalabilidad y seguridad:

*   **Backend:** PHP 8.2+ y Laravel 12.x bajo el patrón arquitectónico Modelo-Vista-Controlador (MVC).
*   **Seguridad y Control de Acceso:** Autenticación basada en API mediante Laravel Sanctum. Implementación de Políticas de Autorización (Laravel Policies) acopladas a controladores y rutas mediante middlewares específicos para restringir privilegios críticos a nivel de modelo.
*   **Base de Datos:** MySQL con gestión de colas de procesos (Queue Jobs) para la ejecución asíncrona de tareas pesadas.
*   **Tiempo Real:** Laravel Reverb como servidor de WebSockets nativo.
*   **Frontend Web:** JavaScript moderno, Bootstrap y compilación optimizada a través de Vite.

---

## Requisitos de Sistema

*   PHP >= 8.2
*   Composer >= 2.x
*   Node.js >= 18.x
*   NPM >= 9.x
*   Motor de base de datos MySQL o MariaDB

---

## Instrucciones de Instalación y Configuración

Siga las siguientes instrucciones para desplegar un entorno de desarrollo local:

### 1. Clonar el repositorio y configurar dependencias de PHP
```bash
git clone https://github.com/alejomm05/SISCOM-UNET.git
cd SISCOM-UNET
composer install
```

### 2. Configurar el entorno
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Ejecutar migraciones y seeders
```bash
php artisan migrate --seed
```

### 4. Dependencias de frontend
```bash
npm install
npm run dev
```

### 5. Iniciar servidor
```bash
php artisan serve
```

---

## Estructura de Archivos y Directorios del Proyecto

```
SISCOM-UNET/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── Admin/
│   │   │   │   │   └── UserController.php
│   │   │   │   └── Auth/
│   │   │   │       └── LoginController.php
│   │   │   └── Controller.php
│   │   └── (Middleware, Requests, Resources — según necesidad)
│   ├── Models/
│   │   ├── Admin.php
│   │   └── User.php
│   ├── Policies/
│   │   └── UserPolicy.php
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/
│   ├── cache/
│   │   ├── .gitignore
│   │   ├── packages.php
│   │   └── services.php
│   ├── app.php
│   └── providers.php
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── filesystems.php
│   ├── logging.php
│   ├── mail.php
│   ├── queue.php
│   ├── sanctum.php
│   ├── services.php
│   └── session.php
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2025_09_14_205036_create_admins_table.php
│   │   ├── 2025_09_14_205100_create_messages_table.php
│   │   ├── 2025_09_14_205114_create_reports_table.php
│   │   ├── 2025_09_14_205129_create_tasks_table.php
│   │   ├── 2025_09_14_205138_create_alerts_table.php
│   │   ├── 2025_09_14_205323_create_posts_table.php
│   │   ├── 2025_09_14_220747_create_personal_access_tokens_table.php
│   │   └── 2025_09_14_220903_create_admins_table.php
│   ├── seeders/
│   │   └── DatabaseSeeder.php
│   ├── .gitignore
│   └── database.sqlite
├── public/
│   ├── build/
│   │   └── assets/
│   ├── .htaccess
│   ├── favicon.ico
│   ├── hot
│   ├── index.php
│   └── robots.txt
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   ├── app.js
│   │   └── bootstrap.js
│   └── views/
│       ├── test-admin.blade.php
│       └── welcome.blade.php
├── routes/
│   ├── api.php
│   ├── console.php
│   └── web.php
├── storage/
│   ├── app/
│   │   ├── private/
│   │   ├── public/
│   │   │   ├── messages/          # Adjuntos de chat (imágenes, audio, video)
│   │   │   └── reports/           # Evidencias digitales de incidentes
│   │   └── .gitignore
│   ├── framework/
│   └── logs/
├── tests/
│   ├── Feature/
│   │   └── ExampleTest.php
│   ├── Unit/
│   │   └── ExampleTest.php
│   └── TestCase.php
├── .editorconfig
├── .env
├── .env.example
├── .gitattributes
├── .gitignore
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── phpunit.xml
├── vite.config.js
└── README.md
```

### Descripción de Directorios Clave

| Directorio | Propósito |
|---|---|
| `app/Http/Controllers/Api` | Controladores de la API REST (autenticación y administración). |
| `app/Models` | Modelos Eloquent que representan las entidades del dominio (Usuarios, Administradores). |
| `app/Policies` | Políticas de autorización que restringen acciones por rol. |
| `config/` | Archivos de configuración de los servicios de Laravel (BD, cache, colas, Sanctum). |
| `database/migrations` | Esquemas de las tablas del sistema (usuarios, mensajes, reportes, tareas, alertas, publicaciones). |
| `routes/` | Definición de rutas web, API y consola. |
| `resources/views` | Vistas Blade del panel administrativo. |
| `storage/app/public/messages` | Almacenamiento de archivos multimedia adjuntos en el chat. |
| `storage/app/public/reports` | Almacenamiento de evidencias digitales de los reportes de incidentes. |
| `tests/` | Pruebas unitarias y de funcionalidad (PHPUnit). |
