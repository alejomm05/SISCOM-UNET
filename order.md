# Estructura del proyecto SISCOM-UNET

Mapa de los archivos propios del proyecto. Se omiten `vendor/`, `node_modules/`, `storage/`, `bootstrap/cache/`, `public/build/` y los `.gitignore`.

```
SISCOM-UNET/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Controller.php                  # Controlador base
│   │       └── Api/
│   │           ├── Admin/
│   │           │   └── UserController.php      # CRUD de guardias (index, store, destroy), solo admins
│   │           └── Auth/
│   │               └── LoginController.php     # login (admins primero, luego guardias) y logout con Sanctum
│   ├── Models/
│   │   ├── Admin.php                           # Administradores (tabla admins), con tokens Sanctum
│   │   └── User.php                            # Guardias (tabla users), con tokens Sanctum y factory
│   ├── Policies/
│   │   └── UserPolicy.php                      # viewAny/create: solo si el usuario autenticado es Admin
│   └── Providers/
│       └── AppServiceProvider.php              # Registra UserPolicy para el modelo User
├── bootstrap/
│   ├── app.php                                 # Rutas (web, api con prefijo /api, console, /up)
│   └── providers.php
├── config/                                     # Configuración estándar de Laravel + sanctum.php
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php     # users (+ columna code), password_reset_tokens, sessions
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2025_09_14_205036_create_admins_table.php
│   │   ├── 2025_09_14_205100_create_messages_table.php  # chat: sender/receiver → users, media_url
│   │   ├── 2025_09_14_205114_create_reports_table.php   # incidentes: ubicación JSON, estado pending/resolved
│   │   ├── 2025_09_14_205129_create_tasks_table.php     # tareas asignadas a guardias
│   │   ├── 2025_09_14_205138_create_alerts_table.php    # alertas de emergencia
│   │   ├── 2025_09_14_205323_create_posts_table.php     # publicaciones de admins
│   │   └── 2025_09_14_220747_create_personal_access_tokens_table.php  # Sanctum
│   └── seeders/
│       └── DatabaseSeeder.php                  # Crea un guardia de prueba (test@example.com)
├── public/
│   ├── index.php
│   ├── .htaccess
│   ├── favicon.ico
│   └── robots.txt
├── resources/
│   ├── css/app.css                             # Tailwind 4
│   ├── js/
│   │   ├── app.js
│   │   └── bootstrap.js                        # Configuración de axios
│   └── views/
│       ├── welcome.blade.php                   # Página inicial (/)
│       └── test-admin.blade.php                # Panel de prueba de la API (/test-admin)
├── routes/
│   ├── api.php                                 # /api/login, /api/logout, /api/admin/users
│   ├── web.php                                 # /, /test-admin
│   └── console.php
├── tests/
│   ├── TestCase.php
│   ├── Feature/ExampleTest.php
│   └── Unit/ExampleTest.php
├── artisan
├── composer.json                               # Scripts: composer dev, composer test
├── package.json                                # Vite + Tailwind
├── vite.config.js
├── phpunit.xml                                 # Tests con SQLite en memoria
├── .env.example
├── CLAUDE.md                                   # Guía para Claude Code
├── order.md                                    # Este archivo
└── README.md
```
