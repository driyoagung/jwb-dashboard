# 📢 Alert

<ComponentPreview name="alert" :height="510" />

`<x-ui.alert>` adalah pesan **inline** yang tetap berada di halaman, berbeda dengan toast yang hilang setelah beberapa detik. Sumber: `resources/views/components/ui/alert.blade.php`.

## 🧩 Prop dan slot

| Input | Default | Fungsi |
| --- | --- | --- |
| `variant` | `info` | `info`, `success`, `warning`, `danger` |
| `title` | `null` | Judul tebal di atas isi |
| `solid` | `false` | Gaya bidang lebih menonjol |
| Slot utama | — | Isi pesan; dapat berisi tautan |
| `action` | tidak ada | Tombol/tautan di sisi kanan |

Variant menentukan ikon dan kelas `alert-{variant}`; hanya `danger` yang diberi `role="alert"` otomatis. Gunakan nilai variant yang tersedia, karena kelasnya disusun dari prop.

## 🎨 Empat nada semantik

```blade
<x-ui.alert variant="info" title="Informasi">Pengaturan berlaku setelah halaman dimuat ulang.</x-ui.alert>
<x-ui.alert variant="success" title="Tersimpan">Perubahan profil berhasil disimpan.</x-ui.alert>
<x-ui.alert variant="warning" title="Stok menipis">Periksa produk yang perlu diisi ulang.</x-ui.alert>
<x-ui.alert variant="danger" title="Gagal memuat">Coba lagi setelah memeriksa koneksi.</x-ui.alert>
```

## 🖱️ Aksi dan versi solid

```blade
<x-ui.alert variant="warning" title="Sinkronisasi tertunda" :solid="true">
    Data terbaru belum tersedia.
    <x-slot:action>
        <a href="{{ request()->fullUrl() }}" class="btn btn-outline btn-sm">Muat ulang</a>
    </x-slot:action>
</x-ui.alert>
```

Slot `action` tidak membuat perilaku klik; hubungkan tautannya dengan route atau tombolnya dengan aksi sendiri. Komponen juga tidak memasang tombol dismiss otomatis; jika perlu, lihat pola `data-dismissible`/`data-dismiss` pada `/components/feedback`.

## ✅ Pesan dari controller

```php
return redirect()->route('admin.settings')->with('status', 'Pengaturan tersimpan.');
```

```blade
@if (session('status'))
    <x-ui.alert variant="success" title="Berhasil">{{ session('status') }}</x-ui.alert>
@endif
```

Blade meng-escape ekspresi output pada template. Untuk error form per kolom, gunakan [field](/komponen/field); untuk kegagalan yang harus tetap terbaca di halaman, alert lebih tepat daripada toast.
