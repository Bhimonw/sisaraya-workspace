# Sistem Kalender RuangKerja Sisaraya

## Struktur Kalender

Sistem kalender memiliki 2 komponen utama:

### 1. Kalender Pribadi (Ruang Pribadi > Kalender Pribadi)
**Route:** `/calendar/personal`  
**Akses:** Anggota tetap only (tidak untuk Guest)

**Fungsi:**
- Input & manage kegiatan pribadi sendiri
- 6 kategori kegiatan: Pribadi, Keluarga, Pekerjaan Luar, Pendidikan, Kesehatan, Lainnya
- Toggle public/private visibility per kegiatan
- CRUD operations (Create, Read, Update, Delete) hanya untuk kegiatan sendiri
- View-only untuk kegiatan publik anggota lain

**Event Sources:**
- `/api/calendar/user/events` - Event komunitas yang user ikuti
- `/api/personal-activities` - Kegiatan pribadi (sendiri + public dari anggota lain)

### 2. Kalender Dashboard (Menu Utama / Quick Action)
**Route:** `/calendar/dashboard`  
**Akses:** Semua user (dengan konten berbeda per role)

**Fungsi:**
- Menampilkan rekap semua kegiatan dalam satu kalender
- Read-only, tidak ada input/edit (redirect ke Kalender Pribadi untuk manage)
- Koordinasi: lihat kesibukan anggota untuk scheduling

**Event Sources:**
- `/api/calendar/user/events` - Event komunitas
- `/api/calendar/user/projects` - Timeline proyek & tickets
- `/api/calendar/all-personal-activities` - Kegiatan pribadi semua anggota (public only, tidak untuk guest)

**Konten per Role:**
- **Anggota Tetap:** Event + Timeline Proyek Semua + Kegiatan Pribadi Publik Semua Anggota
- **Guest:** Event yang diikuti + Timeline Proyek yang diikuti (tidak ada kegiatan pribadi)

## API Endpoints

### Personal Activities (Kalender Pribadi)
- `GET /api/personal-activities` - Kegiatan pribadi (sendiri + public dari anggota lain)
- `POST /personal-activities` - Buat kegiatan baru
- `PUT /personal-activities/{id}` - Update kegiatan (owner only)
- `DELETE /personal-activities/{id}` - Hapus kegiatan (owner only)

### Calendar Events (Kalender Dashboard)
- `GET /api/calendar/user/events` - Event komunitas yang user ikuti
- `GET /api/calendar/user/projects` - Timeline proyek & tickets (guest: proyek yang diikuti saja)
- `GET /api/calendar/all-personal-activities` - Kegiatan pribadi semua anggota (public only, tidak untuk guest)

## Database Schema

### personal_activities
```sql
- id
- user_id (foreign key to users)
- title (string)
- description (text, nullable)
- start_time (datetime)
- end_time (datetime, nullable)
- location (string, nullable)
- type (enum: personal, family, work_external, study, health, other)
- color (string, default #3b82f6)
- is_public (boolean, default true)
- timestamps
```

## Use Cases

### Use Case 1: Anggota Input Kegiatan Pribadi
1. Login sebagai anggota tetap
2. Buka **Ruang Pribadi > Kalender Pribadi**
3. Klik "Tambah Kegiatan Pribadi" atau klik tanggal di kalender
4. Isi form: judul, deskripsi, waktu mulai/selesai, lokasi, kategori
5. Toggle "Tampilkan ke Anggota Lain" jika ingin publik
6. Simpan
7. Kegiatan muncul di kalender pribadi dengan warna sesuai kategori
8. Jika public, muncul juga di Kalender Dashboard anggota lain

### Use Case 2: PM Cek Kesibukan Anggota untuk Scheduling Rapat
1. Login sebagai PM
2. Buka **Kalender Dashboard** dari menu utama atau Quick Action
3. Lihat kalender dengan semua event, proyek, dan kegiatan pribadi anggota
4. Identifikasi waktu dimana banyak anggota sibuk (banyak event di tanggal tertentu)
5. Pilih waktu yang lebih kosong untuk buat rapat
6. Buat event rapat di waktu yang tepat

### Use Case 3: Guest Melihat Timeline Proyek yang Diikuti
1. Login sebagai guest
2. Buka **Kalender Dashboard**
3. Lihat hanya:
   - Event yang guest ikuti (misal: workshop, pertemuan dengan guest)
   - Timeline proyek yang guest diassign
   - Tickets dengan due_date dari proyek tersebut
4. **TIDAK** melihat kegiatan pribadi anggota (untuk privasi)

### Use Case 4: Anggota Lihat Kegiatan Pribadi Anggota Lain (Koordinasi)
1. Login sebagai anggota tetap
2. Buka **Kalender Dashboard**
3. Lihat kegiatan dengan prefix "👤 [Nama]: [Kegiatan]"
4. Contoh: "👤 Bhimo: Kursus Bahasa Inggris"
5. Klik event untuk detail (waktu, lokasi, kategori)
6. Gunakan info ini untuk koordinasi (misal: jangan jadwalkan rapat saat Bhimo kursus)

## Kategori Kegiatan Pribadi

| Kategori | Warna | Contoh |
|----------|-------|--------|
| Pribadi | `#3b82f6` (Blue) | Potong rambut, belanja, workout |
| Keluarga | `#10b981` (Green) | Acara keluarga, jemput anak, kunjungan |
| Pekerjaan Luar | `#f59e0b` (Amber) | Freelance project, part-time job |
| Pendidikan | `#8b5cf6` (Purple) | Kursus, workshop eksternal, ujian |
| Kesehatan | `#ef4444` (Red) | Dokter, check-up, terapi |
| Lainnya | `#6b7280` (Gray) | Kegiatan lain yang tidak masuk kategori di atas |

## Visibility Control

### Public (is_public = true)
- Muncul di Kalender Dashboard semua anggota tetap
- Menampilkan: nama user, judul, waktu, lokasi, kategori
- Tidak menampilkan: description (untuk privasi)
- Tujuan: koordinasi jadwal, hindari bentrok

### Private (is_public = false)
- Hanya muncul di Kalender Pribadi pemilik
- Tidak tampil di Kalender Dashboard anggota lain
- Untuk kegiatan yang sangat pribadi/sensitif

## Navigation

### Menu Structure
```
Dashboard (main menu)
Ruang Pribadi (submenu)
  └─ 📅 Kalender Pribadi [badge: upcoming count]
     └─ Input/manage kegiatan pribadi
  └─ 📝 Catatan Pribadi (soon)
```

### Quick Actions (Dashboard)
- **Kalender Umum** → link ke `/calendar/dashboard`
  - Rekap semua kegiatan
  - Read-only, lihat jadwal koordinasi

## Testing Checklist

### Kalender Pribadi
- [ ] Anggota tetap bisa akses `/calendar/personal`
- [ ] Guest tidak bisa akses kalender pribadi (redirect/403)
- [ ] Bisa tambah kegiatan pribadi dengan semua field
- [ ] Auto color assignment based on type
- [ ] Toggle public/private berfungsi
- [ ] Bisa edit kegiatan sendiri
- [ ] Tidak bisa edit kegiatan orang lain
- [ ] Bisa delete kegiatan sendiri
- [ ] View kegiatan publik anggota lain (read-only)
- [ ] Badge counter menampilkan upcoming activities (7 hari)

### Kalender Dashboard
- [ ] Semua user bisa akses `/calendar/dashboard`
- [ ] Anggota tetap lihat: event + proyek semua + kegiatan pribadi public semua
- [ ] Guest lihat: event diikuti + proyek diikuti + NO personal activities
- [ ] Event komunitas muncul dengan warna hijau
- [ ] Timeline proyek muncul dengan warna indigo
- [ ] Tickets muncul dengan warna sesuai status (todo/doing/done)
- [ ] Kegiatan pribadi muncul dengan prefix "👤 [Nama]:" dan warna sesuai kategori
- [ ] Klik event menampilkan detail modal
- [ ] Legend menampilkan semua kategori dengan warna yang benar
- [ ] Info box menjelaskan konten kalender dengan jelas

### Authorization
- [ ] Personal activity owner bisa edit/delete
- [ ] Non-owner tidak bisa edit/delete (hanya view jika public)
- [ ] Guest tidak bisa lihat personal activities di API
- [ ] Guest hanya lihat proyek yang diikuti
- [ ] Anggota tetap lihat semua proyek

## Future Enhancements

1. **Notifikasi Bentrok Jadwal**
   - Alert jika ada event/rapat bertabrakan dengan kegiatan pribadi
   - Suggestion waktu alternatif

2. **Recurring Events**
   - Kegiatan berulang (harian, mingguan, bulanan)
   - Contoh: "Kursus Bahasa Inggris setiap Senin 19:00"

3. **Calendar Sync**
   - Sinkronisasi dengan Google Calendar
   - Export to iCal format

4. **Availability Status**
   - "Tersedia", "Sibuk", "Keluar Kota"
   - Quick status untuk koordinasi

5. **Meeting Scheduler**
   - Find common available time slots
   - Auto-suggest best meeting time berdasarkan ketersediaan anggota

## Color Coding Reference

### Event Types
- Rapat: `#a855f7` (Purple)
- Workshop: `#3b82f6` (Blue)
- Event Eksternal: `#22c55e` (Green)
- Deadline: `#ef4444` (Red)

### Ticket Status
- Todo: `#6b7280` (Gray)
- Doing: `#3b82f6` (Blue)
- Done: `#22c55e` (Green)

### Projects
- Timeline Proyek: `#6366f1` (Indigo)

### Personal Activities
- Pribadi: `#3b82f6` (Blue)
- Keluarga: `#10b981` (Green)
- Pekerjaan Luar: `#f59e0b` (Amber)
- Pendidikan: `#8b5cf6` (Purple)
- Kesehatan: `#ef4444` (Red)
- Lainnya: `#6b7280` (Gray)
