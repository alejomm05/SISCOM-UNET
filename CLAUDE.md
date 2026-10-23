# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

SISCOM-UNET: communication and incident-reporting system for the UNET university security staff (chat, incident reports with geolocation, guard task checklists, emergency alerts, staff administration). Laravel 12 / PHP 8.2 JSON API authenticated with Sanctum, plus a Vite frontend. Code comments, API messages and the README are in Spanish; keep that convention.

Much of what the README describes (Reverb WebSockets, chat, reports, tasks, alerts) exists only as database migrations so far. What is implemented: login/logout, and admin CRUD over guards (`users`).

**`order.md`** (repo root) is the project's annotated file tree. Whenever you add, remove, rename or substantially change the purpose of a file, update `order.md` in the same change.

## Commands

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed

composer dev          # serve + queue:listen + pail logs + vite, concurrently
php artisan serve     # API only
npm run build         # production frontend build

composer test                                   # config:clear + php artisan test
php artisan test --filter=ExampleTest           # single test class/method
php artisan test tests/Feature/ExampleTest.php  # single file

./vendor/bin/pint     # code style (Laravel Pint)
```

Tests use PHPUnit 11 (not Pest) with in-memory SQLite (see `phpunit.xml`). `.env.example` defaults to SQLite; the README targets MySQL/MariaDB.

## Architecture

**Two separate authenticatable models, one login endpoint.** `App\Models\Admin` (table `admins`) and `App\Models\User` (table `users`, the guards) both extend `Authenticatable` and use `HasApiTokens`. `Api\Auth\LoginController@login` checks `admins` first, then `users`, and returns `{token, user, role}` with `role` = `admin` or `guard`. There is no separate auth guard for admins in `config/auth.php`; `auth:sanctum` resolves whichever model owns the token (polymorphic `personal_access_tokens`).

**Authorization checks the model class.** `UserPolicy` methods take any `Authenticatable` and return `$user instanceof Admin`. Don't type-hint `Admin $admin` in policies: Laravel still calls the method with a `User`, which throws a TypeError (500) instead of a 403. "Admins only" is enforced this way: the `admin` route group in `routes/api.php` uses `can:viewAny,App\Models\User`, and the policy is registered via `Gate::policy` in `AppServiceProvider::boot()`. `UserController::destroy` has no `authorize()` call of its own and relies only on the group middleware.

**Guard creation requires an invite code.** `UserController@store` checks a hardcoded `INVITE-GUARD-2025` and stores it in `users.code`.

**Domain tables** (migrations dated `2025_09_14_*`, no models yet): `messages` (sender/receiver → users, `media_url`), `reports` (user, type, JSON `location`, status pending/resolved), `tasks` (`assigned_to` → users, status pending/completed), `alerts` (user, JSON `location`, `destination`, status pending/attended/ignored), `posts` (`author_id` → **admins**).

**Manual test UI:** `resources/views/test-admin.blade.php` (served at `/test-admin`) is a test panel that uses axios against `/api/login` and `/api/admin/users` with the Bearer token.

## Known quirks (check before relying on them)

- `DatabaseSeeder` only creates a test `User` (guard). No admin is seeded, so you have to insert one manually (e.g. via `php artisan tinker`) to use the admin endpoints.
- The README mentions Bootstrap, but `package.json` actually uses Tailwind CSS 4 through `@tailwindcss/vite`.
