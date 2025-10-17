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
