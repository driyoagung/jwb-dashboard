# 📝 Form & input

Gunakan komponen form untuk presentasi dan hubungan dengan error Laravel; proses penyimpanan tetap dilakukan oleh route/controller. `x-ui.field` menampilkan label, bantuan, dan error dari `$errors` berdasarkan `name`. Pastikan `name` pada field dan kontrol sama.

## 🧷 Field dan validasi bersama {#field-dan-validasi-bersama}

`x-ui.field` menerima `label`, `name`, `help`, `required`, `class`, serta atribut `id`. `name` digunakan untuk mencari pesan error pada `$errors`; jika error ada, pesan bantuan `help` digantikan oleh pesan error pertama. Prop `required` menambahkan tanda bintang pada label **saja**: tetap setel atribut `required` pada kontrol dan validasi `required` di controller. ID otomatis berbentuk `f-{name}` pada field dan input yang cocok.

```blade
<x-ui.field label="Email" name="email" help="Gunakan alamat email kerja." :required="true">
    <x-ui.input name="email" type="email" autocomplete="email" required />
</x-ui.field>
```

Jika Anda menambahkan `id` khusus, berikan nilai yang sama pada `x-ui.field` dan kontrolnya. `class` untuk pembungkus field diterima sebagai prop maupun atribut HTML.

## ⌨️ Input, select, textarea

```blade
<form method="POST" action="{{ route('examples.records.store') }}">
    @csrf
    <x-ui.field name="title" label="Judul" :required="true" help="Judul singkat entri.">
        <x-ui.input name="title" :value="$record?->title" required />
    </x-ui.field>

    <x-ui.field name="category" label="Kategori">
        <x-ui.select name="category" placeholder="Pilih kategori"
            :value="$record?->category"
            :options="['panduan' => 'Panduan', 'laporan' => 'Laporan']" />
    </x-ui.field>

    <x-ui.field name="summary" label="Ringkasan">
        <x-ui.textarea name="summary" :value="$record?->summary" rows="4" />
    </x-ui.field>

    <x-ui.button type="submit">Simpan</x-ui.button>
</form>
```

### 📥 `x-ui.input` {#x-ui-input}

Prop: `name`, `value`, `type` (default `text`), `invalid`, `addon`, `icon`. `icon="search"` menaruh ikon di sisi kiri dan menambah jarak teks; `addon="Rp"` menaruh label di awal grup input. Atribut seperti `placeholder`, `min`, `max`, `autocomplete`, `readonly` dan `required` diteruskan ke `<input>`. Untuk input `type="file"`, komponen tidak mengisi `value` meskipun ada nilai lama.

```blade
<x-ui.input name="price" type="number" addon="Rp" min="0" step="1" :value="$product?->price" />
```

### 🔽 `x-ui.select` {#x-ui-select}

Prop: `name`, `value`, `options` berupa array `value => label`, `placeholder`, `includeBlank`, `invalid`, serta `searchable` (default `false`). `placeholder` atau `includeBlank` menyisipkan `<option value="">`; bila keduanya tidak ada, pilihan pertama dapat terkirim walaupun pengguna belum memilih secara sadar. Slot bisa berisi `<option>` tambahan. Aktifkan `:searchable="true"` untuk Choices.js; [referensi select](/komponen/select) menjelaskan detailnya.

```blade
<x-ui.select name="status" :value="$record?->status" placeholder="Pilih status"
    :options="['draf' => 'Draf', 'aktif' => 'Aktif']" required />
```

### 📄 `x-ui.textarea` {#x-ui-textarea}

Prop: `name`, `value`, `rows` (default 4), `invalid`. Isi `<textarea>` berasal dari `old(name, value)`; **bukan** dari slot. Atribut `maxlength`, `placeholder`, `required` dan lainnya diteruskan ke elemen HTML.

```blade
<x-ui.textarea name="notes" :value="$record?->notes" rows="6" maxlength="1000"
    placeholder="Catatan untuk tim…" />
```

Ketiganya memprioritaskan `old(name, value)` setelah redirect validasi. Error akan menambah `aria-invalid`/kelas invalid; `x-ui.field` menampilkan pesan pertama. Prop `invalid` dapat dipakai untuk memaksa status invalid ketika validasi berasal dari sumber di luar `$errors`.

Contoh ini memakai nama route dari [panduan CRUD](/integrasi/crud) yang perlu Anda tambahkan; `$record` dapat bernilai `null` pada halaman create.

::: tip ID untuk label
Tanpa `id` eksplisit, field dan input dengan `name="title"` sama-sama memakai `f-title`. Untuk nama array (`items[0][title]`), berikan `id` identik secara eksplisit pada kedua komponen agar label menunjuk kontrol yang benar dan ID tetap unik.
:::

## 🔀 Switch {#switch}

`x-ui.switch` membutuhkan `name`; opsional `label`, `hint`, `checked` (default false), `value` (default `1`). Komponen menghasilkan input tersembunyi bernilai `0` dan checkbox bernilai `1` saat aktif, sehingga kontrol yang dimatikan tetap memiliki nilai dalam request.

```blade
<x-ui.switch name="published" label="Terbitkan" :checked="(bool) ($record->published ?? false)" />
```

Di server gunakan `$request->boolean('published')`; jangan menganggap prop `checked` melakukan validasi atau penyimpanan.

`old(name, checked)` memulihkan pilihan setelah redirect. Input tersembunyi dan checkbox menggunakan `name` yang sama: ini disengaja agar form HTML selalu mengirim `0` atau `1`. Jika Anda perlu nilai lain lewat prop `value`, sesuaikan juga konversi nilainya di server.

## 🔎 Combobox {#combobox}

`x-ui.combobox` membutuhkan `id` dan `label`; `name`, `placeholder`, `options` opsional. `options` berupa string atau `['value' => '...', 'label' => '...']`. Input teks untuk pencarian pilihan; **nilai form berada pada hidden input**, yang diisi setelah opsi dipilih. Saat pertama tampil komponen belum mengisi pilihan dari `old()`/nilai edit; tambahkan dukungan itu jika dipakai pada halaman edit.

```blade
<x-ui.combobox id="category-search" label="Kategori" name="category_id"
    :options="[['value' => '1', 'label' => 'Panduan'], ['value' => '2', 'label' => 'Laporan']]" />
```

Daftar opsi difilter di browser saat mengetik. Pengguna dapat memilih memakai klik atau panah dan Enter; setelah dipilih, label tampil pada input teks dan `value` dipindahkan ke input tersembunyi. Mengubah teks setelah pemilihan akan mengosongkan nilai tersembunyi. Untuk halaman edit, komponen saat ini belum menyediakan `value` terpilih awal atau pemulihan `old()`; perlu diperluas sebelum dipakai untuk edit data nyata. Opsi yang ditampilkan sudah dirender saat server mengirim HTML; pencarian ini tidak meminta data jarak jauh.

## 📅 Rentang tanggal {#rentang-tanggal}

`x-ui.date-range` memakai Flatpickr untuk dua tanggal dan tombol preset 7/30 hari. Gunakan `from-name="from"` dan `to-name="to"` agar nilai ISO `Y-m-d` terkirim ke Laravel; tanpa nama input hanya untuk demo. Validasi urutan di server tetap diperlukan. Lihat [referensi date range](/komponen/date-range).

```blade
<x-ui.date-range id="periode-laporan" label="Periode laporan" />
```

## 📎 Pratinjau berkas {#pratinjau-berkas}

`x-ui.file-preview` membutuhkan `id`; prop `label`, `accept`, `maxMb` (default 5). Elemen menyediakan area pilih/seret, nama dan ukuran berkas, thumbnail gambar atau ikon PDF, tombol hapus, dan pesan error. Pratinjau gambar/PDF dan pemeriksaan jenis/ukuran dilakukan oleh JS. Input bawaan **belum memiliki `name`**; tambah `name="attachment"` pada input di komponen sebelum menggunakan unggahan form, lalu tambahkan `enctype="multipart/form-data"`, validasi Laravel, dan penyimpanan server. JS saat ini menerima PNG/JPG/PDF meskipun prop `accept` diubah; selaraskan aturan klien dan server ketika menambah jenis berkas. `maxMb` merupakan validasi klien, bukan pembatas ukuran upload PHP atau Laravel.

```blade
<x-ui.file-preview id="berkas-demo" label="Pilih berkas" :max-mb="5" />
```

Contoh interaktif ada di `/components/filters`; panduan backend di [form & unggah](/integrasi/form).
