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
