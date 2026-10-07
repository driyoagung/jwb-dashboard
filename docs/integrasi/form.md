# 📝 Form, validasi & unggah

## 🔄 Ubah form pratinjau menjadi form Laravel

`resources/views/components/examples/record-form.blade.php` saat ini menghasilkan `<form data-demo-form>` tanpa `method`/`action`. `advanced-inputs.js` mencegat submit dan menampilkan toast. Untuk menyimpan entri, ganti tag form di komponen tersebut:

```blade
@props(['mode' => 'create', 'record' => null])

@php($editing = $mode === 'edit')

<form method="POST"
    action="{{ $editing ? route('examples.records.update', $record) : route('examples.records.store') }}"
    class="space-y-6">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <x-ui.field name="title" label="Judul entri" :required="true">
        <x-ui.input name="title" :value="$record?->title" required />
    </x-ui.field>
    <x-ui.field name="category" label="Kategori" :required="true">
        <x-ui.select name="category" :value="$record?->category"
            :options="['panduan' => 'Panduan', 'laporan' => 'Laporan', 'catatan' => 'Catatan', 'arsip' => 'Arsip']"
            placeholder="Pilih kategori" required />
    </x-ui.field>
    <x-ui.field name="status" label="Status" :required="true">
        <x-ui.select name="status" :value="$record?->status ?? 'draf'"
            :options="['draf' => 'Draf', 'ditinjau' => 'Ditinjau', 'aktif' => 'Aktif', 'arsip' => 'Arsip']"
            required />
    </x-ui.field>
    <x-ui.field name="summary" label="Ringkasan">
        <x-ui.textarea name="summary" :value="$record?->summary" />
    </x-ui.field>
    <x-ui.button type="submit">{{ $editing ? 'Simpan perubahan' : 'Buat entri' }}</x-ui.button>
</form>
```

Ini adalah contoh **pengganti** markup form demo, bukan komponen tambahan yang sudah ada. Gabungkan field dan struktur kartu dari komponen lama bila ingin mempertahankan tata letaknya. Di halaman edit panggil `<x-examples.record-form mode="edit" :record="$record" />`; di create panggil `<x-examples.record-form />`.

`x-ui.input`, `x-ui.select`, dan `x-ui.textarea` memakai `old($name, $value)` sehingga nilai yang gagal validasi tetap tampil; `x-ui.field` menampilkan error pertama dari `$errors`. Hindari `data-demo-form` agar submit tidak diblokir JavaScript. Berikan `action` dan `method` pada setiap form nyata, lalu tangani otorisasi dan validasi di sisi server.

## ✅ Flash message sesudah redirect

Di view tujuan, misalnya `examples/records/show.blade.php`:

```blade
@if (session('status'))
    <x-ui.alert variant="success" title="Berhasil">
        {{ session('status') }}
    </x-ui.alert>
@endif
```

## 📎 Unggah berkas

`x-ui.file-preview` bawaan hanya pratinjau dan validasi di browser. Edit `resources/views/components/ui/file-preview.blade.php` untuk menerima prop `name` dan pasang atribut `name` berisi prop tersebut pada `<input type="file">`. Contoh penggunaan setelah modifikasi:

```blade
<form method="POST" action="{{ route('attachments.store') }}" enctype="multipart/form-data">
    @csrf
    <x-ui.file-preview id="attachment" name="attachment" label="Lampiran" />
    <x-ui.button type="submit">Unggah</x-ui.button>
</form>
```

Di controller validasi dan simpan file di server, misalnya:

```php
$data = $request->validate([
    'attachment' => ['required', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:5120'],
]);

$path = $data['attachment']->store('attachments', 'public');
```

Pastikan disk publik telah disiapkan (`php artisan storage:link` jika file perlu diakses dari web). Simpan `$path` dalam tabel aplikasi bila harus dirujuk lagi. Prop `accept` pada input hanya membantu pemilihan file; JS bawaan tetap membatasi jenis PNG/JPG/PDF dan maksimal `maxMb`. Aturan backend tetap otoritatif.
