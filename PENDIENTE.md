# Pendientes de SISCOM-UNET

Lista de trabajo para retomar el proyecto. Estado al 23/10/2026: backend en `main` (commit `16bfdc6`), app Flutter en `~/proyectos/siscom_unet_flutter` **sin cambios**.

> ⚠️ **La app Flutter actual no funciona con la API de `main`.** La API ahora exige el token en casi todas las rutas y la app no lo envía en la mayoría de las llamadas. Antes de desplegar `main`, completa la sección 1.

---

## 1. App Flutter: cambios necesarios (bloqueante)

Todo está en `lib/services/api_service.dart`, salvo donde se indica otro archivo.

### 1.1 Enviar el token en todas las llamadas
Crear un método privado que lea el token de `StorageService().getToken()` y arme los headers (`Authorization: Bearer <token>`, `Accept: application/json`). Usarlo en:

- [ ] `getTasks`
- [ ] `createTask`
- [ ] `completeTask`
- [ ] `deleteTask`
- [ ] `createReport` (es multipart: agregar el header a `request.headers`)
- [ ] `fetchMessages`
- [ ] `sendMessage` (los dos casos: JSON y multipart)
- [ ] `fetchRecentActivities`
- [ ] `fetchActivityDetail`
- [ ] `fetchEmergencyContacts`

Los parámetros `userId` y `email` pueden quedarse por ahora: el backend los ignora y toma al usuario del token. Más adelante se pueden quitar de las firmas y de las pantallas.

### 1.2 Chat
- [ ] `lib/screens/chat_screen.dart:57`: `_currentUserId = 3;` está fijo. Hay que leerlo de `StorageService().getUserId()`.
- [ ] Decidir si un mensaje es propio comparando **`message['user']['id']` y `message['user']['role']`** con el usuario y el rol guardados. Los IDs de supervisores y guardias se repiten (el supervisor #1 y el guardia #1 son personas distintas), así que comparar solo `user_id` no basta. El backend ya envía `user.role` (`guard` o `supervisor`) en el historial, en el detalle y en el evento en tiempo real.
- [ ] Los mensajes de supervisores llegan con `user_id: null` y `admin_id` con valor. La app no debe fallar con eso.

### 1.3 Tiempo real (`lib/services/pusher_service.dart`)
- [ ] Quitar el `_pusher!.connect()` duplicado (líneas 66 y 70).
- [ ] `authUri` se arma sin puerto. Funciona detrás del túnel (443), pero en red local con `:8000` falla. Tomar el puerto de `API_URL`.
- [ ] Suscribirse a `private-user.{id}` (solo guardias) y escuchar el evento `task.assigned`. Mostrar la notificación con `notification_service.dart`. Es el aviso de "emergencia asignada" que envía el panel; hoy no lo escucha nadie.
- [ ] `pubspec.yaml` tiene tres paquetes de Pusher (`pusher_client`, `pusher_channels_flutter`, `dart_pusher_channels`) y solo se usa `pusher_client`. Quitar los que sobran.

### 1.4 Supervisores en la app
- [ ] El supervisor ya puede usar el chat. Reportes, tareas y emergencias son solo para guardias: la API le responde **403**. Ocultar esos módulos en `home_screen.dart` cuando el rol es `supervisor`, o mostrar un mensaje claro.
- [ ] `lib/screens/gestion_guards_screen.dart` no se abre desde ninguna pantalla. Decidir si se agrega al menú del supervisor o se elimina.

### 1.5 Perfil
- [ ] Ya no requiere cambios: `/api/user` devuelve `role` como `supervisor` o `guard`, que es lo que espera `profile_screen.dart`. Solo falta comprobarlo.

---

## 2. Pruebas con la app Flutter

Hacerlas con el backend de `main` y Reverb corriendo (ver "Configuración" más abajo). Probar con **un guardia y un supervisor**, idealmente en dos teléfonos.

**Login y perfil**
- [ ] Iniciar sesión como guardia y como supervisor.
- [ ] El perfil muestra el rol correcto.
- [ ] Al cerrar sesión, el token queda inválido.

**Chat**
- [ ] El guardia envía un mensaje y al supervisor le llega al instante, y viceversa.
- [ ] Enviar imagen, audio y video.
- [ ] Los mensajes propios se ven del lado correcto para cada usuario.
- [ ] Dos guardias distintos ven bien quién escribió cada mensaje.

**Tareas**
- [ ] El guardia crea, completa y borra una tarea propia.
- [ ] No puede borrar una tarea que viene de una emergencia (403).

**Reportes**
- [ ] Crear un reporte con y sin archivo.
- [ ] Verlo en el panel con el tipo, la descripción y la ubicación correctos.

**Emergencias**
- [ ] El guardia activa una emergencia. Un segundo intento mientras hay una activa debe ser rechazado.
- [ ] El supervisor la ve en el panel y la asigna a un guardia.
- [ ] Al guardia asignado le llega el aviso al instante.
- [ ] El supervisor la marca como resuelta.

**Contactos**
- [ ] El guardia solo puede verlos.
- [ ] El supervisor puede crear y eliminar.
- [ ] El enlace de WhatsApp abre.

**Feed de actividad**
- [ ] Carga y el detalle de cada tipo abre.

**Sin token**
- [ ] Con la sesión vencida o borrada, la app vuelve al login y no queda colgada.

---

## 3. Backend: pendientes

- [ ] **Supervisores en reportes, tareas y emergencias desde la app.** Requiere el mismo cambio que se hizo en el chat (`messages.admin_id`), porque `user_id` y `assigned_to` apuntan solo a `users`. Relajar el middleware sin cambiar la base de datos guardaría el ID del supervisor como si fuera de un guardia.
- [ ] **Código de invitación fijo** (`INVITE-GUARD-2025` en `app/Http/Controllers/Api/UserController.php`). Como solo el supervisor crea guardias, se puede eliminar o pasar al `.env`.
- [ ] **Dependencias:** Composer avisa de 53 vulnerabilidades conocidas en 16 paquetes. Ejecutar `composer update` y repetir todas las pruebas.
- [ ] **Tests automáticos:** solo existen los de ejemplo. Pasar a PHPUnit (`tests/Feature`) las pruebas que se hicieron con curl: unas 60 de la API (sin token → 401, permisos por rol, autor tomado del token, canales privados) y las del panel.
- [ ] **`reports.location` se guarda codificado dos veces.** El panel lo muestra bien con `location_text`, pero conviene normalizarlo junto con la app: guardar sin `json_encode` y migrar los datos existentes.
- [ ] **Indicadores decorativos:** `dashboard.blade.php` ("+12%", "+8%", "-3%", "+5%") y `emergencies/index.blade.php` ("18min") tienen valores fijos. Calcularlos o quitarlos.
- [ ] **Limpieza:** las tablas `alerts` y `posts` y la vista `admin/task.blade.php` no se usan.

---

## 4. Configuración al desplegar

- [ ] `php artisan migrate`: hay 2 migraciones nuevas (`shift` puede quedar vacío y `messages.admin_id`).
- [ ] En `.env`:
  - `BROADCAST_CONNECTION=reverb`
  - `REVERB_SERVER_PATH=` **vacío**: con `/socket`, ni la app ni Laravel llegan a Reverb.
  - `APP_URL` con `https://` si se usa el túnel.
  - `SESSION_DOMAIN` y `SANCTUM_STATEFUL_DOMAINS` iguales al dominio con el que se entra al panel.
  - `PANEL_DEMO_EMAIL` y `PANEL_DEMO_PASSWORD` vacías, salvo en una demo.
- [ ] Dejar corriendo `php artisan queue:work`. Los eventos del chat y de las emergencias salen por la cola; sin el worker no llegan en tiempo real.
- [ ] Dejar corriendo `php artisan reverb:start`.
- [ ] **Cambiar las claves** (`APP_KEY`, las de Reverb y la contraseña de la base de datos) si el `.env` que estaba en la carpeta de Drive se compartió.

---

## 5. Portafolio y CV

- [ ] Regenerar `resume-ES.pdf` y `resume-EN.pdf` con el texto corregido de SISCOM (el del sitio ya está actualizado en `messages/*.json`). Quitar "Laravel Policies", "Queue Jobs para tareas pesadas", "Vite" y "arquitectura distribuida".
- [ ] `public/resume.pdf` (no está enlazado, pero es público) repite una línea de SISCOM dentro de la sección de Lockers. Corregirlo o borrarlo.
- [ ] Los archivos `content/projects/*.mdx` y `_registry.ts` del portafolio están vacíos, aunque el README del portafolio los describe como los casos de estudio.
- [ ] Cuando la app Flutter funcione, agregar capturas de la app a la galería (`public/images/projects/siscom-unet/`).
