# Modal Form UX Improvements

**Date**: October 21, 2025  
**Status**: ✅ Complete  
**Branch**: profile

## Changes Made

### 1. Links Tab - Renamed Label
**Before**: "Nama Link/Platform"  
**After**: "Nama Orang/Pemilik"

**Reasoning**: Field ini untuk menyimpan nama orang atau entitas pemilik link/kontak, bukan nama platform.

**Example Values**:
- ✅ "John Doe"
- ✅ "PT. Maju Jaya"
- ✅ "Jane Smith - Designer"
- ❌ "LinkedIn" (ini bukan nama orang)

### 2. Modal Tab - Conditional Field

#### For "Uang" (Money)
- ❌ **Field "Nama Item" HIDDEN**
- ✅ Auto-filled dengan value: "Modal Uang Tunai"
- Reasoning: Uang tidak perlu nama barang, karena uang ya uang

**UI Behavior**:
```
[•] Uang  [ ] Alat
↓
[Field Nama Item tidak muncul]
[Jumlah Uang (Rp)]  → Rp 1.000.000
[Deskripsi]         → "Modal untuk operasional"
```

#### For "Alat" (Equipment)
- ✅ **Field "Nama Alat/Barang" SHOWN**
- Required field
- Placeholder: "Contoh: Kamera Canon, Laptop Dell"

**UI Behavior**:
```
[ ] Uang  [•] Alat
↓
[Nama Alat/Barang*] → Kamera Canon EOS 80D
[Deskripsi]         → "Untuk dokumentasi acara"
[✓] Dapat dipinjam
```

## Implementation Details

### Alpine.js Conditional Rendering

**Field Nama Alat** (only for "alat"):
```blade
<div x-show="jenis === 'alat'" x-cloak>
    <label class="block text-sm font-semibold text-gray-700 mb-2">
        Nama Alat/Barang *
    </label>
    <input type="text" 
           name="modals[0][nama_item]" 
           :required="jenis === 'alat'" 
           placeholder="Contoh: Kamera Canon, Laptop Dell"
           class="...">
</div>
```

**Hidden Input for "uang"**:
```blade
<div x-show="jenis === 'uang'" x-cloak>
    <input type="hidden" 
           name="modals[0][nama_item]" 
           value="Modal Uang Tunai">
</div>
```

### Form Flow

#### Scenario 1: User pilih "Uang"
1. User klik radio "💵 Uang"
2. Field "Nama Item" HILANG (x-show="jenis === 'alat'")
3. Hidden input dengan value "Modal Uang Tunai" MUNCUL
4. User isi jumlah uang: `Rp 5.000.000`
5. Submit → Database: `{ jenis: 'uang', nama_item: 'Modal Uang Tunai', jumlah_uang: 5000000 }`

#### Scenario 2: User pilih "Alat"
1. User klik radio "🛠️ Alat"
2. Field "Nama Alat/Barang" MUNCUL
3. User isi nama alat: "Kamera Canon"
4. Hidden input untuk uang HILANG
5. Submit → Database: `{ jenis: 'alat', nama_item: 'Kamera Canon', jumlah_uang: null }`

## Database Impact

### `member_modals` Table

| Field | Jenis = "uang" | Jenis = "alat" |
|-------|----------------|----------------|
| `jenis` | `'uang'` | `'alat'` |
| `nama_item` | `'Modal Uang Tunai'` (auto) | User input (required) |
| `jumlah_uang` | User input (formatted) | `NULL` |
| `dapat_dipinjam` | Optional | Optional |

### Example Records

**Record 1 - Uang**:
```json
{
  "id": 1,
  "user_id": 1,
  "jenis": "uang",
  "nama_item": "Modal Uang Tunai",
  "jumlah_uang": 5000000,
  "deskripsi": "Modal untuk operasional proyek X",
  "dapat_dipinjam": false
}
```

**Record 2 - Alat**:
```json
{
  "id": 2,
  "user_id": 1,
  "jenis": "alat",
  "nama_item": "Kamera Canon EOS 80D",
  "jumlah_uang": null,
  "deskripsi": "Untuk dokumentasi acara",
  "dapat_dipinjam": true
}
```

## UX Benefits

### Before ❌
- User bingung isi apa di "Nama Item" untuk uang
- "Nama Link/Platform" tidak jelas maksudnya
- Inconsistent form (semua field muncul untuk semua jenis)

### After ✅
- Form lebih fokus: hanya field yang relevan yang muncul
- "Nama Orang/Pemilik" lebih jelas untuk contacts
- Auto-fill "Modal Uang Tunai" untuk uang → user tidak perlu mikir
- Better UX: conditional fields = less confusion

## Validation

### Frontend Validation (Alpine.js)
- `nama_item` required **hanya** untuk `jenis === 'alat'`
- `:required="jenis === 'alat'"` on input element

### Backend Validation (Unchanged)
Controller validation masih sama di `MemberDataController.php`:
```php
'modals.*.jenis' => 'required|in:uang,alat',
'modals.*.nama_item' => 'required|string|max:255',
'modals.*.jumlah_uang' => 'nullable|integer|min:0',
```

Backend tetap expect `nama_item` (provided by hidden input for "uang").

## Testing Checklist

### Test Case 1: Modal Uang
- [ ] Pilih tab "Modal"
- [ ] Pilih radio "💵 Uang"
- [ ] Verify: Field "Nama Item" **TIDAK MUNCUL**
- [ ] Isi jumlah uang: `1000000`
- [ ] Verify: Format menjadi `Rp 1.000.000`
- [ ] Submit form
- [ ] Check database: `nama_item = 'Modal Uang Tunai'`

### Test Case 2: Modal Alat
- [ ] Pilih tab "Modal"
- [ ] Pilih radio "🛠️ Alat"
- [ ] Verify: Field "Nama Alat/Barang" **MUNCUL**
- [ ] Isi nama alat: "Laptop HP"
- [ ] Submit form
- [ ] Check database: `nama_item = 'Laptop HP'`, `jumlah_uang = null`

### Test Case 3: Links
- [ ] Pilih tab "Link & Kontak"
- [ ] Verify: Label adalah "Nama Orang/Pemilik"
- [ ] Isi nama: "John Doe"
- [ ] Isi bidang: "Portfolio"
- [ ] Submit form
- [ ] Check database: `nama = 'John Doe'`

## Display Updates Needed

⚠️ **Note**: Perlu update juga di view `index.blade.php` untuk display:

### Current Display (May Need Update)
```blade
<h4 class="font-bold text-gray-900 text-lg mb-2">
    {{ $modal->nama_item }}
</h4>
```

### Suggested Display Logic
```blade
@if($modal->jenis == 'uang')
    <h4 class="font-bold text-gray-900 text-lg mb-2">
        Modal Uang Tunai
    </h4>
@else
    <h4 class="font-bold text-gray-900 text-lg mb-2">
        {{ $modal->nama_item }}
    </h4>
@endif
```

Or just keep it as is since `nama_item` will always have value (either user input or "Modal Uang Tunai").

## Related Files

- ✅ `resources/views/member-data/_add-data-modal.blade.php` (Modified)
- ℹ️ `resources/views/member-data/index.blade.php` (Display - may need update)
- ℹ️ `app/Http/Controllers/MemberDataController.php` (No changes needed)

## Changelog Entry

```
[2025-10-21] Changed Links label to 'Nama Orang/Pemilik' and made 'Nama Item' 
conditional (only for 'alat', hidden for 'uang')
```

---

**Implementation Status**: ✅ Complete  
**Backend Changes**: None required  
**Frontend Only**: Yes  
**Breaking Changes**: No
