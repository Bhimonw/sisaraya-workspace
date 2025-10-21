# Browser Push Notification Guide — SISARAYA

## 📌 Apa itu Browser Push Notifications?

Push notifications adalah notifikasi real-time yang muncul di device pengguna (laptop, handphone, tablet) **bahkan ketika browser sedang tidak terbuka**. Notifikasi akan muncul di:
- **Windows**: Action Center (pojok kanan bawah)
- **macOS**: Notification Center (pojok kanan atas)
- **Android**: Status bar & notification drawer
- **iOS**: Lock screen & Notification Center

**Gratis 100%** — tidak ada biaya SMS atau WhatsApp API.

---

## 🚀 Setup Lengkap

### 1. Generate VAPID Keys

VAPID keys adalah kunci enkripsi untuk autentikasi push notification. **Harus dilakukan sekali di awal**.

#### Cara 1: Via Artisan Command (Recommended)
```powershell
php artisan webpush:vapid
```

Jika muncul error **"Unable to create the key"** (OpenSSL issue di Windows), gunakan cara 2.

#### Cara 2: Online Generator (Workaround untuk OpenSSL Issue)
1. Buka browser ke: https://web-push-codelab.glitch.me/
2. Klik tombol **"Generate new keys"**
3. Copy **Public Key** dan **Private Key** yang muncul
4. Paste ke `.env`:
```env
VAPID_PUBLIC_KEY=BMZ7kl8jf... (panjangnya ~88 karakter)
VAPID_PRIVATE_KEY=Xy9kL3m... (panjangnya ~43 karakter)
```

#### Cara 3: Via Node.js Script
Buat file `generate-vapid.js`:
```javascript
const webpush = require('web-push');
const vapidKeys = webpush.generateVAPIDKeys();
console.log('VAPID_PUBLIC_KEY=' + vapidKeys.publicKey);
console.log('VAPID_PRIVATE_KEY=' + vapidKeys.privateKey);
```

Jalankan:
```powershell
npm install web-push
node generate-vapid.js
```

Copy output ke `.env`.

**⚠️ PENTING**: 
- VAPID keys bersifat **PERMANENT** untuk production
- Jangan regenerate setelah production karena akan invalidate semua subscriptions
- Simpan backup di password manager

---

### 2. Update .env File

Tambahkan konfigurasi ini di `.env`:

```env
# Web Push Notifications
VAPID_PUBLIC_KEY=your_public_key_here
VAPID_PRIVATE_KEY=your_private_key_here
VAPID_SUBJECT=mailto:admin@sisaraya.com
```

Ganti `admin@sisaraya.com` dengan email admin sebenarnya.

---

### 3. Jalankan Migration (Sudah dilakukan)

Migration untuk tabel `push_subscriptions` sudah di-run:
```powershell
php artisan migrate
```

Tabel ini menyimpan endpoint subscription dari setiap user/device.

---

### 4. Build Frontend Assets (Sudah dilakukan)

Assets sudah di-build dengan:
```powershell
npm run build
```

Ini menghasilkan:
- `public/build/assets/push-notifications-*.js` (5.05 kB)
- Service Worker di `public/sw.js`

---

## 👤 User Experience Flow

### A. Subscription Flow (User Perspective)

1. **User login** ke SISARAYA
2. **Popup permission muncul** (custom UI, bukan native browser alert):
   ```
   ┌──────────────────────────────────────┐
   │  🔔 Aktifkan Notifikasi Push         │
   │                                      │
   │  Dapatkan notifikasi real-time untuk:│
   │  • Tiket baru ditugaskan             │
   │  • RAB disetujui/ditolak             │
   │  • Update proyek penting             │
   │                                      │
   │  [Aktifkan]  [Nanti]                 │
   └──────────────────────────────────────┘
   ```

3. **User klik "Aktifkan"**:
   - Browser native permission dialog muncul
   - User klik "Allow"
   - Subscription tersimpan di database

4. **User klik "Nanti"**:
   - Popup tidak muncul lagi selama 7 hari
   - Setelah 7 hari, popup muncul lagi

### B. Notification Appearance

Ketika event terjadi (contoh: tiket ditugaskan):

#### Di Windows:
```
┌──────────────────────────────────────┐
│ 🖼️  [Icon]  SISARAYA                 │
│                                      │
│ Tiket Baru Ditugaskan               │
│ "Redesign Landing Page" telah        │
│ ditugaskan kepada Anda               │
│                                      │
│ 🔵 Lihat Detail                      │
│                              [Close] │
└──────────────────────────────────────┘
```

#### Di Android:
```
┌──────────────────────────────────────┐
│ 🔔 SISARAYA • 2 menit lalu           │
│ Tiket Baru Ditugaskan               │
│ "Redesign Landing Page" telah di...  │
└──────────────────────────────────────┘
```

**User klik notifikasi** → Browser auto-buka tab ke halaman detail tiket.

---

## 🔧 Technical Implementation

### Files Created/Modified

#### 1. Backend Files

**a. User Model** (`app/Models/User.php`)
```php
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use HasPushSubscriptions; // ✅ Trait ditambahkan
}
```

**b. Notification Classes**
- `app/Notifications/TicketAssignedNotification.php` — Tiket ditugaskan
- `app/Notifications/RabApprovedNotification.php` — RAB disetujui

Struktur:
```php
public function via($notifiable): array
{
    return ['database', WebPushChannel::class];
}

public function toWebPush($notifiable): WebPushMessage
{
    return (new WebPushMessage)
        ->title('Tiket Baru Ditugaskan')
        ->body($this->ticket->title)
        ->icon('/images/notification-icon.png')
        ->action('Lihat Detail', 'view_ticket')
        ->data(['url' => route('tickets.show', $this->ticket)]);
}
```

**c. Controller Integration**

`app/Http/Controllers/TicketController.php`:
```php
// Send push notification saat ticket di-claim
$user->notify(new TicketAssignedNotification($ticket));
```

`app/Http/Controllers/RabController.php`:
```php
// Send push notification saat RAB di-approve
$rab->creator->notify(new RabApprovedNotification($rab));
```

`app/Http/Controllers/NotificationController.php`:
```php
// Store subscription
public function storePushSubscription(Request $request)
{
    Auth::user()->updatePushSubscription(
        $request->endpoint,
        $request->keys['p256dh'],
        $request->keys['auth']
    );
}

// Delete subscription
public function deletePushSubscription(Request $request)
{
    Auth::user()->deletePushSubscription($request->endpoint);
}
```

#### 2. Frontend Files

**a. Service Worker** (`public/sw.js`)
```javascript
self.addEventListener('push', function(event) {
    const data = event.data.json();
    self.registration.showNotification(data.title, {
        body: data.body,
        icon: data.icon,
        badge: data.badge,
        data: data.data
    });
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    clients.openWindow(event.notification.data.url);
});
```

**b. Subscription Manager** (`resources/js/push-notifications.js`)
- Register service worker
- Request permission dengan custom UI
- Save subscription ke backend via API
- Handle unsubscribe

**c. Layout Update** (`resources/views/layouts/app.blade.php`)
```blade
<meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">
@vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/push-notifications.js'])
```

#### 3. Routes

`routes/web.php`:
```php
Route::middleware('auth')->group(function () {
    // Push Notifications
    Route::post('/push-subscriptions', [NotificationController::class, 'storePushSubscription']);
    Route::delete('/push-subscriptions', [NotificationController::class, 'deletePushSubscription']);
});
```

---

## 🧪 Testing Guide

### 1. Local Development Testing

#### Step 1: Start Development Server
```powershell
composer run dev
```

Atau manual:
```powershell
php artisan serve
npm run dev
php artisan queue:listen
```

**⚠️ IMPORTANT**: Service Worker hanya bekerja di:
- `localhost` (development)
- HTTPS domain (production)

Jangan gunakan IP address seperti `192.168.x.x` — push notification tidak akan bekerja.

#### Step 2: Login & Check Permission Popup

1. Buka browser: http://localhost:8000
2. Login dengan user test (contoh: `bhimo` / `password`)
3. **Popup permission harus muncul** dalam 3 detik
4. Klik **"Aktifkan"**
5. **Browser native permission** muncul → Klik **"Allow"**

#### Step 3: Verify Subscription Saved

Buka browser console (F12):
```javascript
// Should see log:
// "✅ Push subscription saved successfully"
```

Check database:
```powershell
php artisan tinker
>>> DB::table('push_subscriptions')->count();
// Harus > 0
```

#### Step 4: Trigger Test Notification

**Option A: Via Tinker**
```powershell
php artisan tinker
```

```php
$user = User::find(1); // User yang sudah subscribe
$ticket = Ticket::first();
$user->notify(new App\Notifications\TicketAssignedNotification($ticket));
```

**Option B: Via UI (Real Scenario)**
1. Login sebagai user A (contoh: `bhimo`)
2. Subscribe to push notifications
3. Logout
4. Login sebagai PM (contoh: `bagas`)
5. Create ticket dengan `assigned_to_role` = role yang dimiliki user A
6. User A claim ticket
7. **Push notification harus muncul** di device user A

#### Step 5: Verify Notification Received

Cek:
- **Windows**: Action Center (Windows + A)
- **macOS**: Notification Center
- **Android**: Status bar dropdown

Klik notifikasi → Browser harus auto-buka halaman detail ticket.

---

### 2. Common Testing Scenarios

#### Scenario 1: Ticket Assignment
```
1. User login → Subscribe push
2. PM create general ticket for user's role
3. User claim ticket
4. ✅ Push notification: "Tiket Baru Ditugaskan"
```

#### Scenario 2: RAB Approval
```
1. User create RAB
2. User subscribe push
3. Bendahara approve RAB
4. ✅ Push notification: "RAB Disetujui"
```

#### Scenario 3: Multiple Devices
```
1. User subscribe from laptop (Chrome)
2. User subscribe from phone (Android)
3. Trigger notification
4. ✅ Both devices receive notification
```

#### Scenario 4: Unsubscribe
```
1. User block notification via browser settings
2. Frontend auto-detects permission revoked
3. Subscription deleted from database
4. ❌ No more notifications sent
```

---

## 🐛 Troubleshooting

### Issue 1: Permission Popup Tidak Muncul

**Gejala**: User login, tapi popup permission tidak muncul.

**Penyebab**:
- Service Worker gagal register
- VAPID public key tidak ada di meta tag
- User sudah dismiss popup dalam 7 hari terakhir

**Solusi**:
```javascript
// Check di browser console (F12)
console.log(document.querySelector('meta[name="vapid-public-key"]').content);
// Harus ada value, bukan kosong

// Check service worker
navigator.serviceWorker.getRegistrations().then(regs => console.log(regs));
// Harus ada 1 registration

// Clear localStorage untuk reset 7-day cache
localStorage.removeItem('pushPermissionDismissed');
location.reload();
```

---

### Issue 2: "Unable to create the key" Error

**Gejala**: 
```
php artisan webpush:vapid
RuntimeException: Unable to create the key
```

**Penyebab**: OpenSSL configuration issue di Windows (PHP 8.4.5).

**Solusi**: Gunakan online generator (Cara 2 di bagian Setup).

---

### Issue 3: Notification Tidak Muncul di Device

**Gejala**: Subscription berhasil, tapi notifikasi tidak muncul.

**Checklist**:

1. **Queue Running?**
   ```powershell
   php artisan queue:listen
   # Atau
   composer run dev
   ```
   
2. **Notification Channel Correct?**
   ```php
   // Di Notification class
   public function via($notifiable): array
   {
       return ['database', WebPushChannel::class]; // ✅ WebPushChannel harus ada
   }
   ```

3. **User Has Active Subscription?**
   ```php
   php artisan tinker
   >>> $user = User::find(1);
   >>> $user->pushSubscriptions()->count();
   // Harus > 0
   ```

4. **Browser Notification Permission Allowed?**
   - Chrome: Settings → Privacy → Site Settings → Notifications → Check localhost
   - Firefox: Address bar → Lock icon → Permissions → Notifications

5. **Service Worker Active?**
   ```javascript
   // Browser console
   navigator.serviceWorker.controller
   // Harus ada object, bukan null
   ```

---

### Issue 4: Service Worker Not Found (404)

**Gejala**: 
```
Failed to register service worker: 404 Not Found
```

**Penyebab**: File `public/sw.js` tidak ada atau path salah.

**Solusi**:
```powershell
# Verify file exists
ls public/sw.js

# Correct path in resources/js/push-notifications.js:
navigator.serviceWorker.register('/sw.js')  # ✅ Correct
# NOT:
navigator.serviceWorker.register('/public/sw.js')  # ❌ Wrong
```

---

### Issue 5: VAPID Public Key Empty

**Gejala**: 
```javascript
// Browser console
Uncaught TypeError: VAPID public key is null
```

**Penyebab**: `.env` tidak memiliki `VAPID_PUBLIC_KEY` atau config cache outdated.

**Solusi**:
```powershell
# Clear config cache
php artisan config:clear

# Verify .env has key
cat .env | grep VAPID_PUBLIC_KEY

# Restart server
php artisan serve
```

---

### Issue 6: Notification Muncul Tapi Klik Tidak Redirect

**Gejala**: Notification muncul, tapi klik tidak buka halaman detail.

**Penyebab**: `data.url` tidak ada di notification payload.

**Solusi**:
```php
// Di Notification class, pastikan data.url ada:
public function toWebPush($notifiable): WebPushMessage
{
    return (new WebPushMessage)
        ->data([
            'url' => route('tickets.show', $this->ticket->id), // ✅ Harus ada
        ]);
}
```

```javascript
// Di sw.js, pastikan handle click:
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url)  // ✅ Use data.url
    );
});
```

---

## 🎯 Best Practices

### 1. Notification Frequency
**Jangan spam user dengan terlalu banyak notifikasi.**

Good:
- Ticket assigned to me
- RAB approved for my RAB
- Important project updates

Bad:
- Every comment on every ticket
- Every minor status change
- Marketing messages

### 2. Notification Content

**Be specific and actionable:**

✅ Good:
```
Title: "Tiket Baru Ditugaskan"
Body: "Redesign Landing Page telah ditugaskan kepada Anda"
```

❌ Bad:
```
Title: "Notifikasi Baru"
Body: "Ada update di SISARAYA"
```

### 3. Handle Permission Denial Gracefully

```javascript
// Jangan paksa user subscribe
if (Notification.permission === 'denied') {
    console.log('User denied permission, respect their choice');
    // Don't show popup again
}
```

### 4. Test on Multiple Browsers

Push notification behavior berbeda di:
- Chrome/Edge (same engine)
- Firefox
- Safari (macOS/iOS)
- Opera

**Safari membutuhkan HTTPS bahkan untuk localhost** (gunakan ngrok untuk testing).

---

## 📊 Monitoring & Analytics

### Check Subscription Stats

```php
php artisan tinker

// Total active subscriptions
>>> DB::table('push_subscriptions')->count();

// Subscriptions per user
>>> DB::table('push_subscriptions')
    ->select('subscribable_id', DB::raw('count(*) as devices'))
    ->groupBy('subscribable_id')
    ->get();

// Recent subscriptions
>>> DB::table('push_subscriptions')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();
```

### Check Notification Delivery

```php
// Total notifications sent (database channel)
>>> DB::table('notifications')->count();

// Recent notifications
>>> DB::table('notifications')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();
```

---

## 🚀 Production Deployment

### Pre-Deployment Checklist

- [ ] VAPID keys generated & saved in `.env`
- [ ] VAPID keys backed up di password manager
- [ ] `VAPID_SUBJECT` email valid
- [ ] Service Worker accessible di `/sw.js`
- [ ] Assets built: `npm run build`
- [ ] Queue worker running: `php artisan queue:work --daemon`
- [ ] HTTPS enabled (required for push notifications)
- [ ] Test on staging environment first

### Production .env

```env
APP_ENV=production
APP_DEBUG=false

VAPID_PUBLIC_KEY=your_production_public_key
VAPID_PRIVATE_KEY=your_production_private_key
VAPID_SUBJECT=mailto:admin@sisaraya.com

QUEUE_CONNECTION=database  # Atau redis untuk performa lebih baik
```

### Queue Worker Setup (Systemd)

Create `/etc/systemd/system/sisaraya-queue.service`:
```ini
[Unit]
Description=SISARAYA Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/sisaraya/artisan queue:work --sleep=3 --tries=3

[Install]
WantedBy=multi-user.target
```

Enable & start:
```bash
sudo systemctl enable sisaraya-queue
sudo systemctl start sisaraya-queue
```

---

## 🆘 Support & Resources

### Package Documentation
- Laravel WebPush: https://github.com/laravel-notification-channels/webpush
- Web Push Protocol: https://web.dev/push-notifications-overview/
- Service Worker API: https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API

### Browser Compatibility
- Chrome 42+ ✅
- Firefox 44+ ✅
- Edge 17+ ✅
- Safari 16+ ✅ (with caveats)
- Opera 37+ ✅
- IE: ❌ Not supported

### Testing Tools
- Online VAPID Generator: https://web-push-codelab.glitch.me/
- Push Notification Tester: https://tests.peter.sh/notification-generator/
- Service Worker Debugger: Chrome DevTools → Application → Service Workers

---

## 📝 Quick Reference

### Send Notification from Code

```php
use App\Notifications\TicketAssignedNotification;

// Single user
$user->notify(new TicketAssignedNotification($ticket));

// Multiple users
User::whereIn('id', [1,2,3])->each(function($user) use ($ticket) {
    $user->notify(new TicketAssignedNotification($ticket));
});
```

### Check Subscription Status

```javascript
// Frontend
Notification.permission  // "granted", "denied", or "default"

navigator.serviceWorker.ready.then(reg => {
    reg.pushManager.getSubscription().then(sub => {
        console.log(sub ? 'Subscribed' : 'Not subscribed');
    });
});
```

```php
// Backend
$user->pushSubscriptions()->exists()  // true/false
```

### Unsubscribe User

```javascript
// Frontend (automatic on permission revoke)
navigator.serviceWorker.ready.then(reg => {
    reg.pushManager.getSubscription().then(sub => {
        sub.unsubscribe();
    });
});
```

```php
// Backend
$user->pushSubscriptions()->delete();
```

---

## ✅ Implementation Checklist

Setup:
- [x] Package installed (`laravel-notification-channels/webpush`)
- [x] Migration run (`push_subscriptions` table)
- [x] VAPID keys generated (manual via online tool if OpenSSL issue)
- [x] `.env` updated with VAPID keys
- [x] User model has `HasPushSubscriptions` trait

Code:
- [x] Notification classes created (`TicketAssignedNotification`, `RabApprovedNotification`)
- [x] Service Worker created (`public/sw.js`)
- [x] Frontend JS created (`resources/js/push-notifications.js`)
- [x] Layout updated (meta tag + vite)
- [x] Routes created (store/delete subscription)
- [x] Controller methods added (`NotificationController`)
- [x] Integrated in `TicketController::claim()`
- [x] Integrated in `RabController::approve()`

Testing:
- [ ] VAPID keys tested in production
- [ ] Subscription flow tested (permission popup → allow → save)
- [ ] Notification delivery tested (claim ticket → push received)
- [ ] Multiple devices tested
- [ ] Unsubscribe tested
- [ ] Service Worker tested on Chrome/Firefox
- [ ] Queue worker verified running

Production:
- [ ] Assets built (`npm run build`)
- [ ] HTTPS enabled
- [ ] Queue worker as systemd service
- [ ] VAPID keys backed up
- [ ] Monitoring setup (subscription stats, delivery rate)

---

**Selesai!** Push notification system siap digunakan. 🎉

Untuk pertanyaan atau issue, check Troubleshooting section atau contact development team.
