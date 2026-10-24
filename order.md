# Estructura del proyecto SISCOM-UNET

Mapa de los archivos propios del proyecto. Se omiten `vendor/`, `node_modules/`, `storage/`, `bootstrap/cache/`, `public/build/` y los `.gitignore`.

```
SISCOM-UNET/
├── app/
│   ├── Events/
│   │   ├── MessageSent.php                     # Broadcast al canal privado guardians-chat (chat grupal)
│   │   └── NewEmergencyTaskAssigned.php        # Broadcast a user.{id} cuando se asigna una emergencia
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php                  # Controlador base
│   │   │   ├── Admin/                          # Panel web del supervisor (sesión admin_web)
│   │   │   │   ├── DashboardController.php     # Estadísticas y gráficos
│   │   │   │   ├── GuardiaController.php       # CRUD de guardias + conteo de guardias en turno
│   │   │   │   ├── TaskAdminController.php     # Historial, asignar, borrar y vaciar tareas
│   │   │   │   ├── ReportAdminController.php   # Listado, borrar y vaciar reportes (y sus archivos)
│   │   │   │   ├── EmergencyAdminController.php        # Ver, crear, resolver y asignar emergencias
│   │   │   │   └── EmergencyContactAdminController.php # CRUD de contactos de emergencia
│   │   │   ├── Api/                            # API para la app móvil Flutter (Sanctum)
│   │   │   │   ├── Auth/LoginController.php    # login (admin → 'supervisor', user → 'guard') y logout
│   │   │   │   ├── UserController.php          # Guardias: listar/crear/borrar (supervisor), ver detalle
│   │   │   │   ├── MessageController.php       # Chat: enviar (texto/archivo) y leer historial
│   │   │   │   ├── ReportController.php        # Reportes de incidentes con ubicación y archivo
│   │   │   │   ├── TaskController.php          # Tareas propias del guardia: listar, crear, completar
│   │   │   │   ├── EmergencyController.php     # Botón de pánico (una emergencia activa a la vez)
│   │   │   │   ├── EmergencyContactController.php # Contactos de emergencia
│   │   │   │   └── ActivityController.php      # Feed de actividad (reportes, mensajes, guardias nuevos)
│   │   │   └── Auth/
│   │   │       └── WebLoginController.php      # Login/logout del panel web
│   │   └── Middleware/
│   │       └── Authenticate.php                # Redirige a admin.login cuando no hay sesión
│   ├── Models/
│   │   ├── Admin.php                           # Supervisores (tabla admins)
│   │   ├── User.php                            # Guardias (tabla users): role, shift, reports(), tasks()
│   │   ├── Message.php                         # Mensaje del chat grupal
│   │   ├── Report.php                          # Reporte de incidente
│   │   ├── Task.php                            # Tarea asignada (puede venir de una emergencia)
│   │   ├── Emergency.php                       # Emergencia (reportada por guardia o por el admin)
│   │   └── EmergencyContact.php                # Contacto externo (bomberos, policía, médico...)
│   ├── Policies/
│   │   └── UserPolicy.php                      # viewAny/create: solo si el usuario autenticado es Admin
│   └── Providers/
│       └── AppServiceProvider.php              # Registra UserPolicy; fuerza https si APP_URL es https
├── bootstrap/
│   ├── app.php                                 # Rutas, excepción CSRF de admin/login, alias abilities/ability
│   └── providers.php
├── config/                                     # Configuración de Laravel; propios: auth.php (guards), broadcasting.php, reverb.php, sanctum.php
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php     # users (+ columna code), password_reset_tokens, sessions
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2025_09_14_205036_create_admins_table.php
│   │   ├── 2025_09_14_205100_create_messages_table.php  # Chat (reestructurada por la de 2025_09_28)
│   │   ├── 2025_09_14_205114_create_reports_table.php   # Incidentes: ubicación JSON, estado pending/resolved
│   │   ├── 2025_09_14_205129_create_tasks_table.php     # Tareas asignadas a guardias
│   │   ├── 2025_09_14_205138_create_alerts_table.php    # Sin uso (reemplazada por emergencies)
│   │   ├── 2025_09_14_205323_create_posts_table.php     # Sin uso por ahora
│   │   ├── 2025_09_14_220747_create_personal_access_tokens_table.php  # Sanctum
│   │   ├── 2025_09_28_225715_update_messages_table_for_group_chat.php # messages: user_id, content, file_path, file_type
│   │   ├── 2025_10_09_052045_add_role_to_users_table.php              # users.role (default 'guardia')
│   │   ├── 2025_10_09_063316_create_emergencies_table.php
│   │   ├── 2025_10_09_073020_create_emergency_contacts_table.php
│   │   ├── 2025_10_09_124517_add_priority_to_tasks_table.php          # tasks.priority y tasks.emergency_id
│   │   ├── 2025_10_09_124711_alter_emergencies_for_admin_and_tracking_fields.php # user_id nullable, attended_at
│   │   ├── 2025_10_10_063002_add_shift_to_users_table.php             # users.shift (Mañana/Tarde/Noche)
│   │   └── 2025_10_23_000000_make_shift_nullable_on_users_table.php   # shift nullable en bases existentes
│   └── seeders/
│       └── DatabaseSeeder.php                  # Crea un guardia de prueba (test@example.com)
├── public/
│   ├── index.php
│   ├── .htaccess
│   ├── favicon.ico
│   └── robots.txt
├── resources/
│   ├── css/app.css
│   ├── js/
│   │   ├── app.js
│   │   ├── bootstrap.js                        # axios + importa echo.js
│   │   └── echo.js                             # Laravel Echo con Reverb
│   └── views/
│       ├── welcome.blade.php                   # Página inicial (/)
│       └── admin/                              # Panel web (Bootstrap 5, Chart.js, SweetAlert2 por CDN)
│           ├── layouts/app.blade.php           # Layout con menú lateral
│           ├── auth/login.blade.php
│           ├── dashboard.blade.php
│           ├── guardias/{index,create,edit,show}.blade.php
│           ├── tasks/history.blade.php
│           ├── task.blade.php                  # Sin uso (ninguna ruta la renderiza)
│           ├── reports/index.blade.php
│           ├── emergencies/index.blade.php
│           └── contacts/{index,form}.blade.php
├── routes/
│   ├── api.php                                 # API Flutter: /api/login, rutas por habilidad supervisor/guard, /api/broadcasting/auth
│   ├── web.php                                 # Panel web /admin/* (auth:admin_web)
│   ├── channels.php                            # Canales privados: guardians-chat, user.{userId}
│   └── console.php
├── tests/
│   ├── TestCase.php
│   ├── Feature/ExampleTest.php
│   └── Unit/ExampleTest.php
├── artisan
├── composer.json                               # Scripts: composer dev, composer test
├── package.json                                # Vite, Tailwind, laravel-echo, pusher-js
├── vite.config.js
├── proxy.cjs                                   # Proxy HTTP→:8000 y WS→Reverb :8080 para el túnel de Serveo
├── phpunit.xml                                 # Tests con SQLite en memoria
├── .env.example
├── CLAUDE.md                                   # Guía para Claude Code
├── order.md                                    # Este archivo
└── README.md
```
