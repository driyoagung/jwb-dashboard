# 🔘 Button

<ComponentPreview name="button" :height="220" />

`<x-ui.button>` merender `<button>` dengan gaya yang konsisten. Sumber: `resources/views/components/ui/button.blade.php`; contoh visual: `/components/buttons`.

## 🧩 API dan elemen yang dihasilkan

| Prop | Default | Fungsi |
| --- | --- | --- |
| `variant` | `primary` | Gaya visual dari daftar di bawah |
| `size` | `default` | `default`, `sm`, `lg`, atau `icon` |
| `type` | `button` | Tipe HTML, misalnya `submit` atau `reset` |
| Slot utama | — | Teks dan/atau ikon di dalam tombol |

Atribut lain seperti `disabled`, `name`, `value`, `aria-label`, `data-toast`, dan `data-modal-open` diteruskan ke `<button>`. Nama `variant`/`size` yang tidak dikenal jatuh kembali ke default.

## 🎨 Semua variant

```blade
<x-ui.button>Utama</x-ui.button>
<x-ui.button variant="secondary">Sekunder</x-ui.button>
<x-ui.button variant="outline">Garis tepi</x-ui.button>
<x-ui.button variant="ghost">Tanpa bidang</x-ui.button>
<x-ui.button variant="soft">Sorotan lembut</x-ui.button>
<x-ui.button variant="destructive">Hapus</x-ui.button>
<x-ui.button variant="success">Berhasil</x-ui.button>
<x-ui.button variant="warning">Perhatian</x-ui.button>
<x-ui.button variant="info">Informasi</x-ui.button>
<x-ui.button variant="link">Aksi bergaya tautan</x-ui.button>
```

Variant `link` **masih tombol**, bukan navigasi. Untuk berpindah halaman gunakan `<a href="..." class="btn btn-primary">`.

## 📏 Ukuran, ikon, dan status

```blade
<x-ui.button size="sm">Kecil</x-ui.button>
<x-ui.button size="lg"><x-ui.icon name="plus" /> Tambah data</x-ui.button>
<x-ui.button size="icon" variant="outline" aria-label="Buka filter" data-modal-open="#filter">
    <x-ui.icon name="filter" />
</x-ui.button>
<x-ui.button disabled>Menunggu proses</x-ui.button>
```

Tombol hanya berisi ikon perlu `aria-label`. `disabled` bawaan browser mencegah klik; untuk menampilkan loading nyata, hubungkan status tombol ke request, bukan `data-load` demo yang hanya menunggu timer.

## 📨 Submit Laravel dan konfirmasi hapus

```blade
<form method="POST" action="{{ route('examples.records.store') }}">
    @csrf
    <x-ui.button type="submit" variant="primary">Simpan entri</x-ui.button>
</form>

<form method="POST" action="{{ route('examples.records.destroy', $record) }}">
    @csrf
    @method('DELETE')
    <x-ui.button type="submit" variant="destructive">Hapus entri</x-ui.button>
</form>
```

Route `examples.records.store` dan `destroy` pada contoh perlu ditambahkan mengikuti [panduan CRUD](/integrasi/crud). `data-toast` dan `data-load` hanyalah perilaku frontend; tampilkan sukses setelah server mengonfirmasi melalui redirect/flash. Lihat juga [modal](/komponen/modal) untuk menempatkan form hapus dalam dialog.
