# 📭 Empty

<ComponentPreview name="empty" :height="290" />

`<x-ui.empty>` adalah placeholder untuk bagian/halaman yang benar-benar belum memiliki konten, di luar tabel. Sumber: `resources/views/components/ui/empty.blade.php`.

## 🧩 API

| Prop | Default | Fungsi |
| --- | --- | --- |
| `icon` | `file-text` | Ikon pada bidang lingkaran |
| `title` | `Belum ada data` | Judul kosong |
| `description` | `null` | Penjelasan tindakan berikutnya |
| Slot utama | kosong | Aksi opsional |

Tidak ada `variant`/`state`; ikon dan teks ditampilkan sebagai status umum. Komponen merender slot hanya jika tidak kosong. Atribut `class` tambahan tidak diproses pada root; jika butuh jarak khusus, bungkus komponen dalam elemen lain.

## 📦 Belum ada produk

```blade
<x-ui.empty icon="package" title="Belum ada produk"
    description="Tambahkan produk pertama agar muncul di katalog.">
    <a href="{{ route('products.create') }}" class="btn btn-primary">Tambah produk</a>
</x-ui.empty>
```

Route `products.create` adalah ilustrasi route milik aplikasi Anda.

## 🗂️ Tanpa aksi dan keadaan kondisional

```blade
@forelse ($messages as $message)
    <p>{{ $message->subject }}</p>
@empty
    <x-ui.empty icon="mail" title="Kotak masuk kosong"
        description="Pesan baru akan muncul di halaman ini." />
@endforelse
```

Untuk proses sedang memuat, kegagalan request, atau hasil pencarian kosong, pilih [table state](/komponen/table-state) yang memang menyediakan state `loading`, `error`, dan `filtered`.
