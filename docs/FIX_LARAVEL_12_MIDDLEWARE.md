# Perbaikan Middleware Permission untuk Laravel 12

## Masalah
Error `Target class [permission] does not exist` terjadi ketika mengakses route `/businesses`.

## Akar Masalah
**Laravel 12 menggunakan struktur middleware yang BERBEDA dari Laravel 10/11:**
- Laravel 10/11: Middleware didaftarkan di `app/Http/Kernel.php` 
- **Laravel 12: Middleware didaftarkan di `bootstrap/app.php`**

## Solusi yang Diterapkan
Mendaftarkan middleware Spatie Permission di file yang benar untuk Laravel 12:

### File: `bootstrap/app.php`
```php
->withMiddleware(function (Middleware $middleware): void {
    // Register route middleware aliases
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
    ]);
})
```

## Cara Kerja Route /businesses

### 1. Route Definition
File: `routes/web.php`
```php
Route::middleware(['auth'])->group(function () {
    Route::resource('businesses', BusinessController::class);
});
```

### 2. Controller dengan Permission Middleware
File: `app/Http/Controllers/BusinessController.php`
```php
public function __construct()
{
    $this->middleware('auth');
    $this->middleware('permission:business.create')->only(['create','store']);
    $this->middleware('permission:business.view')->only(['index','show']);
}

public function index()
{
    $businesses = Business::latest()->paginate(12);
    return view('businesses.index', compact('businesses'));
}
```

### 3. Halaman yang Ditampilkan
File: `resources/views/businesses/index.blade.php`

**Elemen UI:**
- **Judul**: "Usaha Komunitas"
- **Tombol**: "Buat Usaha Baru" (hanya muncul jika user memiliki permission `business.create`)
- **Daftar Usaha**: Card yang menampilkan:
  - Nama usaha (link ke detail)
  - Deskripsi (terpotong 120 karakter)
  - Status usaha (di sebelah kanan)
- **Pagination**: Links untuk navigasi halaman

### 4. Permissions yang Dibutuhkan

**Untuk mengakses halaman index:**
- Permission: `business.view`
- Role: `kewirausahaan` (sudah memiliki permission ini via RolePermissionSeeder)

**Untuk membuat usaha baru:**
- Permission: `business.create`
- Route: `/businesses/create`

**Untuk melihat detail usaha:**
- Permission: `business.view`
- Route: `/businesses/{id}`

## Testing
1. Login sebagai user dengan role `kewirausahaan` (contoh: user ID 13 - Kafilah)
2. Akses: `http://localhost:8000/businesses`
3. **Harapan**: Halaman "Usaha Komunitas" ditampilkan dengan daftar usaha
4. Tombol "Buat Usaha Baru" terlihat (karena user memiliki `business.create`)

## Catatan Penting untuk Developer
- **Laravel 12**: JANGAN edit `app/Http/Kernel.php` untuk middleware
- **Gunakan `bootstrap/app.php`** dengan method `$middleware->alias([...])`
- Setelah mengubah `bootstrap/app.php`, **server harus direstart**
- Cache tidak perlu dihapus untuk perubahan middleware di Laravel 12

## Referensi
- Laravel 12 Migration Guide: https://laravel.com/docs/12.x/upgrade
- Spatie Laravel Permission: https://spatie.be/docs/laravel-permission/v6
