## SISARAYA — Copilot / AI agent instructions

This file provides the essential, discoverable knowledge an automated coding agent needs to be productive in this repository.

Keep instructions short and action-oriented. When making changes that affect behavior, update `docs/CHANGELOG.md` with a one-line summary (there's a helper at `php tools/update-docs.php`).

Key facts (quick):
- Framework: Laravel 12 (PHP 8.4+). PSR-4 autoloading with `App\` -> `app/`.
- Frontend: Vite + Tailwind + Alpine.js. Entry points: `resources/css/app.css`, `resources/js/app.js`.
- Default DB for local dev: SQLite (`database/database.sqlite`). Tests use in-memory sqlite (see `phpunit.xml`).
- Permissions: spatie/laravel-permission. Roles and permissions are seeded via `database/seeders` (see `RolePermissionSeeder` / `SisarayaMembersSeeder`).
- Public registration is disabled; users are created by HR via admin UI.

Primary workflows (commands an agent can run or propose):
- Install / setup (developer): `composer install`, copy `.env.example` -> `.env`, `php artisan key:generate`, create sqlite file `php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"`, `php artisan migrate --seed`, `npm install`, `npm run build`.
- Development with hot-reload: `npm run dev` (or `composer run dev` defined in `composer.json` which runs concurrently: server, queue listener, pail logs, vite).
- Run tests: `composer test` / `php artisan test` (uses sqlite in-memory as configured in `phpunit.xml`).
- Storage symlink (if working with uploads): `php artisan storage:link`.

Important files and where to look for patterns:
- Routing & permissions: `routes/web.php` — shows how route groups use `auth`, `role`, and permission middleware. Use these for discovering authorization requirements.
- Controllers: `app/Http/Controllers` — resources for projects, tickets, rabs, votes, documents. Look for resource controllers (e.g. `ProjectController`, `TicketController`) to infer CRUD patterns and validation via `app/Http/Requests`.
- Policies & middleware: `app/Policies`, `app/Http/Middleware/RoleMiddleware.php` — authorization is handled both via policies and `spatie` permissions; routes sometimes wrap groups in `role:pm` or `permission:...`.
- Console commands: `app/Console/Commands` and `app/Console/Kernel.php` — used for maintenance tasks (e.g. legacy role migrations). If altering role names, prefer using existing commands like `MigrateLegacyRoles`.
- Seeds & data: `database/seeders` — seeds create roles, permissions, and test users. When adding permissions, update the seeder so new environments receive them.
- Docs: `docs/` contains project-specific docs that explain implemented features (in Indonesian). Reference these when the change touches feature scope.

Conventions and idioms to follow (project-specific):
- Role/permission naming: lowercase snake_case (e.g. `finance.manage_rab`, `projects.create`). When adding new permission strings, add them to seeders.
- Multiple roles per user are normal (e.g. `pm` + `sekretaris`). Avoid assuming single-role logic.
- Default test credentials & passwords: many seeded users use `password` as the default.
- Database defaults: local development expects SQLite file at `database/database.sqlite`. Avoid requiring external DBs unless explicitly requested.
- UI conditionals: views use Blade `@can`, `@role`, and `@canany` to show/hide links. Use controller policies or `permission` middleware to match view behavior.

Examples (copy-paste-friendly) from the codebase:
- Protect a route with a permission (pattern found in `README.md` and `routes/web.php`):

```php
Route::middleware(['permission:users.manage'])->group(function () {
    Route::resource('admin/users', Admin\UserController::class);
});
```

- Create sqlite quickly (used in composer scripts):

```php
@php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
```

- Add changelog entry when behavior changes:

```bash
php tools/update-docs.php "Short summary of change"
```

Common pitfalls an agent should avoid or check for:
- Do not enable public registration — it's intentionally disabled in `routes/auth.php`.
- When modifying permissions, ensure the seeder and any UI references (Blade `@can`) are updated together.
- If a change affects queued jobs or logging, note that the concurrent dev script runs `php artisan queue:listen` and `php artisan pail` — for local testing you may prefer `QUEUE_CONNECTION=sync`.

Migration conflicts (common)
- Duplicate migrations that create the same table are a recurring issue (example: two `create_votes_table` migrations). When you see "table already exists" errors during `php artisan migrate`, follow this workflow:
    1. Search `database/migrations` for duplicate migrations that reference the same table name.
    2. Decide canonical schema (pick the migration that matches models/controllers, usually the earlier one) and either:
         - Merge required fields into the canonical migration (recommended during early development), or
         - Neutralize the duplicate by making it a no-op (empty up()/down()) and move the original to `database/migrations/disabled/` as backup.
    3. Re-run migrations (`php artisan migrate --seed`). If you changed migrations after they've run in other environments, prefer creating a new migration to alter schema instead of editing already-run migrations.
    4. Update docs and tests to reflect the chosen schema.


Code changes and tests policy for an agent:
- Small behavior change: update relevant controller, add a focused unit/feature test under `tests/Feature` or `tests/Unit`, run `composer test` and ensure green.
- Database changes: add a migration in `database/migrations` and, if needed, update seeders. Run `php artisan migrate --seed` locally in CI scripts.

Where to look for more context:
- Feature implementation notes: `docs/PROGRESS_IMPLEMENTASI.md`, `docs/INDEX.md`.
- Calendar system & API: `docs/CALENDAR_SYSTEM.md` and API Calendar controllers under `app/Http/Controllers/Api/CalendarController.php`.

If you need clarification from humans:
- Ask which environment to target (local SQLite vs production MySQL/Postgres).
- Ask whether to update seeders or create a new migration when adding new permissions.

Small extras the agent may perform safely:
- Add or update tests for any changed behavior (happy path + 1 edge case).
- Add a one-line changelog using `tools/update-docs.php` when behavior or UI changes.

### Agent checklist — common tasks
Below are short, repeatable workflows an agent should follow when implementing common changes. Keep changes minimal, add tests, and update docs.

- Add a new permission
    1. Pick a lowercase, snake_case name: e.g. `projects.export`.
    2. Add permission in a seeder (e.g. `database/seeders/RolePermissionSeeder.php`) so new installs receive it.
    3. If needed, assign the permission to roles in the seeder or via a migration/command.
    4. Update any Blade/UI guards (`@can('projects.export')`) and controller middleware (`->middleware('permission:projects.export')`).
    5. Add a tiny feature test that asserts a user with/without the permission can/cannot access the endpoint.
    6. Run `php artisan db:seed --class=RolePermissionSeeder --force` in local dev (or create a migration for production-safe changes).

- Add a migration or modify schema
    1. Create migration: `php artisan make:migration add_field_to_table --table=table_name`.
    2. Implement `up()` / `down()` carefully and prefer backward-compatible changes where possible (nullable columns, default values).
    3. Add a focused migration test if the change affects application logic.
    4. Run `php artisan migrate` locally (in CI set DB to sqlite in-memory or the project's staging DB).
    5. Update seeders if the new column requires seeded values.

- Update views / Blade templates
    1. Use `@can`, `@role`, or `@canany` to gate UI elements consistently with backend permissions.
    2. Keep markup and Tailwind classes consistent with `resources/views/layouts/_menu.blade.php` patterns.
    3. Add a tiny feature test (HTTP test) that verifies the presence/absence of the new link or component for different roles.

- Add or update a controller action
    1. Follow existing Controller patterns (resource controllers under `app/Http/Controllers`).
    2. Validate input using form requests in `app/Http/Requests` when appropriate.
    3. Use policies (`app/Policies`) or permission middleware for authorization.
    4. Add route entry in `routes/web.php` (keep middleware consistent with neighboring routes).
    5. Add unit/feature tests and run `composer test`.

- Tests & CI
    - Use `composer test` / `php artisan test`. Tests use sqlite in-memory per `phpunit.xml`.
    - When modifying seeders or migrations that tests rely on, update tests accordingly.

Close each change with:
- A one-line changelog entry via `php tools/update-docs.php "Short summary of change"`.
- A short PR description that references the related `docs/` file if behavior/UI changed.

Finish: after writing changes, run `composer test` and `npm run build` (or `npm run dev` for dev) before opening PR; include a short description linking to the relevant `docs/` file.

If this file is outdated or you want additional details included, tell me which parts were unclear and I will iterate.
