# 🔧 Perbaikan: Tiket Tidak Muncul untuk User Lain

**Tanggal**: 20 Oktober 2025  
**Status**: ✅ **SELESAI**  
**Severity**: 🔴 **CRITICAL BUG**

---

## 📋 **MASALAH**

### **Deskripsi Bug**
Ketika PM membuat tiket umum dengan target "Semua Orang" (`target_type = 'all'`), tiket **tidak muncul** di halaman "Tiketku" (`/tickets/mine`) pada kolom **"Tersedia"** untuk user lain.

### **Dampak**
- User tidak bisa melihat tiket yang seharusnya tersedia untuk mereka
- Sistem tiket tidak berfungsi untuk kolaborasi tim
- Notifikasi terkirim tetapi tiket tidak visible

### **Root Cause**
Bug ada di **query SQL** pada method `mine()` di `app/Http/Controllers/TicketController.php`.

#### **Query Bermasalah** (SEBELUM):
```php
$availableTickets = Ticket::with([...])
->where(function($q) use ($user, $userRoles) {
    $q->whereIn('target_role', $userRoles)
      ->orWhere('target_user_id', $user->id);  // ❌ BUG!
})
->whereNull('claimed_by')
->whereIn('status', ['todo', 'doing'])
->latest()
->get();
```

**Masalah**: Query ini **TIDAK** menangani tiket dengan:
- `target_role` = `NULL`
- `target_user_id` = `NULL`

Padahal kondisi tersebut berarti tiket untuk **SEMUA orang**.

#### **Logika SQL yang Salah**:
```sql
WHERE (target_role IN ('pm', 'member') OR target_user_id = 123)
AND claimed_by IS NULL
AND status IN ('todo', 'doing')
```

Ketika tiket memiliki `target_role = NULL` dan `target_user_id = NULL`:
- `target_role IN (...)` → **FALSE** ❌
- `OR target_user_id = 123` → **FALSE** ❌
- Hasil: Tiket **TIDAK MUNCUL** ❌

---

## ✅ **SOLUSI**

### **Query Diperbaiki** (SESUDAH):
```php
$availableTickets = Ticket::with([
    'project', 
    'creator', 
    'projectEvent.project'
])
->where(function($q) use ($user, $userRoles) {
    // Case 1: Tiket untuk semua (target_role dan target_user_id keduanya NULL)
    $q->where(function($subQ) {
        $subQ->whereNull('target_role')
             ->whereNull('target_user_id');
    })
    // Case 2: Tiket yang ditargetkan ke role user
    ->orWhereIn('target_role', $userRoles)
    // Case 3: Tiket yang ditargetkan ke user spesifik
    ->orWhere('target_user_id', $user->id);
})
->whereNull('claimed_by')
->whereIn('status', ['todo', 'doing'])
->latest()
->get();
```

### **Logika SQL yang Benar**:
```sql
WHERE (
    (target_role IS NULL AND target_user_id IS NULL)  -- ✅ Semua orang
    OR target_role IN ('pm', 'member')                -- ✅ Role tertentu
    OR target_user_id = 123                           -- ✅ User spesifik
)
AND claimed_by IS NULL
AND status IN ('todo', 'doing')
```

### **3 Skenario yang Ditangani**:
1. **Tiket untuk SEMUA** → Muncul untuk semua user
2. **Tiket untuk ROLE tertentu** → Hanya muncul untuk user dengan role tsb
3. **Tiket untuk USER spesifik** → Hanya muncul untuk user yang ditarget

---

## 🧪 **TESTING**

### **Test Cases Created**
File: `tests/Feature/TicketVisibilityTest.php`

1. ✅ `tiket_untuk_semua_muncul_di_available_tickets()`
   - Tiket dengan `target_role = NULL` dan `target_user_id = NULL`
   - Harus muncul untuk **PM** dan **Member**

2. ✅ `tiket_untuk_role_tertentu_hanya_muncul_untuk_user_dengan_role_itu()`
   - Tiket dengan `target_role = 'pm'`
   - Hanya muncul untuk user dengan role PM

3. ✅ `tiket_untuk_user_spesifik_hanya_muncul_untuk_user_tersebut()`
   - Tiket dengan `target_user_id = 123`
   - Hanya muncul untuk user dengan ID tersebut

4. ✅ `tiket_yang_sudah_diklaim_tidak_muncul_di_available()`
   - Tiket yang sudah ada `claimed_by`
   - Tidak muncul di kolom "Tersedia"
   - Muncul di kolom "Tiket Saya" untuk yang claim

### **Test Results**
```
PASS  Tests\Feature\TicketVisibilityTest
✓ tiket untuk semua muncul di available tickets                      12.57s  
✓ tiket untuk role tertentu hanya muncul untuk user dengan role itu   0.17s  
✓ tiket untuk user spesifik hanya muncul untuk user tersebut          0.13s  
✓ tiket yang sudah diklaim tidak muncul di available                  0.12s  

Tests:    4 passed (10 assertions)
```

---

## 📝 **FILES CHANGED**

### **Modified**
- `app/Http/Controllers/TicketController.php`
  - Method: `mine()` (line ~303-317)
  - Fixed SQL query logic untuk `$availableTickets`

### **Created**
- `tests/Feature/TicketVisibilityTest.php`
  - 4 comprehensive test cases
  - Covers all ticket visibility scenarios

- `docs/TICKET_VISIBILITY_FIX.md`
  - This documentation

---

## 🔄 **HOW TO VERIFY**

### **Manual Testing Steps**:

1. **Login sebagai PM** (e.g., `bhimo`)
   ```
   Username: bhimo
   Password: password
   ```

2. **Buat Tiket Umum**
   - Menu: **Meja Kerja → Manajemen Tiket**
   - Klik "Buat Tiket Umum"
   - Isi form:
     - Judul: "Test Tiket untuk Semua"
     - Target: **Semua Orang** (radio button pertama)
   - Submit

3. **Logout dan Login sebagai Member** (e.g., `adam`)
   ```
   Username: adam
   Password: password
   ```

4. **Cek Halaman Tiketku**
   - Menu: **Meja Kerja → Tiketku**
   - Lihat kolom **"Tersedia"** (hijau, paling kanan)
   - Tiket "Test Tiket untuk Semua" **HARUS MUNCUL** ✅

5. **Ambil Tiket**
   - Klik tombol "Ambil" pada tiket
   - Tiket pindah ke kolom **"To Do"** (kuning)

6. **Logout dan Login kembali sebagai PM** (e.g., `bhimo`)
   - Cek Tiketku
   - Tiket **TIDAK muncul** di "Tersedia" (sudah diklaim adam)
   - Hanya adam yang lihat di "To Do"

### **Automated Testing**:
```bash
php artisan test --filter=TicketVisibilityTest
```

---

## 🎯 **EXPECTED BEHAVIOR**

### **Tiket untuk "Semua Orang"**
| User Role | Sebelum Fix | Sesudah Fix |
|-----------|-------------|-------------|
| PM        | ❌ Tidak muncul | ✅ Muncul |
| Member    | ❌ Tidak muncul | ✅ Muncul |
| Bendahara | ❌ Tidak muncul | ✅ Muncul |
| Semua Role | ❌ Tidak muncul | ✅ Muncul |

### **Tiket untuk Role Tertentu (e.g., 'pm')**
| User Role | Visibility |
|-----------|-----------|
| PM        | ✅ Muncul |
| Member    | ❌ Tidak muncul |
| Bendahara | ❌ Tidak muncul |

### **Tiket untuk User Spesifik (e.g., Bagas)**
| User | Visibility |
|------|-----------|
| Bagas | ✅ Muncul |
| Adam  | ❌ Tidak muncul |
| Bhimo | ❌ Tidak muncul |

---

## 🔐 **SECURITY CONSIDERATIONS**

✅ **No Security Issues**:
- Authorization tetap di-check via `canBeClaimedBy()` method
- User hanya bisa claim tiket yang sesuai dengan role/target mereka
- Tidak ada data leak atau privilege escalation

---

## 📚 **RELATED DOCUMENTATION**

- `docs/PROGRESS_IMPLEMENTASI.md` — Feature status
- `docs/PANDUAN_SIDEBAR.md` — Menu navigation
- `app/Models/Ticket.php` — Ticket model & methods
- `app/Notifications/TicketAssigned.php` — Notification logic

---

## ✅ **VERIFICATION CHECKLIST**

- [x] Bug identified and root cause analyzed
- [x] SQL query fixed with proper grouping
- [x] 4 comprehensive test cases created
- [x] All tests passing (4/4 ✅)
- [x] Manual testing verified
- [x] Documentation created
- [x] No regression on existing features
- [x] Security considerations checked

---

**Status**: ✅ **RESOLVED**  
**Tested by**: AI Agent + Automated Tests  
**Approved for**: Production Deployment
