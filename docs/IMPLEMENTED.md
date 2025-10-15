# Implemented Features (selected)

This file summarizes key features implemented in the application. Keep it updated when changes are made.

## Voting protections (2025-10-14)
- Duplicate vote prevention: users can only vote once per candidate per context.
- Quorum rule: a vote can only be finalized when at least 50% of eligible members (roles: member, pm, bendahara) have voted.
- Finalization: only users with role `pm` or `bendahara` can finalize a vote. Finalized results are persisted to `votes_results` table.

Files changed:
- `app/Http/Controllers/VoteController.php` (duplicate check, quorum calc, finalize endpoint)
- `app/Models/VotesResult.php` (model for finalized results)
- `database/migrations/2025_10_14_000006_create_votes_results_table.php` (migration)
- `resources/views/votes/tally.blade.php` (UI: quorum, finalize button, result)
- `routes/web.php` (finalize route)

Notes:
- Quorum threshold is currently 50% (>=). If you want a stricter rule (majority of eligible, >50%), update the controller logic.
- Consider adding automated tests for vote finalization and quorum edge cases.
# Implemented Features (RuangKerja MVP)

This document summarizes the features implemented so far, where to find the code, and quick validation steps.

## High-level implemented areas

- Authentication & Role management
  - Breeze (Blade) authentication scaffold installed.
  - Spatie roles & permissions seeded via `database/seeders/RolePermissionSeeder.php` and demo users via `database/seeders/UserPerRoleSeeder.php`.

- Projects & Tickets (Kanban)
  - Ticket CRUD & Kanban UI in `resources/views/projects/show.blade.php`.
  - TicketController: `app/Http/Controllers/TicketController.php` (store, move, createFromRab, overview).
  - Frontend helper: `resources/js/app.js` (moveTicket) for drag/drop.
  - Permissions: `tickets.create`, `tickets.update_status`, etc.

- Documents
  - Document upload/listing: `app/Http/Controllers/DocumentController.php`, `database/migrations/*create_documents_table*`, views under `resources/views/documents`.

- RAB (Rencana Anggaran Biaya)
  - Model: `app/Models/Rab.php` (relations to project & creator & approver).
  - Migrations: rabs table and extra fields (funds_status, approved_by, approved_at).
  - Controller: `app/Http/Controllers/RabController.php` (CRUD + approve/reject).
  - Views: `resources/views/rab/*` (index/create/show/edit) and flash partial.
  - Integration: create-ticket-from-rab (`routes/web.php` route `tickets.createFromRab`) and ticket type `permintaan_dana` which sets RAB `funds_status` to `requested` when moved to done.

- Business (Kewirausahaan)
  - Model: `app/Models/Business.php`.
  - Migration: `database/migrations/2025_10_14_000002_create_businesses_table.php`.
  - Controller: `app/Http/Controllers/BusinessController.php` (index/create/store/show).
  - Views: `resources/views/businesses/*`.
  - Route: `businesses` resource (index/create/store/show) and menu link in `resources/views/layouts/_menu.blade.php`.

- Events & Voting
  - Events: `app/Models/Event.php`, migration `2025_10_14_000003_create_events_table.php`, controller `app/Http/Controllers/EventController.php`, views `resources/views/events/*` and attach/detach participant routes.
  - Votes: simple voting system with migration `2025_10_14_000004_create_votes_table.php`, model `app/Models/Vote.php`, controller `app/Http/Controllers/VoteController.php`, and tally view.

- Guest lifecycle
  - `guest_expired_at` column on users (`2025_10_14_000005_add_guest_expired_to_users_table.php`).
  - When attaching a participant with role `guest`, the user's `guest_expired_at` is set to the event `end_date`.
  - `guests:expire` artisan command (`app/Console/Commands/ExpireGuests.php`) to clear guest personal data while preserving the user row.

## Important files & locations

- Routes: `routes/web.php` (rabs, projects.tickets, documents, businesses, events, votes)
- Seeders: `database/seeders/RolePermissionSeeder.php`, `UserPerRoleSeeder.php`
- Policies & Auth: `app/Policies/*` (ProjectPolicy, TicketPolicy were added earlier)

## How to validate locally

1. Run migrations and seeders:

```powershell
cd d:\Code\Program\RuangKerjaSisaraya\ruangkerja-mvp
php artisan migrate --force
php artisan db:seed --force
```

2. Start the dev server:

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

3. Login with seeded demo users (password is `password`). Seeds create users like `pm@example.com`, `bendahara@example.com`, etc.

4. Quick flows to try:
- As any authenticated user: create a RAB (`RAB` menu).
- As PM (user with `tickets.create`): create ticket, move it on Kanban.
- As Bendahara (user with `finance.manage_rab`): approve/reject RAB on RAB show page.
- Create event and add participant as `guest` (Event show -> add participant) and run `php artisan guests:expire` to observe guest clearing.

## Next recommended tasks
- Add feature tests for main flows (RAB approval, ticket creation permission, event guest expiry).
- Add notifications for RAB status changes.
- Add audit logs for RAB & ticket transitions.

---
Generated by the automated workspace assistant.
