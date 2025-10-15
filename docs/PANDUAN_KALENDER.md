# 📅 Panduan Penggunaan Kalender - RuangKerja

## Cara Akses Kalender

### 1. Kalender Pribadi
**URL:** `/calendar/personal`

Kalender pribadi menampilkan semua aktivitas Anda:
- ✅ Tiket yang Anda buat atau di-assign ke Anda (dengan due_date)
- ✅ Event komunitas yang Anda ikuti

**Warna Kalender:**
- 🔘 Abu-abu (Gray) = Tiket status "To Do"
- 🔵 Biru (Blue) = Tiket status "Doing"
- 🟢 Hijau (Green) = Tiket status "Done"
- 🟢 Hijau Zamrud (Emerald) = Event Komunitas

**Cara Lihat Detail:**
- Klik pada item kalender untuk melihat detail (judul, tipe, status, proyek)

**View yang Tersedia:**
- 📅 **Month** - Tampilan bulan penuh
- 📋 **Week** - Tampilan per minggu
- 📄 **Day** - Tampilan per hari

### 2. Kalender di Halaman Project
**Lokasi:** Buka project → Jika Anda bukan PM/Owner, akan melihat kalender

Kalender project menampilkan:
- ✅ Semua tiket project dengan deadline
- ✅ Event yang relevan dengan anggota project

**Fitur:**
- Sama seperti kalender pribadi (month/week/day view)
- Hanya menampilkan data yang relevan dengan project tersebut

---

## Fitur Kalender

### ✅ Yang Sudah Ada
1. **View Switching** - Ganti antara Month/Week/Day
2. **Navigation** - Prev/Next/Today button
3. **Color Coding** - Warna berbeda untuk status tiket dan event
4. **Event Click** - Klik untuk lihat detail
5. **Auto-load** - Data dimuat otomatis dari API

### 🚧 Yang Akan Datang (Phase 2)
1. **Drag & Drop** - Ubah tanggal tiket dengan drag
2. **Create Event** - Buat tiket/event langsung dari kalender
3. **Filter** - Filter berdasarkan tipe (tiket saja, event saja)
4. **Reminder** - Notifikasi sebelum deadline
5. **Sync** - Export ke Google Calendar / iCal

---

## Technical Details (untuk Developer)

### API Endpoints
```
GET /api/calendar/user/events
- Returns: Array of user's tickets & events in FullCalendar format

GET /api/calendar/project/{id}/events
- Returns: Array of project's tickets & events in FullCalendar format
```

### Response Format
```json
[
  {
    "id": "ticket-123",
    "title": "Buat Design Poster",
    "start": "2025-10-20",
    "backgroundColor": "#3b82f6",
    "borderColor": "#3b82f6",
    "extendedProps": {
      "type": "ticket",
      "status": "doing",
      "assignee": "John Doe"
    }
  },
  {
    "id": "event-456",
    "title": "Music Festival",
    "start": "2025-10-25",
    "end": "2025-10-26",
    "backgroundColor": "#10b981",
    "borderColor": "#10b981",
    "extendedProps": {
      "type": "event",
      "description": "Annual music festival"
    }
  }
]
```

### Files Terkait
- `resources/js/calendar.js` - FullCalendar initialization
- `app/Http/Controllers/Api/CalendarController.php` - API logic
- `app/Http/Controllers/CalendarController.php` - View controller
- `resources/views/calendar/personal.blade.php` - Personal calendar view
- `resources/views/projects/show.blade.php` - Project calendar integration

### NPM Packages
```json
"@fullcalendar/core": "^6.1.10",
"@fullcalendar/daygrid": "^6.1.10",
"@fullcalendar/timegrid": "^6.1.10",
"@fullcalendar/interaction": "^6.1.10"
```

---

## Troubleshooting

### Kalender tidak muncul?
1. Pastikan `npm run build` sudah dijalankan
2. Clear browser cache (Ctrl+Shift+R)
3. Cek console browser untuk error

### Data tidak muncul di kalender?
1. Pastikan tiket memiliki `due_date`
2. Pastikan user sudah di-assign ke event
3. Cek API endpoint di Network tab browser

### Warna tidak sesuai?
Warna ditentukan oleh status tiket di `CalendarController`:
- todo → gray (#6b7280)
- doing → blue (#3b82f6)
- done → green (#22c55e)
- event → emerald (#10b981)

---

**Update Terakhir:** 14 Oktober 2025  
**Status:** ✅ Production Ready (MVP Phase 1)
