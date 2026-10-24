# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

SISCOM-UNET: communication and incident-reporting system for the UNET university security staff. It has two clients:

- **Flutter mobile app for guards** (separate repo, not here). It consumes the JSON API in `routes/api.php` with Sanctum tokens and real-time events over Laravel Reverb.
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

**API authorization (`routes/api.php`).** Everything except `/login` is inside `auth:sanctum`. Role-specific groups use Sanctum's `abilities:supervisor` / `abilities:guard` middleware (aliases registered in `bootstrap/app.php`). Controllers take the acting user from `$request->user()`. **Never accept `user_id`/`email` from the request body to identify the caller**; the imported code originally did this and it allowed impersonation.

**Real time.** `MessageSent` → private channel `guardians-chat`; `NewEmergencyTaskAssigned` (fired from `EmergencyAdminController@assignTask`) → private channel `user.{guardId}`. Channel rules are in `routes/channels.php`. The Flutter app authorizes via `POST /api/broadcasting/auth` with its Bearer token. `routes/api.php` already has the `/api` prefix, so don't add `prefix => 'api'` to `Broadcast::routes`. The `channels:` entry in `bootstrap/app.php` also registers the default session-based `/broadcasting/auth`.

**Domain flow.** A guard sends a panic alert (`POST /api/emergencies`), and only one emergency can be active (`reported`/`in_progress`). The supervisor sees it in `/admin/emergencies` and assigns it to a guard. That creates a `Task` with `priority=high` and `emergency_id`, marks the emergency `in_progress` and broadcasts to that guard. Resolving sets `resolved` + `attended_at`. The `alerts` and `posts` tables are legacy/unused.

**Tunnel setup.** The project was run behind Serveo/localtunnel for the mobile app. `AppServiceProvider` forces https URLs only when `APP_URL` starts with `https://`. `SESSION_DOMAIN` must match the host you browse with, or the panel login won't stick.

## Gotchas

- Route order matters for literal segments next to `{param}` routes (e.g. `tasks/clear-all` must be declared before `tasks/{task}`).
- `tasks.description` is NOT NULL even though the API treats it as optional (`TaskController` stores `''`).
- With `REVERB_SERVER_PATH=/socket`, Laravel's server-side publish to Reverb fails with "Pusher error: Not found". The Pusher PHP SDK resolves `apps/...` against a base URL without a trailing slash and drops `/socket`. It works with an empty path.
- `.env` is unreadable inside Claude Code's sandbox. Run artisan with explicit env vars (e.g. `APP_KEY`, `DB_*`), or tests that boot the app fail with MissingAppKeyException.
