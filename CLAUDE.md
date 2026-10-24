# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

SISCOM-UNET: communication and incident-reporting system for the UNET university security staff. It has two clients:

- **Flutter mobile app** (separate repo at `~/proyectos/siscom_unet_flutter`; all HTTP calls are in `lib/services/api_service.dart`, Reverb in `lib/services/pusher_service.dart`). It consumes the JSON API in `routes/api.php` with Sanctum tokens and real-time events over Laravel Reverb. **Both guards and supervisors use the app**, and supervisors also use the web panel. Any API change must be checked against `api_service.dart`.
- **Web panel for the supervisor** under `/admin`. Blade views in `resources/views/admin/` use Bootstrap 5, Chart.js and SweetAlert2 loaded from CDNs (not Vite/Tailwind).

Laravel 12 / PHP 8.2+ (dev machine runs 8.5). Code comments, API messages and docs are in Spanish; keep that convention.

**`order.md`** (repo root) is the project's annotated file tree. Whenever you add, remove, rename or substantially change the purpose of a file, update `order.md` in the same change.

## Commands

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed

composer dev          # serve + queue:listen + pail logs + vite, concurrently
php artisan serve     # web panel + API on :8000
php artisan reverb:start   # WebSocket server on :8080 (needed for chat/emergency events when BROADCAST_CONNECTION=reverb)
node proxy.cjs        # optional: :3000 proxy (HTTP→8000, WS→8080) for the Serveo tunnel

composer test                                   # config:clear + php artisan test
php artisan test --filter=ExampleTest           # single test class/method
./vendor/bin/pint     # code style
```

Tests use PHPUnit 11 with in-memory SQLite (`phpunit.xml`). Production/dev DB is MySQL; code must also work on SQLite (see `ActivityController` for the driver check).

No admin is seeded. Create one with `php artisan tinker`: `App\Models\Admin::create([...])`.

## Architecture

**Two user models, two auth mechanisms.**
- `Admin` (table `admins`) = supervisor. `User` (table `users`, `role` = `guardia`, `shift` = Mañana/Tarde/Noche) = guard. IDs of the two tables overlap, so never compare IDs across them; check `instanceof` instead.
- API: `POST /api/login` tries `admins` then `users` and issues a Sanctum token with ability `supervisor` or `guard`. The `sanctum` guard in `config/auth.php` deliberately has **no provider**: with `provider => users`, Sanctum rejects Admin tokens.
- Web panel: separate session guard `admin_web` (provider `admins`). Login is at `/admin/login` (`WebLoginController`) and is CSRF-exempt in `bootstrap/app.php`.

**API authorization (`routes/api.php`).** Everything except `/login` is inside `auth:sanctum`. `/api/user` returns `role` as `supervisor`/`guard`, the same as `/login` (the DB stores `guardia`). Role-specific groups use Sanctum's `abilities:supervisor` / `abilities:guard` middleware (aliases registered in `bootstrap/app.php`). Controllers take the acting user from `$request->user()`. **Never accept `user_id`/`email` from the request body to identify the caller**; the imported code originally did this and it allowed impersonation.

**Real time.** `MessageSent` → private channel `guardians-chat`; `NewEmergencyTaskAssigned` (fired from `EmergencyAdminController@assignTask`) → private channel `user.{guardId}`. Channel rules are in `routes/channels.php`. The Flutter app authorizes via `POST /api/broadcasting/auth` with its Bearer token. `routes/api.php` already has the `/api` prefix, so don't add `prefix => 'api'` to `Broadcast::routes`. The `channels:` entry in `bootstrap/app.php` also registers the default session-based `/broadcasting/auth`.

**Domain flow.** A guard sends a panic alert (`POST /api/emergencies`), and only one emergency can be active (`reported`/`in_progress`). The supervisor sees it in `/admin/emergencies` and assigns it to a guard. That creates a `Task` with `priority=high` and `emergency_id`, marks the emergency `in_progress` and broadcasts to that guard. Resolving sets `resolved` + `attended_at`. The `alerts` and `posts` tables are legacy/unused.

**Tunnel setup.** The project was run behind Serveo/localtunnel for the mobile app. `AppServiceProvider` forces https URLs only when `APP_URL` starts with `https://`. `SESSION_DOMAIN` must match the host you browse with, or the panel login won't stick.

## Gotchas

- **Chat authors:** a message has exactly one author: `user_id` (guard) or `admin_id` (supervisor). `Message::toArray()` and `MessageSent` always return the author under the `user` key (`id`, `name`, `email`, `role` = `guard`|`supervisor`), because the app reads `message['user']['name']`. Since IDs overlap, the app must compare `user.role` plus `user.id`, not `user_id` alone.
- **Known gap:** reports, tasks and emergencies in the API are still guard-only (`abilities:guard`), because their `user_id`/`assigned_to` reference `users`. Supervisors using the app get 403 there. Supporting them needs the same kind of schema change as `messages.admin_id`; relaxing the middleware alone would save an Admin's ID as a guard's ID.
- **Flutter app out of sync (pending):**
  - Most `ApiService` methods don't send the Bearer token, so they get 401 since the API was secured.
  - `chat_screen.dart` hardcodes `_currentUserId = 3`.
  - The app never subscribes to `private-user.{id}`, so emergency-task notifications don't arrive.
  - The app's Pusher client connects without a path prefix, so `REVERB_SERVER_PATH` must be empty.

- Route order matters for literal segments next to `{param}` routes (e.g. `tasks/clear-all` must be declared before `tasks/{task}`).
- `tasks.description` is NOT NULL even though the API treats it as optional (`TaskController` stores `''`).
- Report types are `sospechoso`, `daño`, `emergencia`, `otro` (validated in `Api\ReportController`). The panel view must use the same values.
- `reports.location` is double-encoded (the controller calls `json_encode` and the model's `json` cast encodes again). The app depends on the current format, so don't change how it's stored. In views, use `$report->location_text`.
- Panel counters (`TaskAdminController`, `ReportAdminController`) are computed over the whole table in `$stats`, not over the paginated page. Task status shown in the panel comes from `Task::display_status` (completed / expired / pending).
- Never hardcode credentials in views. The login demo box only renders when `PANEL_DEMO_EMAIL`/`PANEL_DEMO_PASSWORD` are set (`config('app.demo_login')`).
- Some dashboard card badges (e.g. "+12%", "18min") are static decoration, not real metrics.
- With `REVERB_SERVER_PATH=/socket`, Laravel's server-side publish to Reverb fails with "Pusher error: Not found". The Pusher PHP SDK resolves `apps/...` against a base URL without a trailing slash and drops `/socket`. It works with an empty path.
- `.env` is unreadable inside Claude Code's sandbox. Run artisan with explicit env vars (e.g. `APP_KEY`, `DB_*`), or tests that boot the app fail with MissingAppKeyException.
