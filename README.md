# SISCOM-UNET
### Sistema de Comunicación y Reporte para Seguridad Universitaria

SISCOM-UNET digitaliza la comunicación y la gestión operativa del cuerpo de seguridad de la Universidad Nacional Experimental del Táchira (UNET). Reemplaza la coordinación por radio con un chat en tiempo real, reportes de incidentes con evidencia, tareas asignadas y un botón de emergencia. Así quedan documentadas las incidencias y baja el tiempo de respuesta en el campus.

Este repositorio contiene el **backend (API + panel web del supervisor)**. La **app móvil** de guardias y supervisores está hecha en Flutter, en un repositorio aparte, y consume la API de este proyecto.

---

## Módulos

| Módulo | App móvil (API) | Panel web del supervisor (`/admin`) |
|---|---|---|
| **Chat grupal** | Mensajes de texto, imagen, audio y video en tiempo real. Escriben guardias y supervisores | — |
| **Reportes de incidentes** | El guardia reporta tipo, descripción, ubicación y archivo adjunto | Listado paginado, eliminar y vaciar historial (con sus archivos) |
| **Tareas** | El guardia ve sus tareas pendientes y puede crearlas, completarlas y eliminarlas | Asignar tareas con prioridad, historial, eliminar y vaciar |
| **Emergencias** | Botón de pánico (solo una emergencia activa a la vez) y consulta de la emergencia activa | Ver, crear, resolver y asignar a un guardia como tarea de alta prioridad, con aviso en tiempo real |
| **Contactos de emergencia** | Consulta para todos; el supervisor también crea, edita y elimina | CRUD con enlace directo a WhatsApp |
| **Guardias** | El supervisor lista, crea y elimina | CRUD, turnos (Mañana/Tarde/Noche) y cuántos guardias están en turno ahora |
| **Actividad** | Feed con reportes, mensajes y guardias nuevos | Dashboard con estadísticas y gráficos |

---

## Arquitectura

- **Backend:** PHP 8.2+ y Laravel 12.
- **Dos tipos de usuario:**
  - **Supervisores** (tabla `admins`): usan el panel web y la app.
  - **Guardias** (tabla `users`): usan la app.
- **Autenticación de la API:** Laravel Sanctum.
  - `POST /api/login` emite un token con la habilidad `supervisor` o `guard`.
  - Todas las demás rutas exigen el token (`Authorization: Bearer ...`).
  - Las rutas exclusivas de cada rol se protegen con `abilities:supervisor` y `abilities:guard`.
  - El usuario siempre se toma del token, nunca de datos enviados en el cuerpo del pedido.
- **Autenticación del panel web:** sesión propia (guard `admin_web`), separada de la API.
- **Tiempo real:** Laravel Reverb (WebSockets compatibles con Pusher).
  - `guardians-chat`: chat grupal.
  - `user.{id}`: avisos de tareas de emergencia para cada guardia.
  - La app autoriza los canales privados en `POST /api/broadcasting/auth` con su token.
- **Base de datos:** MySQL/MariaDB. Los tests corren en SQLite en memoria.
- **Panel web:** vistas Blade con Bootstrap 5, Chart.js y SweetAlert2 cargados desde CDN.
- **Acceso remoto en desarrollo:** `proxy.cjs` publica la API (`:8000`) y Reverb (`:8080`) en un solo puerto (`:3000`), para exponerlos con un túnel (p. ej. Serveo) y probar la app desde un teléfono.

El mapa comentado de todos los archivos está en [`order.md`](order.md).

---

## Requisitos

- PHP >= 8.2 y Composer 2
- Node.js >= 18 y NPM
- MySQL o MariaDB

---

## Instalación

```bash
git clone https://github.com/alejomm05/SISCOM-UNET.git
cd SISCOM-UNET
composer install
npm install

cp .env.example .env
php artisan key:generate
```

Configura en `.env` la base de datos (`DB_*`) y Reverb:

```dotenv
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=siscom
REVERB_APP_KEY=una-clave
REVERB_APP_SECRET=un-secreto
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
REVERB_SERVER_PATH=
```

`REVERB_SERVER_PATH` debe quedar **vacío**: el cliente de la app se conecta sin prefijo, y con `/socket` Laravel tampoco logra publicar eventos en Reverb.

Crea las tablas y un supervisor (el seeder solo crea un guardia de prueba, `test@example.com` / `password`):

```bash
php artisan migrate --seed
php artisan tinker
>>> App\Models\Admin::create(['name' => 'Supervisor', 'email' => 'supervisor@unet.edu.ve', 'password' => bcrypt('cambia-esta-clave')]);
```

---

## Ejecución

```bash
composer dev            # servidor, cola, logs y Vite al mismo tiempo
php artisan reverb:start  # servidor de WebSockets (en otra terminal)
```

- Panel web: http://127.0.0.1:8000/admin/login
- API: http://127.0.0.1:8000/api

Los eventos en tiempo real se envían a través de la **cola**: sin `php artisan queue:work` (que `composer dev` ya incluye), los mensajes se guardan pero no llegan al instante.

Para probar la app desde un teléfono: levanta `node proxy.cjs`, expón el puerto 3000 con un túnel y ajusta `APP_URL` (con `https://`), `SESSION_DOMAIN` y `SANCTUM_STATEFUL_DOMAINS` al dominio del túnel.

---

## API

| Método y ruta | Quién |
|---|---|
| `POST /api/login` | Público |
| `POST /api/logout`, `GET /api/user` | Autenticado |
| `GET/POST /api/messages`, `GET /api/messages/{id}` | Supervisor y guardia |
| `GET /api/reports/{id}`, `GET /api/activities`, `GET /api/users/{id}` | Supervisor y guardia |
| `GET /api/emergencies/active`, `GET /api/emergency-contacts` | Supervisor y guardia |
| `POST /api/reports`, `POST /api/emergencies` | Guardia |
| `GET/POST /api/tasks`, `PUT/DELETE /api/tasks/{id}` | Guardia (solo sus tareas) |
| `GET/POST /api/users`, `DELETE /api/users/{id}` | Supervisor |
| `POST/PUT/DELETE /api/emergency-contacts/...` | Supervisor |
| `POST /api/broadcasting/auth` | Autenticado (canales privados) |

---

## Tests

```bash
composer test
php artisan test --filter=NombreDelTest
```

---

## Limitaciones conocidas

- Los reportes, las tareas y las emergencias desde la app son solo para guardias; un supervisor recibe 403. Desde el panel web el supervisor sí gestiona todo.
- La app Flutter debe enviar el token en todas las llamadas para funcionar con esta versión de la API.
- El código de invitación para crear guardias desde la API está fijo en `Api/UserController`.
