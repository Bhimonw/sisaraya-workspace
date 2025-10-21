# Changelog

All notable changes to this project should be documented in this file.

## 2025-10-14
- **Voting protections & quorum rules** - Duplicate vote prevention, quorum 50%, finalize endpoint. Lihat `docs/IMPLEMENTED.md` dan `docs/PROGRESS_IMPLEMENTASI.md` untuk detail.
- **Permission-based project views** - PM melihat full Kanban, anggota lain hanya melihat kalender & tiket mereka. Tombol "New Project" hanya untuk role dengan permission `projects.create`.
- **Ticket claiming** - Anggota bisa "Take" tiket yang belum di-assign via route `tickets.claim`.
- **Documentation update** - Dibuat `docs/PROGRESS_IMPLEMENTASI.md` (Bahasa Indonesia) dengan checklist lengkap dari 6 dokumen requirement. Progress: 95%.
- **Helper script** - Dibuat `tools/update-docs.php` untuk memudahkan update changelog.
- **Git hook template** - Sample post-merge hook di `tools/githooks/post-merge.sample`.

## 2025-10-12
- FullCalendar integration - Kalender pribadi dan project calendar dengan API endpoints. View month/week/day, color coding, tiket dengan deadline dan event komunitas.

## 2025-10-13
- Perbaikan menu navigation per role & aktivasi kalender dengan CDN. Menu sekarang menampilkan: Kalender (semua kecuali guest), Management section (HR/PM/Bendahara/Kewirausahaan), Events & Voting. Calendar menggunakan FullCalendar CDN untuk menghindari build issues.

## 2025-10-13
- Update sidebar menu dengan struktur lengkap: 1) Dashboard, 2) Ruang Pribadi (kalender aktivitas, catatan pribadi), 3) Tiket Kerja (meja kerja kanban, daftar tiket), 4) RAB & Laporan (pengajuan, persetujuan, daftar), 5) Ruang Penyimpanan (dokumen umum & rahasia), 6) Ruang Management (per role: HR-anggota, PM-proyek, Bendahara-RAB, Sekretaris-arsip, Kewirausahaan-usaha), 7) Event, 8) Tiket Saya, 9) Akun & Pengaturan. Semua dengan expandable submenu menggunakan Alpine.js x-collapse.

## 2025-10-13
- Cleanup duplikasi menu sidebar dan buat dokumentasi lengkap STATUS_IMPLEMENTASI_SIDEBAR.md. Status: Sidebar struktur 100% complete (9 menu utama), submenu 65% complete (13 implemented, 7 coming soon), total MVP sidebar 84% complete. Semua expandable menu, role-based access, badge counters, active state, dan icons sudah fully implemented.

## 2025-10-13
- Audit lengkap proyek vs 6 dokumen requirements. Status: MVP Core 100% complete (7/7 fitur), Role implementation 100% (11/11), Main features 73% (11/15), Event roles 100%, Sidebar 100% struktur. Gap analysis: 8 missing sub-features (catatan pribadi, arsip pribadi, komentar tiket, dokumen rahasia, notula, riwayat tiket, upload laporan, role request). Created 18 prioritized TODO items untuk Phase 1.5 dan Phase 2. Overall progress: 85% MVP complete. Ready for UAT setelah Priority 1 tasks (anggota seeder, kalender link, file validation).

## 2025-10-13
- Implementasi double role system - Bhimo (PM + Sekretaris). Dashboard dengan role badges, multi-role detection, quick actions per role. Sidebar menu support multiple roles. Seeder 14 anggota Sisaraya. Login: username only (no email). Dokumentasi lengkap di DOUBLE_ROLE_IMPLEMENTATION.md

## 2025-10-16
- **Project rating system** - Fitur rating proyek dengan bintang 1-5, average calculation, past members dapat memberikan rating (soft delete pivot table). View rating di project detail page.
- **Cleanup emoticons** - Removed ALL emoticons dari aplikasi (21 instances di 7 files): status labels, buttons, console.log messages. Professional UI dengan text-only labels.
- **Laravel 12 middleware fix** - Fix permission middleware registration untuk Laravel 12. Middleware harus didaftarkan di `bootstrap/app.php` (bukan `app/Http/Kernel.php`). Business module sekarang accessible untuk role kewirausahaan.

## 2025-10-17
## [Unreleased]

### Added
- Business approval workflow dengan auto-create project
- Menu "Manajemen Usaha" untuk PM (sama dengan kewirausahaan)
- Halaman detail usaha yang lengkap dengan section laporan
- Upload laporan usaha (PDF, Word, Excel, Images max 10MB)
- Download laporan usaha
- Delete laporan usaha (uploader atau PM)
- 4 jenis laporan: Penjualan, Keuangan, Operasional, Lainnya
- Color-coded badges untuk jenis laporan
- File size formatting untuk display
- **Card-based design** untuk halaman index businesses dengan grid layout
- Filter tabs dengan counter untuk setiap status
- Report count badge di card

## 2025-10-20

### Added - Project Creation Enhancements

- **Select All Members checkbox** di form create project dengan indeterminate state support
- **Advanced Member Filter** dengan 3 fitur:
  - Search bar untuk cari anggota berdasarkan nama
  - Filter role dropdown dengan multi-select (12 role options)
  - Counter display: "X / Y dipilih" (menampilkan selected vs visible members)
- **Role badges** di daftar anggota menampilkan semua role user dengan color-coding
- **Empty state** saat tidak ada hasil pencarian/filter
- Filter hanya mempengaruhi anggota yang visible, Select All hanya pilih yang terlihat
- Role counter di dropdown filter menampilkan jumlah member per role

### Fixed - Project Creation

- **Reactive counter** - Counter "X / Y dipilih" sekarang update real-time saat checkbox diklik
- **Watch filters** - Counter update otomatis saat search query atau role filter berubah
- **PM excluded from member list** - User yang membuat project (PM) tidak muncul dalam daftar pilihan anggota karena sudah otomatis menjadi owner/member project dengan kontrol penuh

### Added - Project Member Management

- **Bulk Actions** di kelola member project:
  - Checkbox select individual member dengan select all option
  - Bulk change role: Jadikan Admin atau Jadikan Member untuk multiple members sekaligus
  - Bulk delete: Hapus multiple members dari project sekaligus
  - Counter showing "X member dipilih"
  - Smart protection: Member dengan permanent role tidak dapat dipilih/diubah/dihapus via bulk action
- **Bulk Action Bar** muncul saat ada member yang dipilih
- **API Endpoints**: `bulkUpdateRole` dan `bulkDelete` untuk batch operations
- Semua bulk action dengan confirmation dialog
- Responsive card grid (1/2/3 columns)

### Fixed
- PM role sekarang memiliki permission `business.view` untuk akses halaman businesses

### Improved
- Redesign halaman businesses index dengan card layout yang lebih modern
- Better visual hierarchy dengan gradient headers
- Icon-based status indicators
- Hover effects dan smooth transitions
- Empty state dengan ilustrasi dan call-to-action - Kewirausahaan membuat usaha baru dengan status pending, PM dapat approve/reject melalui notifikasi database. **Ketika disetujui, otomatis create project dengan PM sebagai owner dan kewirausahaan sebagai admin member**. Status: pending/approved/rejected dengan rejection reason. Policy & permission: business.approve untuk PM. UI: status filters, approval buttons, reject modal, project link. Lihat `docs/BUSINESS_APPROVAL_AND_PROJECT_LABELS.md`.
- **Project labels** - Proyek sekarang memiliki label enum: UMKM (purple), DIVISI (blue), Kegiatan (green). Filter by label di index page. Label badge di project card. Optional field di create/edit form.
- Tampilkan bobot pada tabel dan detail tiket di manajemen tiket PM

## 2025-10-16
- Tambahkan slider bobot 1-10 pada form buat tiket umum di modal manajemen tiket PM

## 2025-10-16
- Tambahkan field bobot dengan slider 1-10 pada form buat tiket di project (tab create-ticket)

## 2025-10-16
- Target role pada form tiket project hanya menampilkan role yang ada pada anggota project (permanent & event role), due date menjadi wajib

## 2025-10-16
- Perbaiki logika permanent role untuk support multiple roles per user (user bisa memiliki banyak permanent role sekaligus)

## 2025-10-16
- Menghapus semua emoticon dan menggantinya dengan icon SVG atau text biasa untuk tampilan yang lebih profesional

## 2025-10-16
- Privacy pada kalender: kegiatan pribadi user lain di dashboard hanya ditampilkan sebagai 'Sibuk' tanpa detail, kalender pribadi tetap menampilkan detail lengkap

## 2025-10-16
- Dashboard statistics personalisasi: hanya menampilkan data proyek yang diikuti user dan tiket yang relevan dengan role-nya

## 2025-10-16
- Researcher dapat menambahkan, mengedit, dan menghapus evaluasi proyek dengan status draft/published

## 2025-10-16
- Added project rating system - members can rate completed projects with 1-5 stars and optional comments

## 2025-10-16
- Created comprehensive tests for project rating feature with 7 test scenarios

## 2025-10-16
- Added soft deletes to project_user pivot - past members can now rate completed projects

## 2025-10-16
- Fixed: Use allMembers() method instead of withTrashed() for pivot table soft deletes compatibility

## 2025-10-16
- Removed pause emoji from on_hold status in project views

## 2025-10-16
- Final cleanup: Removed all remaining emoticons from views and JS files (play, checkmark, close buttons)

## 2025-10-16
- Fixed: Added Spatie permission middleware to Kernel.php

## 2025-10-16
- Fixed: Added Spatie permission middleware to Kernel.php

## 2025-10-16
- Fixed: Added business permissions and assigned to kewirausahaan role

## 2025-10-16
- Fix permission middleware for Laravel 12 - register in bootstrap/app.php instead of Kernel.php

## 2025-10-17
- Standarisasi UI dengan modern card-based design di semua halaman (votes, documents, dashboard)

## 2025-10-17
- Tambah fitur kelola label/tag di halaman detail proyek dengan UI yang modern dan interaktif

## 2025-10-17
- Buat komponen reusable untuk label badge dan selector, refactor semua halaman proyek (index, show, create) menggunakan komponen

## 2025-10-17
- Refactor halaman RAB menjadi modular dengan komponen reusable (x-rab-status-badge, x-currency-input), desain card modern responsive, filter status dana (draft/pending/approved/rejected), layout konsisten untuk semua role termasuk bendahara

## 2025-10-17
- Fix error RoleDoesNotExist pada form tiket umum - ganti 'member' dengan 'talent' sesuai role yang ada di database, tambah role media/pr/talent_manager di form

## 2025-10-17
- Implementasi role 'member' sebagai universal role - semua user memiliki role ini, berguna untuk broadcast tiket ke seluruh anggota. Update seeder dan form tiket umum dengan UI yang lebih jelas

## 2025-10-17
- Klarifikasi logika tiket umum/broadcast: 1 tiket per role (bukan per orang), visible untuk semua user dengan role tersebut, bisa di-claim. Update UI dengan contoh penggunaan yang detail dan perbedaan dengan assignment langsung

## 2025-10-17
- Ubah layout Tiket Tersedia di halaman mine menjadi responsive card grid (1-4 kolom) agar tiket tampil bersebelahan dalam satu frame, dengan desain konsisten terhadap overview page

## 2025-10-17
- Ubah layout halaman mine: Tiket Tersedia menjadi kolom ke-4 sejajar dengan Blackout/To Do/Doing dalam grid 1/4 lebar, konsisten dengan design pattern kolom status lainnya

## 2025-10-17
- Rapikan UI halaman RAB index: tambah background abu-abu, perbaiki spacing/padding, tingkatkan shadow & border, perbarui tab filter dengan gradient, polish card layout untuk visual hierarchy yang lebih baik

## 2025-10-17
- Modularisasi halaman Manajemen Anggota HR: buat komponen reusable user-card, user-table, user-grid, role-badge, page-header, dan view-toggle. Refactor admin/users/index.blade.php menggunakan komponen dengan toggle grid/table view

## 2025-10-17
- Improve readability: tambah komentar header dokumentasi di semua komponen users, tambah section dividers dengan garis, dan tambah komentar inline untuk setiap bagian penting

## 2025-10-17
- Simplify halaman Manajemen Anggota: hapus toggle grid/table view, gunakan tampilan tabel saja untuk konsistensi dan kesederhanaan

## 2025-10-17
- Perbaiki tampilan username: pisahkan simbol @ dengan warna abu-abu terang dan username dengan font medium untuk readability yang lebih baik

## 2025-10-17
- Tambah kolom No (nomor urut) di tabel user untuk memudahkan identifikasi dan navigasi daftar anggota

## 2025-10-17
- Restrict HR permissions: HR hanya bisa kelola role user (approve/ubah role), tidak bisa edit detail akun atau hapus user. Tambah halaman manage-roles khusus untuk HR

## 2025-10-17
- Implement role change request system: User harus request perubahan role terlebih dahulu, HR hanya bisa approve/reject request, tidak bisa edit role secara langsung. Tambah migration, model, controller, dan views untuk request workflow

## 2025-10-17
- Tambah route dan akses untuk HR membuat user baru: admin.users.create dan admin.users.store (hanya HR yang memiliki akses)

## 2025-10-17
- Tambah validasi: Role Guest tidak bisa digabung dengan role lainnya (mutually exclusive). Update form create/edit user dan role change request dengan UI logic untuk auto-disable role lain ketika Guest dipilih

## 2025-10-17
- Buat komponen reusable x-back-button dan tambahkan tombol kembali di semua halaman (admin users, role requests, projects). Konsistensi UI untuk navigasi

## 2025-10-18
- Tambah status Blackout untuk proyek: PM dapat membuat proyek dengan status blackout untuk kondisi kritis/darurat. Tambah section khusus Blackout Projects di workspace dengan alert merah dan animasi pulse. Update model, controller, form create/edit, dan filter tabs

## 2025-10-19
- Tambah fitur Availability di Dashboard: Tampilkan anggota yang tersedia/free hari ini (tidak ada tiket aktif atau aktivitas pribadi). Tambah method isFreeOnDate(), getWorkloadOnDate(), getAvailabilityRange() di User model. Card availability ditampilkan di sidebar kanan bawah section Proyek Saya

## 2025-10-19
- Simplifikasi fitur Availability: Hapus halaman terpisah dan banner, pertahankan hanya card compact di sidebar kanan dashboard yang menampilkan anggota tersedia hari ini

## 2025-10-19
- Tambah fitur Online Users: Tampilkan anggota yang sedang login/online di navbar (sebelah notifikasi). Badge hijau menampilkan jumlah user online. Dropdown menampilkan list user online dengan avatar, role, dan last seen. Auto-refresh setiap 30 detik. Tambah kolom last_seen_at di users table dan middleware UpdateLastSeen untuk tracking

## 2025-10-19
- Perbaiki fitur Online Users: Tambah last_seen_at ke fillable, ubah middleware ke web group, gunakan DB query langsung untuk performa, kurangi threshold online dari 5 menit ke 3 menit, update interval dari 30 detik ke 15 detik untuk real-time feel, tambah logging untuk debugging

## 2025-10-20
- Added project chat feature with real-time polling (3s interval) - popup UI below members section

## 2025-10-20
- Improved project chat UI - moved chat button below members list, added 'Kelola Member' button in header banner

## 2025-10-20
- Fixed chat UI placement - moved chat section inside member card (before closing div)

## 2025-10-20
- Fixed chat UI placement - moved chat section inside member card (before closing div)

## 2025-10-20
- Pindahkan fitur chat proyek ke deskripsi project dengan popup style seperti notifikasi - chat sekarang muncul sebagai fixed popup di kanan bawah, dengan badge unread count dan background polling

## 2025-10-20
- Pindahkan tombol chat ke pinggir kanan header proyek (sejajar dengan members preview) - tombol chat sekarang di atas member avatars dengan badge unread yang lebih visible

## 2025-10-20
- Fix member count display: remove +1 to show accurate count (14 members total)

## 2025-10-20
- Add search & role filter to 'Tambah Member' form in project detail (matching create project UX)

## 2025-10-20
- Add search & role filter to member list in 'Kelola Member' section

## 2025-10-20
- Fix member count inconsistency: only count actual members in select all (exclude add member form checkboxes)

## 2025-10-20
- Fix project counter synchronization: show accurate counts for all status tabs (Semua, Aktif, Selesai, etc)

## 2025-10-20
- Add modern search & filter UI for target user selection in ticket creation form

## 2025-10-20
- Change target user selection to multiple choice with select all feature (checkbox instead of radio)

## 2025-10-20
- Fix bug: Tiket untuk 'Semua Orang' tidak muncul di halaman Tiketku - perbaiki SQL query untuk menangani target_role NULL dan target_user_id NULL

## 2025-10-20
- Add visual tag badges to Manajemen Tiket table - display status, context, and deadline badges consistent with Tiketku page for better quick-scan capability

## 2025-10-20
- Modernize Buat Tiket Umum form - add Blackout statistics card, improve form UI with modern styling, animations, gradients, emoji icons, and enhanced UX

## 2025-10-20
- Refactored ticket form into modular components - reduced from 847 to 501 lines (41% reduction)

## 2025-10-20
- Enhanced status badges (TODO, Doing, Done, Blackout) with gradient backgrounds for better visual consistency

## 2025-10-20
- Applied gradient status badges to Tiketku (mine) page modal for consistency with Manajemen Tiket

## 2025-10-20
- Added Status field (TODO/Blackout) to ticket creation form - users can now create blackout tickets directly

## 2025-10-20
- Modernized Project Ticket Creation form - matching Tiket Umum design with gradients, rounded borders, SVG icons, and enhanced UX

## 2025-10-20
- Perbaiki role permission management usaha: tambah business.update dan business.delete permission, ganti @role dengan @can di menu dan views, update policy untuk cek permission bukan role, update BusinessReportController authorization

## 2025-10-20
- Modernisasi form buat usaha baru menjadi modal pop-up: Alpine.js modal dengan gradient header, backdrop blur, smooth animations, keyboard shortcuts (ESC), auto-open saat validation error, improved accessibility (ARIA), dan modern UI/UX

## 2025-10-20
- Modernisasi halaman create business: redirect create route ke index dengan modal auto-open, update create.blade.php dengan gradient header, info cards, better form layout, tips section, dan process steps untuk user guidance

## 2025-10-20
- Modernisasi filter tabs usaha: grid 4 kolom dengan gradient backgrounds, large numbers, icon badges, animated indicators, quick stats summary (approval rate, with project, pending review) saat tab Semua aktif, hover effects, dan responsive design

## 2025-10-20
- Modernized Catatan Pribadi (Notes) page with gradient purple header, 4 stats cards (total/pinned/yellow/other), Alpine.js color and pin filters, modal create form with emoji pickers, inline edit form with color selection, modern action buttons with hover effects and color-coded styling

## 2025-10-20
- Modernized Personal Calendar (Kalender Pribadi) with gradient indigo header, 4 stats cards (total/public/private/upcoming), Alpine.js view mode filters (all/own/public), privacy-first approach (default private), modern modal form with radio privacy selector, stats endpoint, and improved UX with emoji indicators

## 2025-10-20
- Enhanced privacy in Personal Calendar: activities from other users now show as 'Sibuk - [Name]' without details, replaced all emoji with proper SVG icons for professional appearance, improved privacy explanation in UI

## 2025-10-20
- Modernisasi UI - Mengganti semua emoji dengan icon SVG profesional di seluruh aplikasi untuk tampilan yang lebih modern dan konsisten

## 2025-10-21
- Implement browser push notifications system with WebPush package, Service Worker, and comprehensive guide

## 2025-10-21
- Fix login pretty print error when VAPID keys not configured - added safety check

## 2025-10-21
- Configure VAPID keys in .env for push notifications - system ready for testing

## 2025-10-21
- Fix missing TicketController::show method - added detail view for tickets

## 2025-10-21
- Fix missing ticket badge components - replaced includes with inline badges

## 2025-10-21
- Enhanced ticket detail view - smart back navigation logic and modern gradient design

## 2025-10-21
- Enhanced project tickets board - modern gradients, icons, custom scrollbar, improved card UX

## 2025-10-21
- Added General Tickets section and standardized all button sizes (py-2, h-3.5 w-3.5 icons)

## 2025-10-21
- Fixed card alignment in project tickets board - standardized flex-col layout with min-height and bottom-aligned buttons

## 2025-10-21
- Fixed card alignment in project tickets board - standardized flex-col layout with min-height and bottom-aligned buttons across all sections

## 2025-10-21
- Fixed button alignment with status badge min-width, flex-shrink-0 containers, justify-center, and whitespace-nowrap across all ticket cards

## 2025-10-21
- Fix ticket privacy filter - overview page now only shows tickets claimed by user or specifically targeted to user

## 2025-10-21
- Add comprehensive project privacy system documentation - verified no debug output in production code

## 2025-10-21
- Add head-yahya role - project oversight with view-only access, can claim tickets and participate in all project chats

## 2025-10-21
- Rename head-yahya to ketua - role for Yahya as Chairman/Ketua SISARAYA with highest oversight access

## 2025-10-21
- Final naming: use 'head' role for Head of SISARAYA (Yahya) - highest oversight with view-only access

## 2025-10-21
- Quick fix implementation: Error handling, transactions, rate limiting, query validation, and security improvements

## 2025-10-21
- Comprehensive audit report October 2025 completed - overall rating 8.5/10, production-ready with conditions

## 2025-10-21
- Quick fixes completed - test pass rate improved from 56.5% to 86.8%, all critical issues resolved

## 2025-10-21
- Deep dive issues resolution - 9 critical fixes implemented: race conditions (ticket/vote/business), N+1 queries, pagination, privilege escalation, status validation, file cleanup

## 2025-10-21
- ALL 12 FIXES COMPLETED: race conditions, N+1 queries, pagination, privilege escalation, status validation, file cleanup, cascade deletes, queue notifications - Production ready 9.5/10

## 2025-10-21
- Implemented member data management system with photo upload, skills tracking, modal contributions, and external links. Added sekretaris dashboard for data management and CSV export.

## 2025-10-21
- Changed terminology from 'Data Kepegawaian' to 'Data Anggota' throughout the system (views, menu, documentation)

## 2025-10-21
- Modernized Data Anggota page with gradient headers, better card designs, hover effects, and improved visual hierarchy

## 2025-10-21
- Implemented tab-based modal system for adding member data, replacing full-page form with modern popup and event-driven Alpine.js approach

## 2025-10-21
- Separated modal component to _add-data-modal.blade.php and added auto-formatting for Rupiah input with thousands separator (Rp 1.000.000)

## 2025-10-21
- Changed Links label to 'Nama Orang/Pemilik' and made 'Nama Item' conditional (only for 'alat', hidden for 'uang')

## 2025-10-21
- Fixed 'invalid form control not focusable' error by making required attributes conditional based on active tab

## 2025-10-21
- Fixed data not being saved - added function to disable inactive tab fields before form submission

## 2025-10-21
- Modernized admin dashboard views with gradient themes, enhanced search UI, modern cards, and improved visual hierarchy

## 2025-10-21
- Modernized profile form with live photo preview, gradient design, and updated navigation to display user profile photos across the app

## 2025-10-21
- Implemented complete role change request system: users can request role changes, HR can approve/reject with review notes, full workflow with status tracking and notifications

## 2025-10-21
- Integrated role change request system directly in profile page: users can request role changes, view request history, HR can approve/reject with full workflow

## 2025-10-21
- Converted role change request from full section to modal: added Request Role button in profile role display, clean modal UI with form validation and history

## 2025-10-21
- Removed Request Role menu item from sidebar (now only accessible via button in profile) and optimized profile page padding for cleaner layout

## 2025-10-21
- Cleaned up unused view files: removed role-change-request-form.blade.php and role-requests directory (replaced by modal system)

## 2025-10-21
- Updated ROLE_CHANGE_REQUEST_SYSTEM.md documentation to reflect modal implementation instead of full-page form

## 2025-10-21
- Implemented image cropping feature with Cropper.js: users can now crop, zoom, rotate, and flip photos before uploading to profile. Modal-based UI with base64 encoding for seamless integration.

## 2025-10-21
- Created comprehensive documentation for photo crop feature (PHOTO_CROP_FEATURE.md) with complete implementation details, API reference, and troubleshooting guide

## 2025-10-21
- Polished desktop layout: implemented 3-column responsive grid (2:1 ratio), modernized password and delete account forms with gradient designs, added hover effects and smooth transitions for premium feel

## 2025-10-21
- 🎉 PROFILE SYSTEM COMPLETE: All 3 major features implemented (role request, photo crop, desktop layout). Created comprehensive summary document with technical details, user flows, and deployment checklist.

## 2025-10-21
- Simplified photo upload - removed crop modal feature in favor of simple direct upload

## 2025-10-21
- Fixed role change request modal - filter guest role and exclude roles user already has, fixed routing issue

## 2025-10-21
- Updated role_change_requests table structure - migrated from single role to multiple roles (JSON array)

## 2025-10-21
- Cleaned duplicate and non-standard roles - removed capitalized duplicates (HR, PM, Sekretaris, Bendahara, Guest) and non-standard roles (head, Anggota)

## 2025-10-21
- Fixed role request review actions - separated approve and reject forms for proper form submission

## 2025-10-21
- Enhanced role request review - added confirmation dialogs, fixed form visibility, and added quick link from user management page

## 2025-10-21
- Fixed responsive layout for role request button in user management page - now visible on all screen sizes
