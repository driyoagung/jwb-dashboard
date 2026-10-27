# PRODUCT REQUIREMENTS DOCUMENT (PRD)
# Website Point of Sale (POS) — Laravel Blade Starter Kit

**Versi:** 1.0  
**Tanggal:** 17 Oktober 2026  
**Status:** Siap untuk discovery codebase dan implementasi bertahap  
**Target:** POS web sederhana untuk toko/warung, single-store, online-first

## 1. Ringkasan Produk
Bangun aplikasi Point of Sale (POS) untuk operasional toko harian: pengelolaan produk, kategori, satuan, stok, transaksi kasir, draft transaksi, riwayat transaksi, laporan, pengguna, dan pengaturan toko. Aplikasi wajib dibangun **di atas codebase Laravel starter kit dashboard yang sudah tersedia**, bukan membuat dashboard baru.

**Prinsip utama: CODEBASE-FIRST, REUSE-FIRST, BACKEND-FIRST.** Prioritaskan logika bisnis dan integrasi database. UI harus menggunakan layout, Blade components, utilities Tailwind, JavaScript library, serta mekanisme toast/modal/alert yang sudah tersedia. Jangan membuat komponen visual sendiri bila padanannya ada.

### 1.1 Referensi layar
Screenshot yang diberikan memperlihatkan: dashboard dengan KPI omzet hari ini, transaksi hari ini, draft aktif, stok menipis, grafik penjualan tujuh hari, tabel produk terlaris; menu kasir, riwayat transaksi, produk, kategori, satuan, stok, riwayat stok, laporan penjualan/produk terlaris/stok/draft, pengguna; dan pengaturan toko meliputi nama, telepon, alamat, footer struk, pajak, kedaluwarsa draft, batas draft aktif. Screenshot ketiga menjadi referensi kualitas pemanfaatan dashboard components, bukan instruksi untuk menyalin desain.

## 2. Tujuan, Pengguna, dan Batasan
**Tujuan:** kasir dapat menyelesaikan penjualan cepat dan akurat; admin mengelola katalog, stok, pengguna, dan laporan tanpa spreadsheet terpisah.

**Persona:**
- **Admin/Owner:** seluruh modul, pengaturan, laporan, manajemen pengguna, koreksi stok.
- **Kasir:** membuat/menyelesaikan transaksi, menyimpan/melanjutkan draft sendiri, melihat riwayat transaksi sesuai kebijakan akses; tidak mengubah master data atau konfigurasi.

**Cakupan MVP:** satu toko, satu gudang/stok global, satu mata uang IDR, pembayaran tunai dan non-tunai yang dicatat manual, tanpa integrasi payment gateway, tanpa akuntansi penuh, tanpa pembelian supplier, tanpa multi-cabang, tanpa sinkronisasi offline.

**Non-goals MVP:** loyalty, multi-outlet, barcode printer khusus, integrasi marketplace, retur otomatis, diskon bertingkat, pajak per item, harga grosir, hutang/piutang, dan inventory batch/expired date. Ekstensi disiapkan tanpa membangun fitur tersebut sekarang.

## 3. Aturan Wajib untuk AI Coding Agent
1. **Lakukan audit repository sebelum menulis kode.** Identifikasi versi Laravel/PHP, struktur folder, routes, auth, middleware, model, migrations, Blade layouts, komponen, assets, Vite, package manager, test framework, database, dan konvensi kode.
2. **Inventarisasi seluruh Blade components** dengan lokasi file, props, slots, variasi, serta contoh penggunaan; cek halaman demo/storybook internal bila ada. Cari card, stat, badge, button, input, select, textarea, checkbox, radio, alert, toast, modal, table, dropdown, pagination, tabs, empty state, loading, breadcrumb, tooltip, avatar, datepicker, dan komponen lain yang nyata tersedia.
3. **Audit library JS terpasang** (Choices.js, Chart.js, datepicker, formatter, modal/toast handler, AJAX helper, dan lain-lain). Gunakan yang tersedia dan cocok; jangan mengasumsikan API-nya. Jangan menambahkan dependency baru tanpa justifikasi.
4. **Gunakan layout dan navigasi existing**. Pertahankan warna, spacing, typography, responsivitas, dark mode bila tersedia, aksesibilitas, dan konvensi utility Tailwind.
5. **Larangan membuat komponen/styling duplikat** bila komponen existing mampu memenuhi kebutuhan. Jika tidak ada, dokumentasikan gap, pertimbangkan komposisi komponen existing terlebih dahulu, lalu buat komponen reusable minimal dengan persetujuan sesuai pola repo.
6. **Jangan menulis ulang starter kit**, menghapus fitur bawaan, mengganti sistem auth, atau mengubah dependency besar tanpa alasan dan persetujuan.
7. **Tidak memaksakan semua komponen tampil**: manfaatkan seluas mungkin sesuai fungsi dan konteks; hindari UI penuh ornamen yang tidak relevan.
8. **Backend-first**: validation, authorization, integrity, transaksi database, services, dan tests harus benar sebelum polesan UI.
9. **Tidak membuat asumsi tentang isi repo.** Bila ada perbedaan dari dokumen ini, laporkan temuan dan usulkan adaptasi sebelum implementasi.
10. Ikuti konvensi kode, penamaan, dan versi framework yang benar-benar digunakan di repository.

### 3.1 Deliverable tahap discovery (wajib sebelum coding)
Buat `docs/pos/codebase-audit.md` berisi versi stack, struktur folder, pola auth/RBAC, daftar komponen Blade dan contoh syntax yang valid, library JS yang terdeteksi, contoh halaman existing yang akan direuse, tabel pemetaan modul POS → komponen existing, daftar gap, risiko, dan rencana implementasi. Jangan mengarang nama tag seperti `<x-ui.card>` tanpa verifikasi.

## 4. Tech Stack
| Lapisan | Teknologi |
|---|---|
| Backend | Laravel sesuai versi starter kit |
| Server rendering | Blade templating engine |
| Styling | Tailwind CSS dan design system existing |
| Database | MySQL, InnoDB, foreign keys |
| Interaktivitas | JavaScript existing / vanilla JS secukupnya |
| Pilihan select | Choices.js jika telah tersedia dan sesuai |
| Grafik | Chart.js jika telah tersedia |
| Auth | Reuse autentikasi starter kit |
| Test | PHPUnit/Pest sesuai project |
| Build | Vite/build pipeline existing |

Tidak menggunakan SPA framework baru. Seluruh mutasi data harus divalidasi di server, bukan hanya di JavaScript.

## 5. Navigasi dan Modul
| Grup | Menu | Akses |
|---|---|---|
| Main | Dashboard | Admin, Kasir (data dibatasi sesuai role) |
| Kasir | Kasir | Admin, Kasir |
| Kasir | Riwayat Transaksi | Admin, Kasir sesuai scope |
| Master Data | Produk | Admin |
| Master Data | Kategori | Admin |
| Master Data | Satuan | Admin |
| Inventori | Stok | Admin |
| Inventori | Riwayat Stok | Admin |
| Laporan | Penjualan | Admin |
| Laporan | Produk Terlaris | Admin |
| Laporan | Stok | Admin |
| Laporan | Draft Transaksi | Admin |
| Pengaturan | Pengguna | Admin |
| Pengaturan | Toko | Admin |

Menu dan route harus dilindungi oleh middleware/policy, bukan hanya disembunyikan di sidebar.

## 6. Kebutuhan Fungsional Terperinci

### 6.1 Dashboard
**KPI:** omzet hari ini (total transaksi completed, net setelah diskon dan pajak), jumlah transaksi selesai hari ini, draft aktif, jumlah produk stok menipis. **Grafik:** penjualan 7 hari terakhir berdasarkan tanggal lokal toko, isi nol untuk tanggal tanpa transaksi. **Tabel:** produk terlaris bulan ini (qty terjual, omzet), stok menipis (nama, sisa stok, link detail). Semua data berasal dari DB, bukan angka hardcoded. Terapkan index/query agregat yang efisien; hindari N+1. Gunakan card/stat, badge, table, Chart.js, empty states existing.

### 6.2 Master Produk
CRUD produk dengan SKU unik, barcode opsional unik, nama, kategori, satuan, harga jual (integer rupiah), harga modal opsional (integer rupiah), stok awal via movement, stok minimum, status aktif, deskripsi opsional, gambar opsional bila infrastruktur upload existing mendukung. Daftar: pencarian nama/SKU/barcode, filter kategori/status/stok menipis, sort, pagination. Nonaktifkan produk yang tidak dijual; produk yang sudah digunakan transaksi tidak boleh dihapus permanen. Produk nonaktif tidak dapat ditambahkan ke transaksi baru. Perubahan harga tidak mengubah transaksi historis.

### 6.3 Kategori dan Satuan
CRUD kategori (nama unik dalam scope toko, status) dan satuan (nama, singkatan, status). Larang hapus jika direferensikan produk; gunakan nonaktifkan atau soft delete sesuai pola existing. Validasi unik dan pesan error jelas.

### 6.4 Inventori dan Riwayat Stok
Stok tersimpan per produk dalam satuan dasar (MVP kuantitas integer; tidak mendukung konversi satuan). Tampilkan stok saat ini, minimum, status aman/menipis/habis. Penyesuaian manual: stok masuk, stok keluar, koreksi, dengan alasan wajib dan aktor. Seluruh perubahan menghasilkan `stock_movements` immutable dengan qty sebelum/sesudah, delta bertanda, tipe, referensi transaksi bila relevan, timestamp. Stok tidak boleh negatif. Draft tidak mengurangi stok. Penjualan selesai mengurangi stok tepat satu kali. Penghapusan transaksi selesai tidak boleh menghilangkan jejak stok.

### 6.5 Kasir / POS Checkout
Halaman kasir responsif dengan pencarian cepat produk via nama/SKU/barcode (input barcode scanner keyboard-compatible), katalog/list produk, keranjang, perubahan qty, hapus item, subtotal, diskon transaksi (nominal; default 0), pajak persentase dari setting, grand total, pembayaran, dan kembalian. Tampilkan stok tersedia dan blok qty yang melebihi stok pada server.

Metode pembayaran MVP: `cash`, `transfer`, `qris`, `other` (semuanya **pencatatan manual**, tidak mengonfirmasi pembayaran pihak ketiga). Untuk tunai, `amount_received >= grand_total`, kembalian = selisih. Untuk non-tunai, nominal bayar harus sama dengan total, tanpa kembalian; bukti/verifikasi eksternal berada di luar MVP. Simpan snapshot nama produk, SKU, harga, qty, subtotal, diskon/pajak, total, kasir, metode, dan waktu pada transaksi.

**Atomic checkout:** `DB::transaction` + locking baris stok produk dengan urutan konsisten, validasi ulang status/harga/qty/stok di server, create transaksi/items/pembayaran, kurangi stok, buat stock movements, commit. Hindari double-submit dengan idempotency key unik. Jika gagal, rollback keseluruhan dan tampilkan error melalui toast existing. Jangan mempercayai harga/total dari browser.

### 6.6 Draft Transaksi
Kasir bisa menyimpan keranjang sebagai draft, melanjutkan, mengedit, atau membatalkan draft miliknya; admin dapat mengelola seluruh draft. Setting `draft_expiry_hours` default 24, `max_active_drafts` default 20 (tetapkan scope **per kasir**). Draft expired tidak dapat checkout sebelum dipulihkan dengan kebijakan eksplisit; default expired read-only lalu dapat dibatalkan. Pembersihan via scheduler opsional; seluruh query tetap memperlakukan draft lewat expiry sebagai expired meski scheduler belum berjalan. Saat melanjutkan draft, hitung ulang harga dan stok serta tampilkan perubahan kepada kasir sebelum checkout; draft tidak memesan stok.

### 6.7 Riwayat Transaksi dan Struk
List transaksi selesai: nomor transaksi unik, waktu, kasir, jumlah item, total, metode bayar, status. Search nomor transaksi, filter tanggal/kasir/metode, pagination. Detail menampilkan snapshot item dan ringkasan. Cetak struk browser menggunakan print stylesheet existing atau halaman Blade print minimal; memuat nama toko, alamat, telepon, nomor transaksi, tanggal, kasir, item, qty, harga, diskon, pajak, total, bayar, kembalian, footer struk. Tidak perlu printer SDK. Transaksi selesai bersifat immutable; void/refund merupakan fase lanjutan, bukan edit/hapus langsung.

### 6.8 Laporan
- **Penjualan:** rentang tanggal, omzet, jumlah transaksi, rata-rata transaksi, breakdown metode bayar, tabel transaksi, chart tren.
- **Produk terlaris:** rank berdasarkan qty, omzet per produk, filter tanggal, kategori.
- **Stok:** stok kini, stok minimum, nilai persediaan estimasi jika harga modal tersedia, produk menipis/habis.
- **Draft:** aktif, expired, dibatalkan, per kasir, tanggal, ringkasan nilai estimasi (bukan omzet).

Ekspor CSV sebagai prioritas MVP menggunakan streamed response, UTF-8 BOM bila diperlukan untuk Excel; lindungi formula injection pada cell ekspor. PDF/XLSX hanya bila package existing tersedia dan benar-benar diperlukan. Semua filter dan perhitungan laporan harus konsisten dengan dashboard, zona waktu toko, dan status transaksi.

### 6.9 Pengguna dan Role
Gunakan model/auth existing. Role `admin` dan `cashier` via mekanisme role/policy yang tersedia; bila tidak ada, usulkan implementasi minimal sebelum coding. Admin dapat membuat, mengubah role, mengaktifkan/nonaktifkan pengguna dan reset password sesuai pola keamanan project. Kasir tidak dapat mengakses laporan global, master data, atau setting melalui direct URL/API. Cegah admin menonaktifkan akun admin terakhir yang aktif.

### 6.10 Pengaturan Toko
Single row konfigurasi: nama toko, telepon, alamat, footer struk, pajak persentase default 0, kedaluwarsa draft (jam) default 24, maksimal draft aktif per kasir default 20, timezone default `Asia/Jakarta`. Validasi pajak 0–100 dan batas setting yang masuk akal; simpan melalui form Blade dan tampilkan toast existing. Snapshot pajak transaksi tidak boleh berubah saat setting berubah.

## 7. Model Data dan Relasi yang Disarankan
Nama tabel final menyesuaikan konvensi existing dan hasil audit.

| Tabel | Kolom inti |
|---|---|
| `categories` | id, name, is_active, timestamps |
| `units` | id, name, symbol, is_active, timestamps |
| `products` | id, category_id, unit_id, sku unique, barcode nullable unique, name, sell_price, cost_price nullable, stock_qty, min_stock, is_active, timestamps, deleted_at opsional |
| `transactions` | id, transaction_number unique, cashier_id, status (`completed`), subtotal, discount_amount, tax_rate, tax_amount, grand_total, payment_method, amount_received, change_amount, idempotency_key unique, completed_at, timestamps |
| `transaction_items` | id, transaction_id, product_id, product_name, product_sku, unit_name, unit_price, qty, line_total, timestamps |
| `stock_movements` | id, product_id, user_id, transaction_id nullable, type, delta_qty, before_qty, after_qty, reason, created_at |
| `transaction_drafts` | id, cashier_id, status, expires_at, payload_json (items + qty, tidak dipercaya saat checkout), timestamps |
| `store_settings` | id, store_name, phone, address, receipt_footer, tax_rate, draft_expiry_hours, max_active_drafts, timezone, timestamps |
| `users` | reuse existing + mapping role/status sesuai hasil audit |

**Aturan numerik:** rupiah disimpan sebagai integer BIGINT, tidak menggunakan float untuk uang; tarif pajak sebagai DECIMAL(5,2), kalkulasi dan rounding terdefinisi konsisten di backend. Qty MVP integer positif. Gunakan index untuk FK, timestamps, status, expiry, SKU/barcode, completed_at, cashier_id. Pastikan snapshot tidak bergantung pada keberadaan master produk di masa depan.

**Rumus:** subtotal = Σ(unit_price × qty); taxable_base = max(0, subtotal − discount_amount); tax_amount = round(taxable_base × tax_rate / 100) ke rupiah terdekat; grand_total = taxable_base + tax_amount. Diskon tidak boleh melebihi subtotal. Omzet = Σ grand_total transaksi completed. Total stok adalah nilai `products.stock_qty` yang dijaga konsisten dengan movement.

## 8. Arsitektur Backend
Rekomendasi pemisahan tanggung jawab, menyesuaikan codebase:
- `FormRequest`: validasi input dan authorization awal.
- `Policy` / middleware: role, kepemilikan draft, akses resource.
- `Controller`: HTTP request, response Blade/redirect/JSON kecil untuk pencarian produk.
- `Service/Action`: `CheckoutService`, `StockAdjustmentService`, `DraftService`, `SalesReportService`, `DashboardService` bila sesuai pola repo.
- `Model`: relasi, casts, scopes, query builder; hindari business logic kompleks di controller.
- `DB::transaction` dan `lockForUpdate` pada operasi stok/checkout.
- `Job/Scheduler` untuk housekeeping draft opsional, tidak boleh menjadi satu-satunya penjaga expiry.
- `Database Seeder/Factory`: akun demo sesuai pola repo, kategori, satuan, produk dan data uji yang tidak berjalan di production tanpa persetujuan.

Gunakan route names yang konsisten, pagination server-side, eager loading, eager aggregation, CSRF, escaped output Blade, dan rate limiting untuk endpoint pencarian bila perlu.

## 9. Matriks Penggunaan UI Components
**Hanya contoh fungsi, bukan klaim nama komponen yang ada.** AI harus mengisi nama tag Blade yang ditemukan di audit.

| Komponen existing | Penerapan yang sesuai |
|---|---|
| Layout/sidebar/breadcrumb | Seluruh halaman dan navigasi |
| Stat card/card | KPI dashboard, ringkasan kasir, form setting, ringkasan laporan |
| Badge/status | Stok menipis, aktif/nonaktif, status draft/transaksi |
| Table/pagination | Produk, transaksi, laporan, movement |
| Input/select/Choices.js | Search produk, filter, kategori, satuan, kasir |
| Modal | Konfirmasi penyesuaian stok, batal draft, konfirmasi checkout |
| Toast | Sukses/gagal CRUD, checkout, simpan draft |
| Alert | Stok tidak cukup, perubahan harga, draft expired |
| Chart.js | Grafik 7 hari, tren laporan, ringkasan penjualan |
| Button/dropdown | Aksi baris, filter, cetak, ekspor |
| Tabs | Detail transaksi / ringkasan bila membantu |
| Empty state/skeleton | Daftar kosong dan proses loading bila tersedia |
| Tooltip/tooltip help | Penjelasan metrik atau field kompleks bila tersedia |

**Dilarang:** menyalin CSS dari screenshot, membuat sistem toast/modal kedua, menambahkan Tailwind config baru tanpa kebutuhan, dan mengganti struktur HTML komponen existing hanya untuk mengikuti screenshot. Gunakan form dan validasi existing dengan error states.

## 10. User Flow Utama
### 10.1 Penjualan
Login → buka Kasir → cari/scan produk → tambah ke keranjang → ubah qty → pilih metode bayar → validasi pembayaran → konfirmasi → checkout atomic → tampilkan transaksi berhasil → cetak struk opsional → keranjang reset.

### 10.2 Draft
Isi keranjang → simpan draft → daftar draft aktif → lanjutkan draft → refresh stok/harga → kasir konfirmasi perubahan → bayar → checkout → draft ditandai selesai dalam transaksi yang sama.

### 10.3 Stok
Admin buka Stok → cari produk → pilih penyesuaian → isi qty + alasan → konfirmasi modal → transaksi database → stok berubah → movement tercatat → toast sukses.

### 10.4 Laporan
Admin buka laporan → pilih rentang tanggal/filter → tampilkan agregat + chart + tabel → ekspor CSV yang konsisten dengan filter.

## 11. Acceptance Criteria per Modul
- **Dashboard:** seluruh angka berasal dari transaksi nyata; hari tanpa penjualan tampil nol; stok menipis berdasarkan `stock_qty <= min_stock` (min_stock > 0); data scoped dengan benar.
- **Produk:** SKU/barcode unik, pagination/filter berfungsi, input tidak valid ditolak, histori transaksi tidak berubah saat produk diedit.
- **Kasir:** pembayaran tunai kurang ditolak; stok kurang ditolak; request paralel tidak dapat membuat stok negatif; request berulang dengan idempotency key sama tidak membuat dua transaksi.
- **Draft:** tidak mengurangi stok; tidak melewati batas aktif per kasir; expired tidak dapat checkout; perubahan harga/stok diverifikasi ulang.
- **Inventori:** setiap mutasi stok memiliki movement dan user; transaksi rollback tidak meninggalkan movement; penjualan selesai mengurangi stok satu kali.
- **Riwayat:** filter bekerja; detail menampilkan snapshot; struk print-friendly.
- **Laporan:** hasil filter sama dengan CSV; omzet mengecualikan draft; tidak terjadi N+1 pada daftar.
- **Pengaturan:** nilai tersimpan dan digunakan pada transaksi baru, tanpa mengubah transaksi lama.
- **Security:** kasir yang mengakses URL admin menerima 403; validasi server-side; CSRF aktif; data sensitif tidak terpapar di logs.
- **UI:** memakai komponen existing yang relevan; responsif; form error, toast, modal, empty state sesuai pola template; tidak ada styling duplikat yang tidak perlu.

## 12. Skenario Pengujian Wajib
1. Checkout 2 item berhasil dan membuat item snapshot, pembayaran, serta movement.
2. Stok 1 item: dua request checkout bersamaan, hanya satu berhasil.
3. Double-click checkout/retry dengan idempotency key sama hanya menghasilkan satu transaksi.
4. Gagal menyimpan payment/item menyebabkan rollback stok dan transaksi.
5. Draft dibuat, diedit, dilanjutkan, dibatalkan, expired, dan dibatasi per kasir.
6. Draft dibuat dengan harga lama, lalu harga berubah: checkout menampilkan harga terbaru dan meminta konfirmasi.
7. Produk dinonaktifkan atau stok habis sebelum draft dilanjutkan: checkout ditolak dengan pesan jelas.
8. Diskon melebihi subtotal, qty 0/negatif, uang tunai kurang, metode pembayaran invalid ditolak.
9. Admin dapat melihat semua data; kasir hanya data yang diizinkan; direct URL ke admin 403.
10. Report 7 hari termasuk hari tanpa transaksi, filter timezone konsisten, CSV tidak rentan formula injection.
11. Produk yang pernah terjual tidak dapat dihapus hingga menghilangkan integritas riwayat.
12. Print struk memuat setting toko dan snapshot transaksi yang benar.

## 13. Tahapan Implementasi (Milestone)
### Fase 0 — Discovery (wajib)
Audit repo, komponen, auth, library JS, pola kode, identifikasi gap, rencana migrasi. **Stop dan laporkan hasil sebelum implementasi jika ada konflik besar.**

### Fase 1 — Fondasi
Finalisasi schema dan migration, seed/factory, roles/policies, store settings, route/menu, reusable backend helpers yang dibutuhkan.

### Fase 2 — Master Data
Kategori, satuan, produk; pencarian, filter, pagination, validation, komponen existing.

### Fase 3 — Inventori
Stok, stock movements, penyesuaian manual, pengujian integrity.

### Fase 4 — Kasir Inti
Keranjang, product search/barcode, kalkulasi, pembayaran, atomic checkout, idempotency, struk.

### Fase 5 — Draft dan Riwayat
Simpan/lanjut/batal draft, expiry, detail transaksi, filter, print.

### Fase 6 — Dashboard dan Laporan
KPI, Chart.js, stok menipis, produk terlaris, filter, ekspor CSV.

### Fase 7 — Hardening dan UAT
Role matrix, concurrency, regression, performance, responsive UI, error/empty states, dokumentasi deployment.

**Aturan fase:** selesaikan dan tes satu fase sebelum pindah; tampilkan daftar file berubah, migrations, routes, test results, isu, dan keputusan desain.

## 14. Deliverables
- Implementasi di codebase Laravel starter kit existing.
- `docs/pos/codebase-audit.md` dan `docs/pos/implementation-plan.md`.
- Migration, model, request, policy, service, controller, Blade views, JS minimal, seed/factory, automated tests.
- `docs/pos/database-schema.md`, `docs/pos/business-rules.md`, `docs/pos/testing-checklist.md`.
- README instalasi, env vars, migrate/seed, test, build, scheduler jika dipakai, panduan admin/kasir.
- Tidak ada placeholder dummy pada fitur yang dinyatakan selesai.

## 15. Definition of Done
Fitur sesuai acceptance criteria, authorization aman, transaksi stok konsisten, automated tests lulus, migration berjalan di MySQL, halaman memakai komponen starter kit, UI desktop/mobile layak, tidak ada duplikasi design system, dan dokumentasi sinkron dengan implementasi.

## 16. Instruksi Siap Tempel untuk AI Agent
> Baca dokumen `PRD_POS_Laravel_Blade_Starterkit.md` sampai selesai. **Jangan langsung coding.** Audit codebase Laravel starter kit terlebih dahulu: versi framework, auth, migrations, struktur route/controller/model, seluruh Blade components, pola toast/modal, dashboard layout, Choices.js, Chart.js, dan library lain yang terpasang. Buat `docs/pos/codebase-audit.md` berisi inventaris komponen aktual beserta lokasi dan contoh penggunaannya, mapping kebutuhan POS ke komponen existing, gap dan risiko. Setelah itu buat rencana implementasi bertahap. Fokus backend, reuse Blade components dan styling existing, jangan membuat design system atau komponen duplikat, jangan mengasumsikan tag komponen tanpa memeriksa file. Terapkan keamanan, transaksi database, locking stok, idempotency checkout, dan tests. Kerjakan per fase dan laporkan hasilnya sebelum melanjutkan.

## 17. Keputusan Awal yang Boleh Diubah Setelah Audit
- POS single-store, stok satu lokasi.
- Kuantitas stok integer, tanpa pecahan atau konversi satuan.
- Harga dan pembayaran integer rupiah.
- Pajak level transaksi, diskon nominal level transaksi.
- Pembayaran non-tunai dicatat manual.
- Draft tidak mereservasi stok.
- Laporan CSV sebagai format ekspor utama.
- Void/refund, multi-payment, offline PWA, dan multi-outlet ditunda.

Perubahan keputusan harus dicatat dalam `docs/pos/decision-log.md` beserta alasan dan dampaknya.
