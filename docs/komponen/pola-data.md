# 🧰 Pola data baru

Lima komponen baru untuk alur aplikasi admin: [filter bar](/komponen/filter-bar), [detail list](/komponen/detail-list), [timeline](/komponen/timeline), [tabs](/komponen/tabs), dan [confirm action](/komponen/confirm-action). Semua adalah anonymous Blade components (`resources/views/components/ui/`).

## 🖥️ Lihat contoh gabungan

Dengan `ADMIN_SHOWCASE=true`, buka `/components/data-patterns`. Halaman ini menampilkan kelima komponen dan select yang dapat dicari; route pratinjau konfirmasi hanya melakukan redirect dengan flash message dan tidak menghapus data.

## 🔌 Jalur penggunaan nyata

- Di halaman index, gunakan filter bar `method="GET"` agar URL bisa dibagikan dan parameter tersedia lewat `request()`.
- Di halaman show, isi detail list/timeline dengan data controller yang telah diformat untuk ditampilkan.
- Gunakan tabs untuk konten server-rendered, bukan untuk mengasumsikan lazy loading.
- Taruh confirm action dekat entri terkait, lalu arahkan ke route yang memvalidasi izin dan mengeksekusi tindakan.
- Pilih [select searchable](/komponen/select#select-yang-dapat-dicari-choices-js) dan [date range Flatpickr](/komponen/date-range) hanya saat membantu pencarian/penjadwalan.
