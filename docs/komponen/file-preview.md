# 📎 File preview

<ComponentPreview name="file-preview" :height="310" />

`<x-ui.file-preview>` menampilkan area pilih/seret berkas, thumbnail gambar, metadata file, tombol hapus dan pesan error lokal. Sumber: `resources/views/components/ui/file-preview.blade.php`; logika: `resources/js/admin/advanced-inputs.js`.

## 🧩 Prop

| Prop | Default | Fungsi |
| --- | --- | --- |
| `id` | wajib | ID input file/label |
| `name` | `null` | Nama field untuk mengirim berkas melalui form Laravel |
| `label` | `Pilih berkas` | Teks utama dropzone |
| `accept` | `image/png,image/jpeg,application/pdf` | Petunjuk filter dialog file browser |
| `formats` | `PNG, JPG, PDF` | Format yang ditampilkan pada petunjuk unggahan |
| `maxMb` | `5` | Batas ukuran lokal dalam MB |

Atribut `class` dan lainnya diterapkan pada pembungkus, **bukan input file**. Tentukan `name` agar berkas terkirim ke Laravel.

## 🖼️ Varian ukuran dan pratinjau

```blade
<x-ui.file-preview id="lampiran-demo" label="Pilih gambar atau PDF" />
<x-ui.file-preview id="lampiran-besar" label="Pilih lampiran" :max-mb="10" class="mt-6" />
```

JS sekarang menerima hanya MIME PNG, JPG, dan PDF, terlepas dari prop `accept`. Bila mengubah `accept` atau batas ukuran, selaraskan validasi di `advanced-inputs.js` dan Laravel. Gambar dipratinjau melalui object URL lokal; PDF ditampilkan sebagai ikon dan nama, bukan isi PDF. Tombol hapus mengosongkan input dan mencabut object URL.

## 📤 Mengaktifkan upload Laravel

Komponen menerima prop `name` untuk mengirim berkas:

```blade
<form method="POST" action="{{ route('attachments.store') }}" enctype="multipart/form-data">
    @csrf
    <x-ui.file-preview id="lampiran" name="attachment" label="Lampiran entri" />
    <x-ui.button type="submit">Unggah</x-ui.button>
</form>
```

Route `attachments.store` merupakan contoh route yang Anda buat. Di controller lakukan `$request->validate(['attachment' => ['required', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:5120']])`, lalu simpan pada disk aplikasi. `accept` dan `maxMb` di browser **bukan** validasi keamanan server. Rincian lengkap di [panduan unggah](/integrasi/form#unggah-berkas).
