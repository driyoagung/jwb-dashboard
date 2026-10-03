# 🧿 Icon

`<x-ui.icon>` merender SVG `<use>` yang menunjuk `public/icons.svg#i-{name}`. Sumber: `resources/views/components/ui/icon.blade.php`.

## 🧩 Prop dan sumber simbol

| Prop | Default | Fungsi |
| --- | --- | --- |
| `name` | wajib | Akhiran ID sprite (`search` → `i-search`) |
| `class` | `h-4 w-4` | Ukuran dan kelas warna/posisi SVG |

SVG bawaan memiliki `aria-hidden="true"` dan `focusable="false"`. Ikon tidak menyampaikan teks alternatif sendiri; label aksi harus berada pada tombol/tautan induk.

## 📐 Ikon pada teks dan kartu

```blade
<span class="inline-flex items-center gap-2">
    <x-ui.icon name="check-circle" class="h-4 w-4 text-success" />
    Pesanan selesai
</span>
<x-ui.card title="Pesanan terbaru">
    <x-ui.icon name="cart" class="h-6 w-6 text-primary" />
</x-ui.card>
```

## 🖱️ Tombol ikon saja

```blade
<x-ui.button size="icon" variant="outline" aria-label="Buka pencarian">
    <x-ui.icon name="search" />
</x-ui.button>
```

Tanpa `aria-label`, tombol hanya memiliki SVG dekoratif dan tidak punya nama yang jelas bagi pembaca layar.

## 🧭 Menambah ikon baru

Tambahkan `<symbol id="i-nama" ...>` ke `public/icons.svg` dengan ukuran `viewBox` dan gaya stroke yang konsisten, lalu gunakan `name="nama"`. Untuk pilihan ikon yang ada, lihat ID `i-...` dalam berkas itu; contoh yang dipakai proyek: `search`, `plus`, `settings`, `chart`, `coffee`, `check-circle`.

Ada juga `<x-icon>` yang menunjuk `#i-{name}` **tanpa path file**, sehingga memerlukan sprite inline `<x-icon.sprite>` dalam dokumen. Layout admin aktif memakai sprite publik dan `x-ui.icon`; jangan mencampur kedua mekanisme tanpa memuat sprite yang sesuai.
