# 🧭 Panduan Sidebar Menu RuangKerja

Dokumen ini menjelaskan struktur navigasi sidebar aplikasi RuangKerja yang telah diimplementasikan dengan submenu expandable.

---

## 📋 **Struktur Menu Utama**

Sidebar menggunakan **Alpine.js x-collapse** untuk membuat expandable submenu yang smooth dan interaktif.

---

## 🗂️ **Menu Items Detail**

### **1. 🏠 Dashboard**
**Akses:** Semua role  
**Route:** `dashboard`

Halaman utama yang menampilkan ringkasan aktivitas, proyek aktif, dan notifikasi terbaru.

---

### **2. 👤 Ruang Pribadi** *(Expandable)*
**Akses:** Semua role kecuali Guest  
**State:** Expandable dengan Alpine.js

**Sub-menu:**
- **🗓️ Kalender Aktivitas**
  - Route: `calendar.personal`
  - Menampilkan jadwal pribadi dengan FullCalendar
  - Terintegrasi dengan tiket dan event personal

- **📝 Catatan Pribadi** *(Coming Soon)*
  - Untuk mencatat ide, hasil rapat, to-do list pribadi
  - Status: Belum diimplementasi

- **📦 Arsip Pribadi** *(Coming Soon)*
  - Simpanan dokumen pribadi (tidak publik)
  - Status: Belum diimplementasi

---

### **3. 💼 Tiket Kerja** *(Expandable)*
**Akses:** Semua role  
**State:** Expandable dengan Alpine.js  
**Badge:** Menampilkan jumlah tiket terbuka (todo + doing)

**Sub-menu:**
- **🗂️ Meja Kerja (Kanban)**
  - Route: `projects.index`
  - Tampilan drag & drop tugas per proyek
  - Board dengan kolom: To Do, Doing, Done

- **📋 Daftar Tiket (Table)**
  - Route: `tickets.overview`
  - Daftar semua tiket sesuai role atau proyek
  - Filter berdasarkan status, prioritas, due date

- **💬 Diskusi Tiket** *(Coming Soon)*
  - Komentar, upload file, dan progress detail tiap tiket
  - Status: Belum diimplementasi

- **🕒 Riwayat Tiket** *(Coming Soon)*
  - Daftar tiket yang telah selesai atau diarsipkan
  - Status: Belum diimplementasi

---

### **4. 🧾 RAB & Laporan** *(Expandable)*
**Akses:** Semua role (fitur berbeda per role)  
**State:** Expandable dengan Alpine.js

**Sub-menu:**
- **💰 Pengajuan RAB**
  - Route: `rabs.create`
  - Semua anggota bisa mengajukan RAB
  - Form pengajuan dengan item-item kebutuhan

- **✅ Persetujuan RAB** *(Khusus Bendahara)*
  - Route: `rabs.index`
  - Hanya muncul untuk role Bendahara
  - Review dan approve/reject RAB yang masuk

- **📚 Daftar RAB & Laporan**
  - Route: `rabs.index`
  - Semua RAB yang sudah diajukan
  - Status: pending, approved, rejected

- **📤 Upload Laporan** *(Coming Soon)*
  - Upload hasil kegiatan atau laporan keuangan
  - Status: Belum diimplementasi

---

### **5. 🗃️ Ruang Penyimpanan** *(Expandable)*
**Akses:** Semua role (akses berbeda per role)  
**State:** Expandable dengan Alpine.js

**Sub-menu:**
- **📁 Dokumen Umum**
  - Route: `documents.index`
  - File publik komunitas
  - Upload, download, dan manage permissions

- **🔒 Dokumen Rahasia** *(Khusus Sekretaris & HR)*
  - Coming Soon
  - File dengan izin khusus
  - Hanya muncul untuk Sekretaris dan HR

- **🪪 Notula & Arsip** *(Coming Soon)*
  - Catatan rapat dan hasil kegiatan
  - Status: Belum diimplementasi

---

### **6. 🧑‍💼 Ruang Management** *(Expandable, Role-Specific)*
**Akses:** HR, PM, Bendahara, Sekretaris, Kewirausahaan  
**State:** Expandable dengan Alpine.js  
**Tampilan:** Menyesuaikan dengan role user

**Sub-menu per Role:**

#### **a. Untuk HR:**
- **👥 Manajemen Anggota**
  - Route: `admin.users.index`
  - Tambah, ubah, hapus anggota
  - Manage roles dan permissions
  - Voting anggota baru *(coming soon)*

#### **b. Untuk PM:**
- **🏗️ Manajemen Proyek**
  - Route: `projects.create`
  - Membuat, mengatur, dan menutup proyek
  - Assign anggota ke proyek
  - Buat dan kelola tiket

#### **c. Untuk Bendahara:**
- **💰 Verifikasi RAB**
  - Route: `rabs.index`
  - Approve/reject RAB
  - Rekap keuangan *(coming soon)*

#### **d. Untuk Sekretaris:**
- **📂 Pengelolaan Arsip**
  - Route: `documents.index`
  - Mengatur struktur folder
  - Memberikan izin akses dokumen
  - Manage dokumen rahasia *(coming soon)*

#### **e. Untuk Kewirausahaan:**
- **🏪 Usaha Aktif**
  - Route: `businesses.index`
  - Daftar usaha yang sedang dijalankan
  - Upload progress usaha

---

### **7. 🌐 Event**
**Akses:** Semua role  
**Route:** `events.index`

Daftar event komunitas (mendatang, berjalan, selesai). Detail event, peserta, dan chat khusus event *(coming soon)*.

---

### **8. 🗂️ Tiket Saya**
**Akses:** Semua role  
**Route:** `tickets.overview`  
**Badge:** Menampilkan jumlah tiket terbuka milik user

Ringkas menampilkan semua tiket yang ditugaskan ke user (aktif, selesai, menunggu review).

---

### **9. ⚙️ Akun & Pengaturan**
**Akses:** Semua role  
**Route:** `profile.edit`

**Fitur:**
- 👤 Edit profil pengguna
- 🔐 Ubah password
- 🌙 Preferensi tampilan *(coming soon)*
- 🔁 Pengajuan ganti role *(coming soon)*

---

## 🎨 **Fitur UI/UX**

### **1. Expandable Submenu**
```blade
x-data="{ 
    openMenus: {
        tiket: false,
        rab: false,
        penyimpanan: false,
        management: false,
        pribadi: false
    }
}"
```

Setiap menu besar memiliki state expand/collapse yang tersimpan di Alpine.js. User bisa toggle submenu dengan klik.

### **2. Active State Highlighting**
Menu yang sedang aktif akan:
- Background: `bg-gray-100`
- Text color: `text-gray-900`

### **3. Badge Indicators**
- **Tiket Terbuka:** Badge merah menampilkan jumlah tiket todo + doing
- **Counts:** Badge abu-abu untuk jumlah projects, documents, dll

### **4. Icons**
Setiap menu item memiliki icon SVG yang relevan untuk visual clarity.

### **5. Dividers**
Border-top pada section Management, Event, dan Akun untuk memisahkan grup menu.

### **6. Coming Soon Labels**
Fitur yang belum tersedia ditandai dengan:
- Text gray-400 (disabled)
- Label `(soon)` atau `(Coming Soon)`

---

## 🔐 **Role-Based Access Control**

### **Visibility Rules:**

| Menu Item | Guest | Member | PM | HR | Bendahara | Sekretaris | Kewirausahaan |
|-----------|-------|--------|-----|-----|-----------|------------|---------------|
| Dashboard | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Ruang Pribadi | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Tiket Kerja | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| RAB & Laporan | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Ruang Penyimpanan | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Ruang Management | ❌ | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Event | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Tiket Saya | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Akun & Pengaturan | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |

### **Submenu di Ruang Management:**
- **HR:** Manajemen Anggota
- **PM:** Manajemen Proyek
- **Bendahara:** Verifikasi RAB
- **Sekretaris:** Pengelolaan Arsip
- **Kewirausahaan:** Usaha Aktif

Jika user memiliki multiple roles, akan muncul multiple submenu.

---

## 🛠️ **Technical Implementation**

### **File Locations:**
- Main Menu: `resources/views/layouts/_menu.blade.php`
- Layout: `resources/views/layouts/app.blade.php`

### **Dependencies:**
- **Alpine.js:** Untuk expandable submenu dengan x-collapse
- **Tailwind CSS:** Untuk styling
- **Spatie Laravel Permission:** Untuk role-based access control

### **Key Blade Directives:**
```blade
@auth - Check if user is authenticated
@role('rolename') - Check if user has specific role
@if(!$user->hasRole('guest')) - Check if user is not guest
@php $active = request()->routeIs('route.name'); @endphp - Check active route
```

### **Counters:**
```php
$projectsCount = Project::count();
$documentsCount = Document::count();
$openTicketsCount = Ticket::where('status', 'todo')->count() 
                  + Ticket::where('status', 'doing')->count();
```

---

## 🧪 **Testing Checklist**

### **1. Guest User:**
- [ ] Tidak melihat menu "Ruang Pribadi"
- [ ] Tidak melihat menu "Ruang Management"
- [ ] Bisa akses Dashboard, Tiket Kerja, Event

### **2. Member Biasa:**
- [ ] Melihat menu "Ruang Pribadi" dengan Kalender
- [ ] Tidak melihat menu "Ruang Management"
- [ ] Bisa submit RAB di menu "RAB & Laporan"

### **3. HR:**
- [ ] Melihat submenu "Manajemen Anggota" di Ruang Management
- [ ] Bisa tambah/edit/hapus user
- [ ] Melihat semua menu dasar

### **4. PM:**
- [ ] Melihat submenu "Manajemen Proyek" di Ruang Management
- [ ] Bisa create project dan assign members
- [ ] Bisa create dan assign tiket

### **5. Bendahara:**
- [ ] Melihat submenu "Verifikasi RAB" di Ruang Management
- [ ] Melihat submenu "Persetujuan RAB" di menu RAB & Laporan
- [ ] Bisa approve/reject RAB

### **6. Sekretaris:**
- [ ] Melihat submenu "Pengelolaan Arsip" di Ruang Management
- [ ] Melihat submenu "Dokumen Rahasia" di Ruang Penyimpanan
- [ ] Bisa manage permissions dokumen

### **7. Kewirausahaan:**
- [ ] Melihat submenu "Usaha Aktif" di Ruang Management
- [ ] Bisa create dan update usaha

### **8. Expandable Menu:**
- [ ] Klik menu dengan submenu → expand/collapse smooth
- [ ] Icon arrow rotate saat expand
- [ ] State tersimpan selama navigation di page yang sama

### **9. Active State:**
- [ ] Menu yang sedang aktif highlight dengan bg-gray-100
- [ ] Submenu yang aktif juga highlight

### **10. Badge Counters:**
- [ ] Badge tiket terbuka muncul jika ada tiket todo/doing
- [ ] Jumlah badge update otomatis saat ada perubahan

---

## 📊 **Statistik Implementasi**

| Status | Feature | Progress |
|--------|---------|----------|
| ✅ | Dashboard | 100% |
| ✅ | Ruang Pribadi - Kalender | 100% |
| ⏳ | Ruang Pribadi - Catatan & Arsip | 0% |
| ✅ | Tiket Kerja - Kanban & List | 100% |
| ⏳ | Tiket Kerja - Diskusi & Riwayat | 0% |
| ✅ | RAB & Laporan - Pengajuan | 100% |
| ✅ | RAB & Laporan - Persetujuan | 100% |
| ⏳ | RAB & Laporan - Upload Laporan | 0% |
| ✅ | Ruang Penyimpanan - Dokumen Umum | 100% |
| ⏳ | Ruang Penyimpanan - Dokumen Rahasia | 0% |
| ✅ | Ruang Management - All Roles | 100% |
| ✅ | Event - List | 100% |
| ⏳ | Event - Detail & Chat | 50% |
| ✅ | Tiket Saya | 100% |
| ✅ | Akun & Pengaturan - Basic | 100% |
| ⏳ | Akun & Pengaturan - Advanced | 30% |

**Overall Progress MVP:** ~75%

---

## 🚀 **Cara Testing Sidebar**

### **1. Run Development Server:**
```bash
php artisan serve
```

### **2. Login dengan Role Berbeda:**
```bash
# Buat user dengan role berbeda untuk testing
php artisan tinker

$user = User::find(1);
$user->assignRole('hr');
```

### **3. Test Expandable Menu:**
- Klik menu dengan icon arrow (→)
- Pastikan submenu expand/collapse smooth
- Pastikan icon rotate

### **4. Test Role-Based Access:**
- Login sebagai Guest → tidak lihat Ruang Pribadi & Management
- Login sebagai Member → lihat Ruang Pribadi
- Login sebagai HR → lihat Manajemen Anggota
- Login sebagai PM → lihat Manajemen Proyek
- dll.

### **5. Test Active State:**
- Klik menu → pastikan highlight
- Refresh page → pastikan tetap highlight
- Click submenu → pastikan submenu highlight

---

## 🔄 **Future Enhancements**

### **Phase 2 Features:**
1. **Search di Sidebar** - Quick search untuk navigasi cepat
2. **Keyboard Shortcuts** - Akses menu dengan keyboard (Cmd+K)
3. **Favorites/Pinned** - User bisa pin menu yang sering dipakai
4. **Recent Pages** - Menampilkan halaman yang baru dikunjungi
5. **Notification Badges** - Real-time notification di menu items
6. **Collapsed Sidebar** - Mode collapsed (hanya icon) untuk layar kecil
7. **Dark Mode** - Support tema gelap
8. **Custom Menu Order** - User bisa customize urutan menu

---

## 📚 **Resources & References**

- **Alpine.js x-collapse:** https://alpinejs.dev/plugins/collapse
- **Tailwind CSS:** https://tailwindcss.com
- **Spatie Permission:** https://spatie.be/docs/laravel-permission
- **Laravel Routes:** https://laravel.com/docs/routing
- **Blade Directives:** https://laravel.com/docs/blade

---

## ✅ **Kesimpulan**

Sidebar menu RuangKerja telah diimplementasikan dengan:
- ✅ Struktur 9 menu utama sesuai requirements
- ✅ Expandable submenu dengan Alpine.js
- ✅ Role-based access control
- ✅ Badge counters untuk tiket terbuka
- ✅ Active state highlighting
- ✅ Icons untuk visual clarity
- ✅ Coming soon labels untuk fitur mendatang

**Ready for User Acceptance Testing!** 🎉

---

*Dokumen ini akan diupdate seiring dengan implementasi fitur-fitur baru.*
