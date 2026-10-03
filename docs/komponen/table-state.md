# 📋 Table state

`<x-ui.table-state>` menampilkan feedback untuk tabel dan area data: skeleton, tidak ada data, tidak ada hasil filter, atau gagal memuat. Sumber: `resources/views/components/ui/table-state.blade.php`.

## 🧩 Prop dan state

| Prop | Default | Fungsi |
| --- | --- | --- |
| `state` | `empty` | `loading`, `empty`, `filtered`, `error` |
| `title` | `null` | Mengganti judul bawaan, kecuali `loading` |
| `description` | `null` | Mengganti penjelasan bawaan, kecuali `loading` |
| Slot utama | kosong | Tombol pemulihan untuk state selain `loading` |

State `loading` memakai `role="status"` dan skeleton empat baris. `error` memakai `role="alert"`; `empty`/`filtered` memakai `role="status"`. Jika state tidak dikenali, teks default `empty` digunakan, tetapi nilai `state` masih menentukan role/kelas: gunakan hanya empat nilai yang didukung.

## ⏳ Loading, kosong, hasil filter

```blade
<x-ui.table-state state="loading" />
<x-ui.table-state state="empty" title="Belum ada pelanggan"
    description="Pelanggan baru akan muncul setelah transaksi pertama." />
<x-ui.table-state state="filtered" title="Pelanggan tidak ditemukan">
    <a href="{{ route('examples.records.index') }}" class="btn btn-outline">Hapus filter</a>
</x-ui.table-state>
```

Slot pada `loading` tidak dirender; tampilkan tindakan setelah data selesai diproses.

## ❌ Error dengan tindakan pemulihan

```blade
<x-ui.table-state state="error" title="Data gagal dimuat"
    description="Periksa koneksi lalu coba lagi.">
    <a href="{{ request()->fullUrl() }}" class="btn btn-outline">Muat ulang</a>
</x-ui.table-state>
```

## 🗃️ Menentukan state dari query Laravel

```blade
@if ($records->isEmpty())
    <x-ui.table-state :state="request()->filled('q') ? 'filtered' : 'empty'" />
@else
    <table class="table">{{-- baris $records --}}</table>
@endif
```

Saat dipakai di dalam `<table>`, masukkan komponen ke dalam satu `<td colspan="...">` agar markup tabel valid; alternatifnya taruh di luar tabel dalam pembungkus `.card`. Jangan menganggap state `loading` bawaan berjalan otomatis saat query server sinkron; tampilkan hanya ketika benar-benar ada proses asinkron.
