# ⚡ Interaksi JavaScript

`resources/js/app.js` mengimpor `bootstrap.js`, `admin/charts.js`, `admin/interactions.js`, `admin/advanced-inputs.js`, dan `admin/enhanced-controls.js`. Tidak memerlukan framework frontend. Sebagian besar klik ditangani melalui event delegation pada dokumen; inisialisasi tabel dan grafik berjalan saat halaman dimuat.

| Perilaku | Penanda pada markup | Lokasi |
| --- | --- | --- |
| Modal/drawer | `data-modal-open="#id"`, `data-modal-close` | `interactions.js` |
| Dropdown | `data-dropdown`, `data-dropdown-toggle`, `.menu` | `interactions.js` |
| Tab | `role="tablist"`, `role="tab"`, `aria-controls` | `interactions.js` |
| Sidebar/submenu | `#sidebarToggle`, `data-collapse="#id"` | `interactions.js` |
| Toast, dismiss, copy | `data-toast`, `data-dismissible`/`data-dismiss`, `data-copy` | `interactions.js` |
| Tabel | `data-table` dan atribut tabel lainnya | `interactions.js` |
| Grafik | `data-chart` dan atribut data JSON | `charts.js` |
| Combobox, rentang tanggal, preview berkas | `data-combobox`, `data-date-range`, `data-file-preview` | `advanced-inputs.js` |
| Select searchable, kalender | `data-enhanced-select`, `data-date-picker` | `enhanced-controls.js` (Choices.js/Flatpickr) |
| Form demo | `data-demo-form` | `advanced-inputs.js` |

## 🧪 Halaman demo vs form asli

`data-demo-form` mencegat submit (`preventDefault`) dan hanya menampilkan toast. Saat menghubungkan form ke Laravel, hapus atribut tersebut, tentukan `method` dan `action`, serta sertakan `@csrf`/`@method` sesuai kebutuhan.

`data-load` pada tombol hanya membuat simulasi loading selama sekitar 1,6 detik; ini tidak menunggu respons server. Tombol yang benar-benar mengirim form sebaiknya mengelola loading sesuai hasil request. Filter chip dan tombol pilihan state tabel juga hanya mengubah tampilan browser.

## 🔄 Konten yang ditambahkan secara dinamis

Setelah memasukkan elemen tabel atau grafik baru ke DOM, panggil `window.App.initPage(container)`; fungsi ini menginisialisasi tabel/grafik serta menyinkronkan preferensi. `advanced-inputs.js` melakukan query saat skrip pertama dimuat, sehingga combobox/date range/file preview yang disisipkan belakangan memerlukan inisialisasi tambahan dalam kode Anda.

Untuk select searchable dan kalender yang disisipkan belakangan, panggil `window.EnhancedControls.init(container)`; elemen yang sudah diinisialisasi tidak dipasang ulang.

Untuk Blade multi-halaman biasa, browser memuat ulang halaman sehingga inisialisasi awal berjalan otomatis. Kode router pratinjau satu-file (`window.__SPA__`) dalam `interactions.js` berasal dari template HTML dan tidak digunakan oleh route Blade Laravel.
