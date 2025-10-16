# Project Rating System

## Overview
Sistem rating proyek memungkinkan anggota tim untuk memberikan penilaian (1-5 bintang) dan komentar terhadap proyek yang telah selesai. Fitur ini membantu dalam evaluasi kualitas proyek dan memberikan feedback yang bermanfaat.

## Fitur Utama

### 1. **Rating dengan Bintang (1-5)**
   - Interface interaktif dengan visual bintang
   - Hover effect untuk user experience yang baik
   - Warna kuning untuk bintang yang dipilih

### 2. **Komentar Opsional**
   - User dapat menambahkan komentar tekstual
   - Textarea dengan placeholder yang jelas
   - Komentar bisa kosong (opsional)

### 3. **Tampilan Average Rating**
   - Badge di header section rating
   - Menampilkan rata-rata rating dengan 1 desimal
   - Total jumlah rating yang diterima

### 4. **Edit dan Delete Rating**
   - User bisa mengupdate rating mereka kapan saja
   - Tombol delete untuk menghapus rating
   - Konfirmasi sebelum delete

## Aturan Bisnis

### Siapa yang Bisa Rating?
- **Project Owner** (PM) - ✅ Bisa rating proyeknya sendiri
- **Project Members** (Aktif) - ✅ Bisa rating proyek yang mereka ikuti
- **Past Members** (Sudah keluar) - ✅ Bisa rating proyek yang pernah mereka ikuti ⭐ **NEW**
- **Non-Members** - ❌ Tidak bisa rating

> **Catatan Penting:** Sistem menggunakan **soft delete** pada tabel `project_user`. Ketika member dihapus dari proyek, data mereka tidak benar-benar dihilangkan, hanya ditandai sebagai deleted. Ini memungkinkan mantan member untuk tetap bisa memberikan rating pada proyek yang sudah selesai, bahkan setelah mereka tidak lagi menjadi anggota aktif.

### Kapan Bisa Rating?
- **Hanya untuk proyek dengan status `completed`**
- Proyek dengan status lain (planning, active, on_hold) tidak bisa dirating
- Validasi dilakukan di backend (controller)
- Member bisa rating kapan saja setelah proyek selesai, bahkan jika mereka sudah keluar dari tim

### Validasi Input
- **Rating**: Wajib diisi, harus antara 1-5 (integer)
- **Comment**: Opsional, maksimal 1000 karakter
- **One rating per user per project**: Unique constraint pada database

## Struktur Database

### Tabel: `project_ratings`
```sql
- id (bigint, primary key, auto increment)
- project_id (foreign key -> projects.id, cascade on delete)
- user_id (foreign key -> users.id, cascade on delete)
- rating (integer, 1-5)
- comment (text, nullable)
- timestamps (created_at, updated_at)
- UNIQUE constraint on (project_id, user_id)
```

### Tabel: `project_user` (Pivot Table with Soft Deletes)
```sql
- project_id (foreign key -> projects.id)
- user_id (foreign key -> users.id)
- role (string, e.g., 'admin', 'member')
- event_roles (json, nullable)
- timestamps (created_at, updated_at)
- deleted_at (timestamp, nullable) ⭐ **NEW** - untuk soft delete
```

> **Soft Delete:** Ketika member dihapus dari proyek, field `deleted_at` diisi dengan timestamp. Member yang sudah dihapus tetap tercatat di database dan masih bisa rating proyek yang sudah completed.

## File-file Terkait

### Backend
1. **Migration**: 
   - `database/migrations/2025_10_16_225041_create_project_ratings_table.php` - Rating table
   - `database/migrations/2025_10_16_230235_add_soft_deletes_to_project_user_table.php` - Soft deletes ⭐ **NEW**
2. **Model**: `app/Models/ProjectRating.php`
   - Relations: belongsTo Project, belongsTo User
   - Fillable: project_id, user_id, rating, comment
3. **Controller**: `app/Http/Controllers/ProjectRatingController.php`
   - `store()`: Create/update rating (includes `withTrashed()` check for past members)
   - `destroy()`: Delete rating
4. **Routes**: `routes/web.php`
   - POST `/projects/{project}/ratings` → store
   - DELETE `/projects/{project}/ratings` → destroy

### Frontend
1. **View**: `resources/views/projects/show.blade.php`
   - Section rating di tab Overview
   - Form dengan Alpine.js untuk star rating
   - Display list semua rating

### Testing
1. **Feature Tests**: `tests/Feature/ProjectRatingTest.php`
   - 8 test scenarios covering all business rules (including past members)

## Cara Penggunaan

### Memberikan Rating (User)
1. Buka halaman detail project yang sudah selesai
2. Scroll ke section "Rating Proyek" (tab Overview)
3. Klik bintang untuk memilih rating (1-5)
4. (Opsional) Tulis komentar di textarea
5. Klik tombol "Simpan Rating"

### Mengupdate Rating
1. Rating form akan menampilkan rating lama jika sudah ada
2. Ubah bintang atau komentar
3. Klik tombol "Update Rating"

### Menghapus Rating
1. Klik link "Hapus Rating" di bawah form
2. Konfirmasi dialog browser
3. Rating akan dihapus dari database

## Method Model

### Project Model
```php
public function members()
{
    return $this->belongsToMany(User::class, 'project_user')
                ->withPivot('role', 'event_roles', 'deleted_at')
                ->withTimestamps()
                ->wherePivotNull('deleted_at'); // Only active members
}

public function allMembers()
{
    return $this->belongsToMany(User::class, 'project_user')
                ->withPivot('role', 'event_roles', 'deleted_at')
                ->withTimestamps(); // All members including soft deleted
}

public function wasEverMember(User $user): bool
{
    return $this->allMembers()->where('user_id', $user->id)->exists();
}

public function ratings()
{
    return $this->hasMany(ProjectRating::class);
}

public function averageRating(): float
{
    return round($this->ratings()->avg('rating') ?? 0, 1);
}
```

### ProjectRating Model
```php
public function project()
{
    return $this->belongsTo(Project::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}
```

## UI/UX Design

### Color Scheme
- **Header**: Gradient amber-500 to orange-500
- **Badge**: White background dengan opacity 20%
- **Stars**: Yellow-400 (active), Gray-300 (inactive)
- **Form Background**: Gradient dari amber-50 ke orange-50
- **Border**: Amber-200 (form), Amber-500 (rating cards)

### Responsive Design
- Grid 1 kolom di mobile
- Grid 2 kolom di desktop (md breakpoint)
- Stars dan buttons responsif dengan hover effects

### Empty State
- Icon bintang outline abu-abu besar
- Text "Belum ada rating"
- Pesan encouragement untuk jadi yang pertama

## Contoh Skenario

### Skenario 1: PM Menyelesaikan Proyek
1. PM mengubah status proyek dari "active" → "completed"
2. Section rating muncul di halaman project
3. PM memberikan rating 5 bintang dengan komentar refleksi
4. Average rating menampilkan 5.0 (1 rating)

### Skenario 2: Team Members Rating
1. 3 anggota tim memberikan rating berbeda: 5, 4, 3
2. Average rating dihitung: (5+4+3)/3 = 4.0
3. Semua rating ditampilkan di grid dengan nama user dan timestamp
4. PM melihat feedback dari tim

### Skenario 3: User Mengubah Pikiran
1. User awalnya memberikan rating 3 dengan komentar negatif
2. Setelah refleksi, user update rating menjadi 5 dengan komentar positif
3. Database hanya menyimpan 1 rating (latest)
4. Average rating terupdate otomatis

### Skenario 4: Past Member Memberikan Rating ⭐ **NEW**
1. Member aktif berpartisipasi dalam proyek dari awal hingga 80% selesai
2. Member keluar dari tim karena alasan pribadi (dihapus dari project_user)
3. Proyek diselesaikan oleh sisa tim dan status diubah ke "completed"
4. Past member masih bisa mengakses halaman proyek dan melihat section rating
5. Past member memberikan rating 4 dengan komentar apresiasi atas pengalamannya
6. Rating tersimpan dan ikut dihitung dalam average rating
7. PM dan tim melihat feedback dari mantan member

## Testing

### Run Tests
```bash
php artisan test --filter=ProjectRatingTest
```

### Test Coverage
- ✅ Members can rate completed projects
- ✅ Non-members cannot rate projects
- ✅ Cannot rate non-completed projects
- ✅ Rating must be between 1 and 5
- ✅ User can update their rating
- ✅ User can delete their rating
- ✅ Average rating calculated correctly
- ✅ Past members can still rate completed projects ⭐ **NEW**

## Known Issues & Future Improvements

### Known Issues
- None currently

### Future Improvements
1. **Email notification** ketika proyek mendapat rating baru
2. **Rating analytics** - chart rata-rata rating per bulan
3. **Filter ratings** - sort by rating, date, user role
4. **Rating categories** - quality, timeliness, collaboration
5. **Minimum rating threshold** - require X ratings before showing average
6. **Anonymous feedback option** untuk komentar sensitif
7. **Like/helpful button** untuk rating yang bermanfaat

## Changelog
- **2025-10-16**: Initial implementation dengan full CRUD dan 7 test scenarios
- **2025-10-16**: Added UI dengan Alpine.js untuk interactive star rating
- **2025-10-16**: Added average rating badge di header section
- **2025-10-16**: Added soft deletes to project_user - past members can now rate ⭐ **NEW**
- **2025-10-16**: Added test scenario for past members rating capability
- **2025-10-16**: Fixed: Use `allMembers()` method instead of `withTrashed()` for BelongsToMany compatibility

## Kontributor
- Development: GitHub Copilot AI Assistant
- Review: Tim SISARAYA

## Referensi
- Laravel Eloquent Relationships: https://laravel.com/docs/eloquent-relationships
- Alpine.js Reactivity: https://alpinejs.dev/essentials/reactivity
- Tailwind CSS Gradients: https://tailwindcss.com/docs/gradient-color-stops
