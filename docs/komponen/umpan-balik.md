# 💬 Umpan balik & dialog

## 📢 Alert {#alert}

`x-ui.alert` menerima `variant` (`info`, `success`, `warning`, `danger`), `title` opsional, `solid` boolean, slot isi dan slot `action` opsional. Untuk pesan error `danger`, komponen memberi `role="alert"`.

`title` menghasilkan teks tebal di atas isi. `action` dapat dipakai untuk menempatkan tombol/tautan di sisi kanan pesan, contohnya tindakan memuat ulang:

```blade
<x-ui.alert variant="warning" title="Sinkronisasi tertunda">
    Periksa kembali koneksi sebelum melanjutkan.
    <x-slot:action>
        <a href="{{ request()->fullUrl() }}" class="btn btn-outline btn-sm">Muat ulang</a>
    </x-slot:action>
</x-ui.alert>
```

`solid` memberi gaya yang lebih menonjol pada background; gunakan `:solid="true"` untuk prop boolean. Pesan informatif biasa tidak otomatis memakai `role="alert"` sehingga tidak menginterupsi pembaca layar.

```blade
<x-ui.alert variant="success" title="Berhasil disimpan">
    Perubahan tersedia pada halaman detail.
</x-ui.alert>
```

Untuk flash message dari redirect Laravel, tampilkan secara server-rendered:

```blade
@if (session('status'))
    <x-ui.alert variant="success" title="Berhasil">{{ session('status') }}</x-ui.alert>
@endif
```

## 🔔 Toast

Toast dibuat oleh `window.App.toast({ title, description, tone, duration })`, dan ditampilkan dalam `#toasts` milik layout. Pilihan tone: `success`, `danger`, `warning`, `info`, `neutral`. Bisa juga dipicu dengan atribut pada elemen yang diklik:

```blade
<button type="button" data-toast="info" data-toast-title="Pratinjau" data-toast-desc="Hanya tampilan demo">
    Lihat pesan
</button>
```

`data-toast` menampilkan pesan saat klik; ini **bukan** konfirmasi penyimpanan server. Untuk tindakan nyata, tunggu hasil controller/redirect sebelum menampilkan sukses. Ketika membangun pesan toast dari data pengguna, hindari menyisipkan input tak tepercaya secara langsung: implementasi toast saat ini membentuk markup dengan `innerHTML`.

`duration` memakai milidetik; jika tidak diisi, toast menutup sekitar 4,5 detik setelah ditampilkan. `window.App.toast` membutuhkan kontainer `#toasts` yang sudah tersedia pada layout aktif. Jangan menjadikannya satu-satunya tempat menampilkan error form—pesan per field melalui `x-ui.field` tetap dibutuhkan.

## 🪟 Modal & drawer

Keduanya menghasilkan elemen HTML `<dialog>`. `x-ui.modal`: `id`, `title` wajib; `description`, `icon`, `tone`, `size` (`sm`, `lg`, `xl`) opsional; slot `footer` untuk aksi. `x-ui.drawer`: `id`, `title` wajib; `description` opsional; slot `footer` juga tersedia. Trigger memakai `data-modal-open="#id"`, penutup memakai `data-modal-close` di dalam dialog.

```blade
<button type="button" class="btn btn-outline" data-modal-open="#detail-info">Informasi</button>

<x-ui.modal id="detail-info" title="Informasi entri" description="Tinjau sebelum melanjutkan." size="sm">
    <p>Konten dialog.</p>
    <x-slot:footer>
        <button type="button" class="btn btn-primary" data-modal-close>Tutup</button>
    </x-slot:footer>
</x-ui.modal>
```

Untuk hapus data, letakkan form `method="POST"` dengan `@csrf` dan `@method('DELETE')` pada footer; tombol batal boleh menggunakan `data-modal-close`, **tombol submit jangan langsung memakai `data-modal-close` dan toast sukses**. Demo di `/components/feedback` hanya memperlihatkan interaksi antarmuka.

### 🧾 `x-ui.modal` {#x-ui-modal}

Modal menerima `size="sm"` (`max-w-md`), default `lg` (`max-w-lg`), dan `xl` (`max-w-2xl`). `icon` opsional berada di samping judul; `tone` menentukan kelas avatar ikon (default `info`). `id` harus unik karena dipakai untuk label judul, deskripsi, dan pencarian oleh `data-modal-open`. Slot utama adalah isi, sementara `footer` menempatkan aksi di bagian bawah. Penutupan lewat Esc, klik backdrop, atau elemen `data-modal-close` diatur oleh `interactions.js`.

### 🗄️ `x-ui.drawer` {#x-ui-drawer}

Drawer menerima `id`, `title`, `description` dan slot `footer`. Konten drawer dibuat scrollable, sehingga cocok untuk filter panjang tanpa mendorong tombol aksi keluar layar. Pemicu dan mekanisme penutup sama dengan modal; tombol X bawaan sudah memiliki `data-modal-close`. Drawer hanya kerangka visual—filter dari dalamnya tetap perlu form GET atau logika request sendiri.

```blade
<button type="button" class="btn btn-outline" data-modal-open="#drawer-status">Filter</button>
<x-ui.drawer id="drawer-status" title="Filter entri">
    <p>Pilih kriteria yang ingin diterapkan.</p>
    <x-slot:footer>
        <button type="button" class="btn btn-outline" data-modal-close>Batal</button>
    </x-slot:footer>
</x-ui.drawer>
```

## ⏳ Progres & skeleton

Progres `.progress`/`.progress-bar`, spinner `.spinner`, dan skeleton `.skeleton` adalah kelas CSS, bukan komponen Blade tersendiri. Contoh markup dan state tersedia di `resources/views/showcase/feedback.blade.php`.
