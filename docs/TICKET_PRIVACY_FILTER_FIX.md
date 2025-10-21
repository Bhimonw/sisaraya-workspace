# Perbaikan Filter Privasi Tiket

**Tanggal**: 21 Oktober 2025  
**Branch**: Notifikasi  
**Status**: ✅ Selesai

## 🎯 Tujuan

Memperbaiki filter privasi pada halaman **"Semua Tiketku"** (`tickets.overview`) agar hanya menampilkan tiket yang **benar-benar terkait langsung dengan user**, bukan semua tiket dengan target_role yang cocok.

## 🐛 Masalah yang Ditemukan

### Filter Lama (Bermasalah)
```php
$tickets = Ticket::where(function($q) use ($user, $userRoles) {
    $q->where('claimed_by', $user->id)
      ->orWhere('target_user_id', $user->id)
      ->orWhereIn('target_role', $userRoles); // ❌ MASALAH DI SINI
})
->latest()
->get();
```

**Masalah**:
- Menampilkan **SEMUA tiket** dengan `target_role` yang cocok dengan role user
- Termasuk tiket yang **belum diklaim siapapun**
- Termasuk tiket yang **sudah diklaim user lain**
- Halaman "Semua Tiketku" menjadi terlalu ramai dengan tiket yang bukan milik user

**Contoh kasus bermasalah**:
- User A memiliki role `pm`
- Ada 50 tiket umum dengan `target_role = 'pm'`
- Dari 50 tiket tersebut:
  - 20 tiket belum diklaim siapapun
  - 15 tiket diklaim oleh PM lain (User B, C, D)
  - 15 tiket diklaim oleh User A
- **Filter lama**: User A melihat semua 50 tiket ❌
- **Filter baru**: User A hanya melihat 15 tiket miliknya ✅

## ✅ Solusi Implementasi

### Filter Baru (Diperbaiki)
```php
$tickets = Ticket::where(function($q) use ($user) {
    // Case 1: Tiket yang sudah/pernah diambil user (semua status)
    $q->where('claimed_by', $user->id)
      // Case 2: Tiket yang ditargetkan langsung ke user spesifik
      ->orWhere('target_user_id', $user->id);
})
->latest()
->get();
```

### Logika Privasi yang Benar

**Halaman "Semua Tiketku" (`tickets.overview`)** - History lengkap:
- ✅ Tiket yang **pernah/masih diklaim** oleh user (semua status: todo, doing, done, blackout)
- ✅ Tiket yang **ditargetkan langsung** ke user (`target_user_id = user.id`)
- ❌ **TIDAK** termasuk tiket dengan `target_role` yang belum diklaim
- ❌ **TIDAK** termasuk tiket yang diklaim oleh user lain

**Halaman "Tiketku" (`tickets.mine`)** - Tiket aktif:
- ✅ **My Tickets**: Tiket yang diklaim user dengan status aktif (todo, doing, blackout)
- ✅ **Available Tickets**: Tiket yang tersedia untuk diambil:
  - `target_role` cocok dengan role user
  - `target_user_id` adalah user saat ini
  - Atau tiket umum (target_role dan target_user_id keduanya null)
  - Belum diklaim siapapun (`claimed_by = null`)
  - Status aktif (todo, doing)

## 📊 Perbandingan Behavior

| Kondisi Tiket | Filter Lama (overview) | Filter Baru (overview) | Filter Mine (available) |
|---------------|------------------------|------------------------|-------------------------|
| Diklaim oleh user, status done | ✅ Tampil | ✅ Tampil | ❌ Tidak tampil (sudah selesai) |
| Diklaim oleh user, status todo | ✅ Tampil | ✅ Tampil | ❌ Tidak tampil (di My Tickets) |
| Target user spesifik, belum diklaim | ✅ Tampil | ✅ Tampil | ✅ Tampil (di Available) |
| Target role cocok, belum diklaim | ✅ Tampil ❌ | ❌ Tidak tampil ✅ | ✅ Tampil (di Available) |
| Target role cocok, diklaim user lain | ✅ Tampil ❌ | ❌ Tidak tampil ✅ | ❌ Tidak tampil |

## 🔍 Struktur Tabel Tickets

Field yang relevan untuk privasi:
```php
$table->string('target_role')->nullable();      // Role yang ditarget (pm, hr, sekretaris, dll)
$table->foreignId('target_user_id')->nullable(); // User spesifik yang ditarget
$table->foreignId('claimed_by')->nullable();     // User yang mengambil tiket
$table->timestamp('claimed_at')->nullable();     // Waktu tiket diambil
```

**Aturan Privasi**:
1. Jika `target_user_id` ada → tiket **khusus untuk user tersebut** (auto-claimed)
2. Jika `target_role` ada → tiket **untuk semua user dengan role tersebut** (perlu claim)
3. Jika keduanya null → tiket **umum untuk semua** (perlu claim)
4. `claimed_by` menentukan **ownership** tiket

## 🧪 Testing

### Test Case 1: User dengan Role PM
```php
// Setup
$pm = User::factory()->create();
$pm->assignRole('pm');

// Tiket yang ditargetkan ke role PM (belum diklaim)
$ticketForRole = Ticket::create([
    'title' => 'Tiket untuk PM',
    'target_role' => 'pm',
    'status' => 'todo',
]);

// Expected behavior
$overview = $pm->overview(); // ❌ Tidak muncul di overview (belum diklaim)
$mine = $pm->mine(); // ✅ Muncul di available tickets
```

### Test Case 2: Tiket Diklaim oleh User Lain
```php
// Setup
$pm1 = User::factory()->create()->assignRole('pm');
$pm2 = User::factory()->create()->assignRole('pm');

$ticket = Ticket::create([
    'title' => 'Tiket PM',
    'target_role' => 'pm',
    'claimed_by' => $pm1->id,
    'status' => 'doing',
]);

// Expected behavior
$pm1->overview(); // ✅ Muncul (diklaim oleh PM1)
$pm2->overview(); // ❌ Tidak muncul (diklaim oleh PM1, bukan PM2)
```

### Test Case 3: Tiket Spesifik ke User
```php
$user = User::factory()->create();
$ticket = Ticket::create([
    'title' => 'Tugas Khusus',
    'target_user_id' => $user->id,
    'claimed_by' => $user->id,
    'status' => 'done',
]);

// Expected behavior
$user->overview(); // ✅ Muncul (target spesifik + diklaim)
```

## 📝 File yang Diubah

- `app/Http/Controllers/TicketController.php` → Method `overview()`

## 🎓 Lesson Learned

1. **Privasi harus konsisten**: "Semua Tiketku" = tiket yang benar-benar milik user
2. **Role vs Ownership**: 
   - `target_role` = **eligibility** (siapa yang bisa claim)
   - `claimed_by` = **ownership** (siapa yang punya)
3. **Separation of Concerns**:
   - `overview()` = history **pribadi**
   - `mine()` = workspace **aktif** (my tickets + available tickets)

## 🔗 Related Documentation

- `docs/DOUBLE_ROLE_IMPLEMENTATION.md` - Multi-role system
- `docs/GENERAL_TICKETS_SECTION.md` - Tiket umum
- `app/Models/Ticket.php` - Model dan relasi

## ✅ Checklist

- [x] Identifikasi masalah filter privasi
- [x] Perbaiki method `overview()` di TicketController
- [x] Tambahkan komentar untuk dokumentasi inline
- [x] Verifikasi logic dengan contoh use case
- [x] Buat dokumentasi lengkap
- [ ] Testing manual dengan berbagai role
- [ ] Update unit test jika ada
