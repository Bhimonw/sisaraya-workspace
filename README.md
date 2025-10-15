# SISARAYA - Ruang Kerja Platform<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>



![SISARAYA Logo](public/logo-no-bg.png)<p align="center">

<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>

**Kolektif Kreatif Lintas Bidang**<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>

<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>

---<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>

</p>

## 📋 Tentang SISARAYA

## About Laravel

**SISARAYA** adalah platform manajemen workspace yang dirancang khusus untuk komunitas kreatif yang bekerja di berbagai bidang — dari **Event Organizer**, **musik & band**, hingga **kewirausahaan** dan **media kreatif**.

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

Platform ini memfasilitasi kolaborasi tim, manajemen proyek, tracking tiket, voting, dan berbagai fitur lainnya yang mendukung produktivitas komunitas.

- [Simple, fast routing engine](https://laravel.com/docs/routing).

---- [Powerful dependency injection container](https://laravel.com/docs/container).

- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.

## ✨ Fitur Utama- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).

- Database agnostic [schema migrations](https://laravel.com/docs/migrations).

### 🎯 **Manajemen Proyek & Tiket**- [Robust background job processing](https://laravel.com/docs/queues).

- **Proyek**: Kelola proyek dengan detail, timeline, dan anggota tim- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

- **Tiket**: Sistem tracking tugas dengan status, prioritas, dan assignment

- **Kalender**: Visualisasi proyek dan event dalam timelineLaravel is accessible, powerful, and provides tools required for large, robust applications.



### 👥 **Role-Based Access Control**## Learning Laravel

- **Project Manager (PM)**: Mengelola semua aspek proyek dan event

- **Sekretaris**: Dokumentasi dan administrasiLaravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

- **HR**: Manajemen anggota dan voting

- **Bendahara**: Kelola RAB dan keuanganYou may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

- **PR**: Public relations dan komunikasi eksternal

- **Media**: Konten, desain, dan publikasiIf you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

- **Guest**: Akses terbatas untuk melihat progress

## Laravel Sponsors

### 📊 **RAB & Laporan Keuangan**

- Buat dan kelola RAB (Rencana Anggaran Biaya)We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

- Approval workflow untuk bendahara

- Laporan keuangan proyek### Premium Partners



### 📄 **Ruang Dokumen**- **[Vehikl](https://vehikl.com)**

- Upload dan kelola dokumen proyek- **[Tighten Co.](https://tighten.co)**

- Kategorisasi dan pencarian- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**

- Version control- **[64 Robots](https://64robots.com)**

- **[Curotec](https://www.curotec.com/services/technologies/laravel)**

### 🗳️ **Sistem Voting**- **[DevSquad](https://devsquad.com/hire-laravel-developers)**

- Voting untuk keputusan komunitas- **[Redberry](https://redberry.international/laravel-development)**

- Voting anggota baru- **[Active Logic](https://activelogic.com)**

- Transparansi hasil voting

## Contributing

### 🔐 **Keamanan & Privasi**

- Role-based permissions (Spatie Laravel Permission)Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

- Username-based authentication

- Session management## Code of Conduct

- Rate limiting

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

---

## Security Vulnerabilities

## 🚀 Quick Start

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

### **Prerequisites**

- PHP 8.4+## License

- Composer

- Node.js & NPMThe Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

- SQLite (default) atau MySQL/PostgreSQL

## Project setup (quick)

### **Installation**

Run the app locally and create initial roles/permissions:

1. **Clone Repository**

```bash```powershell

git clone <repository-url>cd d:\Code\Program\RuangKerjaSisaraya\ruangkerja-mvp

cd ruangkerja-mvpcomposer install

```npm install

cp .env.example .env

2. **Install Dependencies**php artisan key:generate

```bashphp artisan migrate

composer installphp artisan db:seed --force

npm installnpm run build

``````



3. **Environment Setup**Untuk development dengan hot reload:

```bash```powershell

cp .env.example .envnpm run dev

php artisan key:generate```

```

Kemudian di terminal terpisah:

4. **Database Setup**```powershell

```bashphp artisan serve

# Create SQLite database (jika belum ada)```

touch database/database.sqlite

If you need to migrate legacy uppercase role names to the normalized role names, preview first then run:

# Run migrations and seeders

php artisan migrate --seed```powershell

```# preview only

php artisan roles:migrate-legacy --dry-run

5. **Build Assets**

```bash# apply changes

npm run buildphp artisan roles:migrate-legacy

# atau untuk development```

npm run dev

```If you're using document uploads, create a storage symlink:



6. **Start Server**```powershell

```bashphp artisan storage:link

php artisan serve```

```

Docs & implemented features:

7. **Access Application**

- URL: `http://127.0.0.1:8000` - See `docs/INDEX.md` and `docs/PROGRESS_IMPLEMENTASI.md` (Bahasa Indonesia) for a summary of implemented features and where to find code.

- Login dengan test users (lihat LOGIN_TESTING_GUIDE.md) - **Progress MVP Fase 1: 100% selesai** ✅

 

---Key Features yang sudah tersedia:

- ✅ Autentikasi & 11 Role (HR, PM, Bendahara, Sekretaris, Media, PR, Kewirausahaan, Talent Manager, Researcher, Talent, Guest)

## 👥 Test Users- ✅ Dashboard & Kanban Board (drag & drop)

- ✅ Manajemen Proyek & Tiket (claim ticket untuk anggota)

**Default Password untuk SEMUA users:** `password`- ✅ RAB dengan approval Bendahara

- ✅ Event Management dengan event roles

| Username | Role | Description |- ✅ Voting anggota baru (quorum 50%, duplicate protection)

|----------|------|-------------|- ✅ Kewirausahaan (business tracking)

| bhimo | PM & Sekretaris | Project Manager dan Sekretaris |- ✅ Ruang Penyimpanan (document upload)

| bagas | HR | Human Resource |- ✅ **Kalender FullCalendar** (personal calendar & project calendar dengan month/week/day view)

| dijah | Bendahara | Bendahara |

| yahya | PR | Head of Public Relations |Untuk detail lengkap dan checklist dari 6 dokumen requirement, buka `docs/PROGRESS_IMPLEMENTASI.md`

| fadhil | PR | PR Staff |

| robby | Media | Main Designer |

| fauzan | Media | Content Planner |Documentation workflow (please follow):

| aulia | Media | Social Media Specialist |

| faris | Media | Graphic Designer |- When you make a change that affects behavior, add a short entry to `docs/CHANGELOG.md` describing the change.

| ardhi | Media | Media Editor |- You can use the helper script to append an entry automatically:

| fikri | Member | Anggota |

| iqbal | Member | Anggota |```powershell

| krisna | Member | Anggota |php tools/update-docs.php "Short summary of change"

| nata | Member | Anggota |```



---- We've included a sample Git hook at `tools/githooks/post-merge.sample` that reminds developers to update docs after merges. To enable it locally:



## 📚 Dokumentasi```powershell

copy tools\githooks\post-merge.sample .git\hooks\post-merge

Dokumentasi lengkap tersedia di folder root dan docs:# make executable if needed (on Windows this is optional)

```

- **[LOGIN_TESTING_GUIDE.md](../LOGIN_TESTING_GUIDE.md)** - Panduan testing login

- **[MENU_RESTRUCTURE.md](../MENU_RESTRUCTURE.md)** - Struktur menu dan akses

- **[LANDING_PAGE_UPDATE.md](../LANDING_PAGE_UPDATE.md)** - Dokumentasi landing page

- **[LOGO_UPDATE.md](../LOGO_UPDATE.md)** - Update logo no backgroundThe seeder will create role names like `hr`, `pm`, `bendahara`, `sekretaris`, `media`, `pr`, `talent_manager`, `researcher`, `talent`, `guest` and example permissions (users.manage, projects.*, tickets.*, documents.*, finance.*).

- **[docs/00_START_HERE.md](docs/00_START_HERE.md)** - Panduan awal

- **[docs/CALENDAR_SYSTEM.md](docs/CALENDAR_SYSTEM.md)** - Sistem kalender## Permissions mapping (developer)

- **[docs/EVENT_PROJECT_RELATIONSHIP.md](docs/EVENT_PROJECT_RELATIONSHIP.md)** - Relasi event & proyek

This project uses `spatie/laravel-permission`. The seeder creates a small set of permissions you can use in routes/controllers/policies.

---

- Example permission names created by the seeder:

## 🛠️ Tech Stack	- `users.manage` — manage user CRUD (HR)

	- `projects.create`, `projects.update`, `projects.view`, `projects.manage_members` — project management (PM)

### **Backend**	- `tickets.create`, `tickets.update_status`, `tickets.view_all` — ticket actions

- **Framework**: Laravel 12.33.0	- `documents.upload`, `documents.view_all` — document storage access

- **PHP**: 8.4.5	- `finance.*` (e.g., `finance.manage_rab`) — financial operations for Bendahara

- **Database**: SQLite (default), MySQL/PostgreSQL support	- `business.*` (e.g., `business.manage_talent`) — kewirausahaan/business operations

- **Authentication**: Laravel Breeze (Username-based)

- **Permissions**: Spatie Laravel Permission v6.21- Usage examples:

	- Protect a route with middleware:

### **Frontend**

- **CSS Framework**: Tailwind CSS 3.x		```php

- **JavaScript**: Alpine.js		Route::middleware(['permission:users.manage'])->group(function () {

- **Build Tool**: Vite 7.1.9				Route::resource('admin/users', Admin\UserController::class);

- **Icons**: Heroicons (SVG)		});

- **Fonts**: Inter, Playfair Display		```



### **Additional Packages**	- Check in controller / policy:

- spatie/laravel-permission - Role & Permission management

- laravel/tinker - REPL untuk Laravel		```php

- laravel/pail - Log viewer		if ($request->user()->can('finance.manage_rab')) {

				// allow RAB edit

---		}

		```

## 📂 Project Structure

	- Use in Blade to show UI conditionally:

```

ruangkerja-mvp/		```blade

├── app/		@can('business.create')

│   ├── Http/				<a href="{{ route('business.create') }}">Buat Usaha Baru</a>

│   │   ├── Controllers/     # Controllers (Projects, Tickets, RAB, etc.)		@endcan

│   │   ├── Middleware/      # RoleMiddleware		```

│   │   └── Requests/        # LoginRequest, etc.

│   ├── Models/              # User, Project, Ticket, RAB, Document, VoteIf you extend permissions, add them to `RolePermissionSeeder.php` and re-run `php artisan db:seed --force`, or create a new seeder for production-safe changes.

│   └── Policies/            # Authorization policies

├── database/

│   ├── migrations/          # Database schema
│   ├── seeders/             # RolesSeeder, SisarayaMembersSeeder
│   └── database.sqlite      # SQLite database file
├── public/
│   ├── favicon.ico          # SISARAYA favicon (no bg)
│   ├── logo-no-bg.png       # Logo transparent
│   ├── Logo.png             # Logo original
│   └── Asset.jpg            # Hero background image
├── resources/
│   ├── css/
│   │   └── app.css          # Tailwind imports
│   ├── js/
│   │   └── app.js           # Alpine.js setup
│   └── views/
│       ├── auth/            # login.blade.php
│       ├── layouts/         # app.blade.php, _menu.blade.php
│       ├── welcome.blade.php # Landing page
│       ├── dashboard.blade.php
│       ├── projects/        # index, show, create, edit
│       ├── tickets/         # mine, overview, create, show
│       ├── rabs/            # index, create, show
│       ├── documents/       # index, create
│       └── votes/           # index, create, show
├── routes/
│   ├── web.php              # Main routes
│   └── auth.php             # Auth routes (register disabled)
├── docs/                    # Additional documentation
└── .env                     # Environment configuration
```

---

## 🎨 Design System

### **Color Palette (Gradient Theme)**
```css
/* Primary Gradient */
Violet:  #8b5cf6 (rgb(139, 92, 246))
Blue:    #3b82f6 (rgb(59, 130, 246))
Emerald: #10b981 (rgb(16, 185, 129))

/* Supporting Colors */
Purple:  #a855f7
Pink:    #ec4899
Cyan:    #06b6d4
Teal:    #14b8a6
```

### **Typography**
- **Display/Headings**: Playfair Display (Serif, Bold)
- **Body Text**: Inter (Sans-serif, 400/500/600/700)

### **Components**
- Gradient buttons and badges
- Card hover effects (lift + shadow)
- Glassmorphism navigation
- Animated gradients
- Responsive grid layouts

---

## 🔒 Security Features

- ✅ **Username-based authentication** (bukan email)
- ✅ **Password hashing** (Bcrypt)
- ✅ **CSRF protection** (automatic)
- ✅ **Rate limiting** (5 login attempts max)
- ✅ **Session regeneration** (after login)
- ✅ **Role-based access control** (Spatie Permission)
- ✅ **Guest access restrictions** (limited menu)
- ✅ **Public registration DISABLED** (HR creates users only)

---

## 🧪 Testing

### **Manual Testing**
Lihat **[LOGIN_TESTING_GUIDE.md](../LOGIN_TESTING_GUIDE.md)** untuk:
- Testing login flow
- All test users credentials
- Role-based access testing
- Rate limiting testing
- Remember me functionality

### **Feature Testing Checklist**
- [ ] Login dengan berbagai users
- [ ] Create, edit, delete projects
- [ ] Create, assign, update tickets
- [ ] Create dan approve RAB
- [ ] Upload documents
- [ ] Create dan vote pada voting
- [ ] Guest user access restrictions
- [ ] Calendar view
- [ ] Mobile responsiveness

---

## 📝 Important Notes

### **1. Public Registration Disabled**
```php
// routes/auth.php (intentionally commented out)
// Route::get('register', [RegisteredUserController::class, 'create'])
//     ->name('register');
// Route::post('register', [RegisteredUserController::class, 'store']);
```
**Reason:** Only HR role dapat membuat user baru melalui admin panel untuk kontrol akses yang lebih baik.

### **2. Guest User Restrictions**
User dengan role 'guest' hanya dapat mengakses:
- ✅ Dashboard (read-only overview)
- ✅ Meja Kerja → Semua Proyek (view only)
- ✅ Meja Kerja → Semua Tiket (view only)
- ✅ Akun & Pengaturan (profile only)

Tidak dapat mengakses:
- ❌ Ruang Pribadi (Proyek Saya, Tiketku)
- ❌ RAB & Laporan
- ❌ Ruang Dokumen
- ❌ Voting
- ❌ Ruang Management

### **3. Multiple Roles Support**
User dapat memiliki multiple roles:
```php
// Example: Bhimo has both PM and Sekretaris roles
$user->assignRole(['pm', 'sekretaris']);
```

### **4. Database**
Default: SQLite (file-based, no server needed)
- Location: `database/database.sqlite`
- Easy backup (just copy the file)
- For production: consider MySQL/PostgreSQL

---

## 🤝 Support & Contact

**SISARAYA**  
Kolektif Kreatif Lintas Bidang

📞 **WhatsApp / Telepon**: +62 813-5601-9609

Untuk pertanyaan, bug report, atau feature request, silakan hubungi kontak di atas.

---

## 📄 License

**Proprietary License**  
© 2025 SISARAYA. All rights reserved.

This software is proprietary and confidential. Unauthorized copying, distribution, or use is strictly prohibited.

---

## 🎉 About SISARAYA

**Satu semangat, banyak ekspresi.**

SISARAYA adalah komunitas yang bekerja di berbagai bidang — dari Event Organizer, musik & band, hingga kewirausahaan dan media kreatif. Kami percaya bahwa kolaborasi adalah bahan bakar utama untuk tumbuh bersama.

Platform Ruang Kerja ini dikembangkan dengan ❤️ untuk mendukung kolaborasi dan produktivitas komunitas SISARAYA.

---

**Powered by Laravel Framework, Tailwind CSS, Alpine.js, and Spatie Laravel Permission.**
