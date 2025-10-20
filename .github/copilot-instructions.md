## SISARAYA — Copilot / AI Agent Instructions

**SISARAYA Ruang Kerja** is a Laravel-based collaborative workspace platform for a creative collective ("Kolektif Kreatif Lintas Bidang") managing projects, tickets, documents, voting, and financial tracking (RAB).

### Core Stack & Architecture

- **Framework**: Laravel 12.33 (PHP 8.4+), PSR-4 autoloading `App\` → `app/`
- **Frontend**: Vite 7.1 + Tailwind 3.x + Alpine.js 3.x. Entry: `resources/css/app.css`, `resources/js/app.js`
- **Auth**: Laravel Breeze (username-based, not email). Public registration **intentionally disabled** in `routes/auth.php`
- **Database**: SQLite (`database/database.sqlite`) for local dev. Tests use in-memory SQLite (see `phpunit.xml`)
- **Permissions**: `spatie/laravel-permission` v6.21. Roles/permissions seeded via `database/seeders/RolePermissionSeeder.php`
- **Users**: Created only by HR role via admin UI (`app/Http/Controllers/Admin/UserController.php`)

### 11 Roles & Multi-Role System

Users can have **multiple roles simultaneously** (e.g., `bhimo` = `pm` + `sekretaris`). Roles (lowercase snake_case):
- Core: `member`, `hr`, `pm`, `sekretaris`, `bendahara`, `media`, `pr`
- Extended: `talent_manager`, `researcher`, `talent`, `kewirausahaan`, `guest`
- Guest role has **minimal permissions** (read-only dashboard, projects, tickets)

**Never assume single-role logic.** Use `$user->hasRole('pm')` or `$user->hasAnyRole(['pm', 'sekretaris'])` for checks.

### Developer Workflows

**Setup (first time)**:
```powershell
composer install
cp .env.example .env
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate --seed
npm install
npm run build
```

**Development (hot-reload)**:
```powershell
# Option 1: Vite only
npm run dev

# Option 2: Full stack (server + queue + logs + vite) - defined in composer.json
composer run dev
```
This runs concurrently: `php artisan serve`, `php artisan queue:listen`, `php artisan pail`, and `npm run dev`.

**Testing**: `composer test` or `php artisan test` (SQLite in-memory)

**Storage symlink** (for document uploads): `php artisan storage:link`

**Legacy role migration** (if uppercase roles exist):
```powershell
php artisan roles:migrate-legacy --dry-run  # preview
php artisan roles:migrate-legacy             # apply
```

### Key Architecture Patterns

**Route organization** (`routes/web.php`):
- Routes use `auth`, `role:pm`, and `permission:users.manage` middleware
- Multi-role checks: `role:pm` allows any user with PM role (even if they have other roles too)
- Example resource pattern:
```php
Route::middleware(['permission:users.manage'])->group(function () {
    Route::resource('admin/users', Admin\UserController::class);
});
```

**Controllers** (`app/Http/Controllers`):
- Resource controllers for main entities: `ProjectController`, `TicketController`, `RabController`, `VoteController`
- Admin namespace: `Admin/UserController.php` (HR-only user management)
- API namespace: `Api/CalendarController.php` (FullCalendar integration)
- Validation via Form Requests in `app/Http/Requests`

**Models** (`app/Models`):
- Core: `User`, `Project`, `Ticket`, `Rab` (finance), `Document`, `Vote`, `Business`
- Relations: Projects have members (many-to-many with `project_user` pivot), tickets belong to projects
- User model uses `HasRoles` trait from Spatie

**Authorization**:
- Middleware: `app/Http/Middleware/RoleMiddleware.php` (supports multiple roles via `hasRole`)
- Policies: `app/Policies` for model-level authorization
- Blade directives: `@can('finance.manage_rab')`, `@role('pm')`, `@canany(['update'], $project)`, `@cannot('update', $project)`

**Frontend patterns** (`resources/views`):
- Layout: `layouts/app.blade.php` with `layouts/_menu.blade.php` (dynamic sidebar with role checks)
- Menu uses Alpine.js for expandable sections: `x-data="{ openMenus: {...} }"`, `@click="openMenus.mejaKerja = !openMenus.mejaKerja"`
- Badge counters in menu: `$activeProjectsCount`, `$myTicketsCount`, `$upcomingActivitiesCount` (computed in `_menu.blade.php`)
- Active state: `{{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50' }}`

### Project-Specific Conventions

**Naming**:
- Roles: lowercase snake_case (`hr`, `pm`, `sekretaris`, `bendahara`, `kewirausahaan`)
- Permissions: dot notation (`finance.manage_rab`, `projects.create`, `business.approve`)
- Routes: snake_case with dots (`projects.mine`, `tickets.createGeneral`, `rabs.approve`)

**Database**:
- Local dev: SQLite file at `database/database.sqlite`
- Tests: In-memory SQLite (`phpunit.xml` configures this)
- Migrations in `database/migrations/` — check for duplicates if "table already exists" errors occur

**Authentication**:
- Username-based (NOT email) — see `resources/views/auth/login.blade.php`
- Public registration disabled in `routes/auth.php` (commented out intentionally)
- Default test password: `password` for all seeded users (`database/seeders/SisarayaMembersSeeder.php`)
- Test users: `bhimo` (pm+sekretaris), `bagas` (hr), `dijah` (bendahara), etc.

**Multi-role behavior**:
- A user can have multiple roles: `$user->assignRole(['pm', 'sekretaris'])`
- Check with: `$user->hasRole('pm')` or `$user->hasAnyRole(['pm', 'hr'])`
- Dashboard shows role badges for all user roles (see `resources/views/dashboard.blade.php`)
- Menu items appear if user has ANY of the required roles

### Code Examples from Codebase

**Protect route with permission** (`routes/web.php`):
```php
Route::middleware(['permission:users.manage'])->group(function () {
    Route::resource('admin/users', Admin\UserController::class);
});
```

**Multi-role route protection** (`routes/web.php`):
```php
Route::middleware('role:pm')->group(function () {
    Route::get('tickets/general/create', [TicketController::class, 'createGeneral'])->name('tickets.createGeneral');
});
```

**Blade authorization directives** (`resources/views/layouts/_menu.blade.php`):
```blade
@role('bendahara')
    <a href="{{ route('rabs.index') }}">RAB & Laporan</a>
@endrole

@can('finance.manage_rab')
    <button>Approve RAB</button>
@endcan

@cannot('update', $project)
    <p>View only</p>
@endcannot
```

**Permission seeding** (`database/seeders/RolePermissionSeeder.php`):
```php
$permissions = ['finance.manage_rab', 'finance.upload_documents', 'finance.view_reports'];
foreach ($permissions as $perm) {
    Permission::firstOrCreate(['name' => $perm]);
}
Role::where('name', 'bendahara')->first()?->givePermissionTo(['finance.manage_rab', 'finance.upload_documents']);
```

**Multi-role user check** (`app/Http/Middleware/RoleMiddleware.php`):
```php
foreach ($roles as $role) {
    if ($user->hasRole($role)) {
        return $next($request);
    }
}
```

**Alpine.js expandable menu** (`resources/views/layouts/_menu.blade.php`):
```blade
<ul x-data="{ openMenus: { mejaKerja: false, rab: false } }">
    <button @click="openMenus.mejaKerja = !openMenus.mejaKerja">Meja Kerja</button>
    <div x-show="openMenus.mejaKerja">
        <!-- submenu items -->
    </div>
</ul>
```

### Common Pitfalls & Troubleshooting

**Migration conflicts** (common issue):
- Duplicate migrations creating same table cause "table already exists" errors
- **Workflow to fix**:
  1. Search `database/migrations` for duplicate table names
  2. Choose canonical migration (usually earliest, matching models/controllers)
  3. Either merge fields into canonical migration OR neutralize duplicate (empty up()/down()) and archive
  4. Run `php artisan migrate:fresh --seed` in dev
  5. For production, create new migration to alter schema instead of editing old ones

**Public registration**:
- Registration is **intentionally disabled** in `routes/auth.php`
- Only HR role creates users via admin panel
- Never re-enable registration without project lead approval

**Permission updates**:
- When adding permissions, update BOTH:
  - Seeder: `database/seeders/RolePermissionSeeder.php`
  - UI: Blade `@can()` directives
  - Routes: `permission:` middleware
- Run `php artisan db:seed --class=RolePermissionSeeder --force` after changes

**Queue & logging**:
- `composer run dev` runs queue listener and pail logs
- For simpler testing, set `QUEUE_CONNECTION=sync` in `.env`

**Multi-role logic**:
- Never use `$user->roles->first()` or assume single role
- Always use `hasRole()`, `hasAnyRole()`, or `hasAllRoles()`


### Documentation & Testing

**Update docs when behavior changes**:
```powershell
php tools/update-docs.php "Short summary of change"
```
This appends to `docs/CHANGELOG.md`.

**Key documentation**:
- `docs/PROGRESS_IMPLEMENTASI.md` — Feature implementation status (Bahasa Indonesia), 100% MVP complete
- `docs/INDEX.md` — Documentation index
- `docs/CALENDAR_SYSTEM.md` — Calendar integration details
- `docs/DOUBLE_ROLE_IMPLEMENTATION.md` — Multi-role system guide

**Testing workflow**:
- Run: `composer test` or `php artisan test`
- Tests use SQLite in-memory (configured in `phpunit.xml`)
- When modifying seeders/migrations, update affected tests
- Add focused feature test for new behavior (happy path + 1 edge case)

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
