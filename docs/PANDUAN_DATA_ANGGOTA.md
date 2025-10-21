# Panduan Pengguna: Data Anggota

## 📋 Gambaran Umum

Fitur Data Anggota memungkinkan Anda untuk:
- Upload foto profil
- Mencatat keahlian/skills yang Anda miliki
- Mendaftarkan modal kontribusi (uang atau alat)
- Menambahkan link eksternal (portfolio, media sosial, dll)

Semua data akan dikirim ke **Sekretaris** untuk pengelolaan kolektif.

---

## 👤 Mengisi Profil

### 1. Buka Halaman Profil
- Klik **"Akun & Pengaturan"** di sidebar
- Atau langsung akses: `/profile`

### 2. Upload Foto Profil
```
┌─────────────────────────────────┐
│  Foto Profil                    │
│  ┌────┐                          │
│  │ 👤 │  [Choose File]          │
│  └────┘                          │
│  Format: JPG, PNG, GIF          │
│  Maksimal 2MB                   │
└─────────────────────────────────┘
```

**Tips:**
- Gunakan foto close-up wajah untuk hasil terbaik
- Jika tidak upload foto, akan muncul inisial nama Anda
- Foto lama otomatis terhapus saat upload baru

### 3. Isi Informasi Kontak
```
┌────────────────────────────────┐
│ Nomor Telepon: [_____________] │
│ WhatsApp:      [_____________] │
└────────────────────────────────┘
```

### 4. Tulis Bio (Opsional)
```
┌────────────────────────────────┐
│ Bio:                           │
│ ┌────────────────────────────┐ │
│ │ Ceritakan tentang diri     │ │
│ │ Anda...                    │ │
│ └────────────────────────────┘ │
└────────────────────────────────┘
```

### 5. Klik "Save"

---

## 📝 Mengisi Data Anggota

### Langkah 1: Akses Halaman Data
- Klik **"Data Anggota"** di sidebar
- Atau langsung akses: `/member-data`

### Langkah 2: Klik "+ Tambah Data"

### Langkah 3: Isi Form

#### A. Skills (Keahlian)
```
┌──────────────────────────────────────┐
│ Skill #1                             │
│ ┌──────────────────────────────────┐ │
│ │ Nama Keahlian: Design Grafis    │ │
│ │ Tingkat: [▼ Mahir              ]│ │
│ │ Deskripsi:                       │ │
│ │ Pengalaman 5 tahun...            │ │
│ └──────────────────────────────────┘ │
│                                      │
│ [+ Tambah Skill]                     │
└──────────────────────────────────────┘
```

**Tingkat Keahlian:**
- **Pemula**: Baru belajar, butuh bimbingan
- **Menengah**: Bisa kerja mandiri untuk tugas sederhana
- **Mahir**: Bisa handle tugas kompleks
- **Expert**: Bisa mengajar dan memimpin

**Contoh Skills:**
- Design: Graphic Design, UI/UX, Video Editing
- Development: Web Development, Mobile Apps, Database
- Writing: Copywriting, Content Writing, Translation
- Management: Project Management, Event Planning
- Arts: Fotografi, Ilustrasi, Musik

#### B. Modal (Kontribusi)
```
┌──────────────────────────────────────┐
│ Modal #1                             │
│ ┌──────────────────────────────────┐ │
│ │ Jenis: [▼ Uang                  ]│ │
│ │ Nama Item: Dana Operasional      │ │
│ │ Jumlah: Rp [1000000____________] │ │
│ │ Deskripsi: Untuk proyek X...     │ │
│ │ ☑ Dapat dipinjam anggota lain    │ │
│ └──────────────────────────────────┘ │
│                                      │
│ [+ Tambah Modal]                     │
└──────────────────────────────────────┘
```

**Jenis Modal:**
1. **Uang**: Dana yang Anda kontribusikan
2. **Alat**: Peralatan yang bisa digunakan kolektif

**Contoh Modal Uang:**
- Dana operasional
- Biaya sewa tempat
- Budget marketing

**Contoh Modal Alat:**
- Kamera DSLR
- Laptop
- Printer
- Sound System
- Proyektor

**"Dapat dipinjam":**
- ✅ Centang jika anggota lain boleh pinjam
- ❌ Biarkan kosong jika hanya untuk proyek tertentu

#### C. Links & Kontak Eksternal
```
┌──────────────────────────────────────┐
│ Link #1                              │
│ ┌──────────────────────────────────┐ │
│ │ Nama: Portfolio Website          │ │
│ │ Bidang: Web Design               │ │
│ │ URL: https://myportfolio.com     │ │
│ │ Kontak: -                        │ │
│ └──────────────────────────────────┘ │
│                                      │
│ [+ Tambah Link]                      │
└──────────────────────────────────────┘
```

**Contoh Links:**
- **Portfolio**: Website pribadi, Behance, Dribbble
- **Media Sosial**: Instagram, LinkedIn, Twitter
- **Repository**: GitHub, GitLab
- **Kontak**: Email kerja, nomor bisnis

### Langkah 4: Submit
```
┌────────────────────────────────────┐
│ ℹ️  Data yang Anda kirim akan      │
│    diterima oleh Sekretaris untuk │
│    dikelola dan digunakan dalam   │
│    koordinasi kolektif.           │
│                                    │
│  [Batal]  [Simpan & Kirim]        │
└────────────────────────────────────┘
```

---

## 👀 Melihat Data Anda

Setelah submit, Anda akan diarahkan ke halaman "Data Anggota Saya":

```
┌─────────────────────────────────────────┐
│ Data Anggota Saya    [+ Tambah Data]│
├─────────────────────────────────────────┤
│                                         │
│ 💡 Keahlian / Skills                    │
│ ┌────────────────────────────────────┐  │
│ │ Design Grafis                      │  │
│ │ Tingkat: Mahir           [Hapus]   │  │
│ │ Pengalaman 5 tahun...              │  │
│ └────────────────────────────────────┘  │
│                                         │
│ 💰 Modal (Uang / Alat)                  │
│ ┌────────────────────────────────────┐  │
│ │ 💵 Dana Operasional                │  │
│ │ Rp 1.000.000             [Hapus]   │  │
│ │ ✓ Dapat dipinjam                   │  │
│ └────────────────────────────────────┘  │
│                                         │
│ 🔗 Link & Kontak Eksternal              │
│ ┌────────────────────────────────────┐  │
│ │ Portfolio Website                  │  │
│ │ Bidang: Web Design       [Hapus]   │  │
│ │ 🔗 https://myportfolio.com  ↗      │  │
│ └────────────────────────────────────┘  │
└─────────────────────────────────────────┘
```

**Aksi yang Bisa Dilakukan:**
- ✅ Lihat semua data Anda
- ✅ Tambah data baru kapan saja
- ✅ Hapus data yang tidak relevan lagi
- ✅ Data otomatis ter-update di dashboard Sekretaris

---

## 📊 Dashboard Sekretaris

### Untuk Sekretaris

Sekretaris dapat mengakses dashboard khusus:

#### 1. Lihat Semua Anggota
```
┌─────────────────────────────────────────────┐
│ Data Anggota Anggota    [📥 Export CSV] │
├─────────────────────────────────────────────┤
│ 🔍 [Cari nama atau username...] [Cari]      │
├─────────────────────────────────────────────┤
│                                             │
│ ┌─────────────────────────────────────────┐ │
│ │ 👤 Bhimo (@bhimo)                       │ │
│ │ pm, sekretaris                          │ │
│ │ 📞 0812345678  💬 0812345678           │ │
│ │ 3 skill(s)  2 modal  1 link(s)         │ │
│ │                      [Lihat Detail] ──> │ │
│ └─────────────────────────────────────────┘ │
│                                             │
│ ┌─────────────────────────────────────────┐ │
│ │ 👤 Dijah (@dijah)                       │ │
│ │ bendahara                               │ │
│ │ 2 skill(s)  1 modal  3 link(s)         │ │
│ │                      [Lihat Detail] ──> │ │
│ └─────────────────────────────────────────┘ │
│                                             │
│ « Previous | 1 | 2 | 3 | Next »            │
└─────────────────────────────────────────────┘
```

#### 2. Detail Member
Klik "Lihat Detail" untuk melihat profil lengkap:

```
┌──────────────────────────────────────┐
│ Data Anggota: Bhimo    [← Kembali]│
├──────────────────────────────────────┤
│ 👤 Bhimo                             │
│    @bhimo                            │
│    Bio singkat...                    │
│                                      │
│    Roles: pm, sekretaris             │
│                                      │
│    📞 0812345678                     │
│    💬 0812345678                     │
│                                      │
├──────────────────────────────────────┤
│ 💡 Keahlian / Skills (3)             │
│ ┌──────────────┬──────────────────┐  │
│ │ Design Grafis│ UI/UX Design     │  │
│ │ 🟦 Mahir     │ 🟩 Expert        │  │
│ └──────────────┴──────────────────┘  │
│                                      │
│ 💰 Modal (Uang / Alat) (2)           │
│ ┌──────────────────────────────────┐ │
│ │ 💵 Dana Operasional              │ │
│ │    Rp 1.000.000                  │ │
│ │    ✓ Dapat dipinjam              │ │
│ └──────────────────────────────────┘ │
│                                      │
│ 🔗 Link & Kontak Eksternal (1)       │
│ ┌──────────────────────────────────┐ │
│ │ Portfolio Website                │ │
│ │ Web Design                       │ │
│ │ 🔗 https://myportfolio.com  ↗    │ │
│ └──────────────────────────────────┘ │
└──────────────────────────────────────┘
```

#### 3. Export Data ke CSV
Klik "Export CSV" untuk download semua data:

```
Nama,Username,Phone,WhatsApp,Role,Skills,Modal,Links
Bhimo,bhimo,0812345678,0812345678,pm; sekretaris,Design Grafis; UI/UX; Web Dev,Dana Operasional; Kamera,Portfolio; LinkedIn
Dijah,dijah,0811111111,0811111111,bendahara,Accounting; Excel,Dana Kas,Instagram; Website
...
```

**Kegunaan Export:**
- Analisis skill di kolektif
- Perencanaan resource untuk proyek
- Laporan untuk stakeholder
- Backup data

---

## 🔔 Notifikasi

### Kapan Sekretaris Dapat Notifikasi?

Sekretaris otomatis menerima notifikasi saat:
1. ✅ Member menambah data baru
2. ✅ Member mengupdate data existing

**Format Notifikasi:**
```
┌─────────────────────────────────┐
│ 🔔 Data Member Diperbarui       │
│                                 │
│ Bhimo - Data baru ditambahkan   │
│                                 │
│ [Lihat Detail] ────────────────>│
└─────────────────────────────────┘
```

Klik notifikasi untuk langsung ke detail member.

---

## ❓ FAQ (Frequently Asked Questions)

### Q1: Apakah data saya bersifat rahasia?
**A**: Data hanya bisa dilihat oleh:
- Anda sendiri
- User dengan role **Sekretaris**

Tidak ada akses publik.

### Q2: Bisa ubah data setelah submit?
**A**: Ya! Anda bisa:
- Tambah entry baru kapan saja
- Hapus entry yang sudah tidak relevan
- Tidak bisa edit entry existing (harus hapus → tambah baru)

### Q3: Maksimal berapa data yang bisa ditambah?
**A**: Tidak ada batasan! Anda bisa menambahkan:
- ∞ Skills
- ∞ Modal
- ∞ Links

### Q4: Foto profil wajib?
**A**: Tidak wajib. Jika tidak upload foto, sistem akan menampilkan inisial nama Anda.

### Q5: Format nomor telepon harus bagaimana?
**A**: Bebas! Contoh yang valid:
- 081234567890
- 0812-3456-7890
- +62 812-3456-7890

### Q6: Apa bedanya Phone dan WhatsApp?
**A**: 
- **Phone**: Nomor telepon biasa
- **WhatsApp**: Nomor WA yang aktif

Bisa diisi sama atau berbeda sesuai kebutuhan.

### Q7: Modal "dapat dipinjam" artinya apa?
**A**: Jika dicentang, artinya anggota lain boleh meminjam alat/resource Anda untuk proyek kolektif. Koordinasi lebih lanjut via Sekretaris.

### Q8: Skill saya tidak ada di contoh, boleh isi bebas?
**A**: Ya! Field "Nama Keahlian" adalah **free text**. Isi dengan skill apapun yang Anda miliki.

### Q9: Bisa hapus semua data sekaligus?
**A**: Tidak bisa bulk delete. Harus hapus satu per satu. Ini untuk mencegah penghapusan tidak sengaja.

### Q10: Data saya bisa diedit oleh Sekretaris?
**A**: Tidak. Sekretaris hanya bisa **melihat** data Anda. Edit dan hapus hanya bisa dilakukan oleh Anda sendiri.

---

## 💡 Tips & Best Practices

### Untuk Member

1. **Update Rutin**
   - Review data setiap 3-6 bulan
   - Hapus skill/modal yang sudah tidak relevan
   - Tambah skill baru yang Anda pelajari

2. **Detail Matters**
   - Isi deskripsi skill dengan spesifik
   - Untuk modal alat, sebutkan spesifikasi (contoh: "Kamera Canon EOS 80D")
   - Link sertakan deskripsi singkat

3. **Contact Info**
   - Pastikan nomor telepon/WA aktif
   - Update jika ganti nomor

4. **Modal Contributions**
   - Jujur tentang kemampuan kontribusi
   - Flag "dapat dipinjam" sesuai kenyataan
   - Update jika kondisi berubah

### Untuk Sekretaris

1. **Regular Review**
   - Check dashboard setiap minggu
   - Follow up dengan member yang datanya kurang lengkap

2. **Data Utilization**
   - Gunakan data untuk assignment proyek
   - Match skill dengan kebutuhan proyek
   - Koordinasi peminjaman modal antar member

3. **Export Periodic**
   - Export CSV setiap bulan untuk backup
   - Buat laporan summary untuk leadership

4. **Privacy Respect**
   - Jangan share data member ke pihak luar tanpa izin
   - Gunakan data hanya untuk keperluan kolektif

---

## 🚨 Troubleshooting

### Foto tidak muncul setelah upload?
1. Coba refresh halaman (Ctrl+F5)
2. Pastikan file max 2MB
3. Gunakan format JPG/PNG/GIF
4. Hubungi admin jika masih bermasalah

### Tombol "Simpan" tidak berfungsi?
1. Pastikan field wajib (*) terisi
2. Check format URL (harus `https://...`)
3. Pastikan jumlah uang berupa angka

### Notifikasi tidak diterima Sekretaris?
1. Cek apakah Anda sudah klik "Simpan & Kirim"
2. Notifikasi muncul di icon 🔔 di header
3. Jika masih tidak muncul, hubungi admin

### Data hilang setelah submit?
Data tidak hilang! Kembali ke halaman "Data Anggota" untuk melihat semua data Anda.

---

## 📞 Butuh Bantuan?

Jika ada kendala atau pertanyaan:

1. **Contact Sekretaris**: Via WA atau direct message
2. **Check Documentation**: `docs/MEMBER_DATA_MANAGEMENT.md`
3. **Report Bug**: Hubungi admin/developer

---

**Selamat menggunakan fitur Data Anggota!** 🎉

Dengan melengkapi data, Anda membantu kolektif untuk:
- ✅ Assign proyek sesuai keahlian
- ✅ Resource planning lebih baik
- ✅ Kolaborasi lebih efektif
- ✅ Transparansi kontribusi member

---

**Terakhir diperbarui**: 21 Oktober 2025

