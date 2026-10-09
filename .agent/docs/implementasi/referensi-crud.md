# Referensi CRUD nyata

Contoh tersimpan berada di `/reference/records` saat `ADMIN_SHOWCASE=true`. Ini fitur latihan yang dapat ditiru saat membangun domain proyek baru; contoh lama di `/examples/records` tetap katalog UI frontend. Referensi ini memakai komponen Kenanga yang sudah ada, tanpa komponen UI atau library tabel baru.

## Jalur kode

| Bagian | Implementasi |
| --- | --- |
| Route | `routes/web.php`: `reference.records.*`, middleware `auth` |
| Model dan migrasi | `app/Models/ReferenceRecord.php`, `database/migrations/*create_reference_records_table.php` |
| Relasi | `User::referenceRecords()`; setiap record milik satu user |
| Validasi | `app/Http/Requests/ReferenceRecordIndexRequest.php`, `StoreReferenceRecordRequest.php`, `UpdateReferenceRecordRequest.php` |
| Otorisasi | `app/Policies/ReferenceRecordPolicy.php`, didaftarkan di `AppServiceProvider` |
| HTTP | `app/Http/Controllers/ReferenceRecordController.php` |
| View | `resources/views/reference/records/`: index, form create/edit, detail |
| Login/logout | `AuthController`, `LoginRequest`, `resources/views/auth/login.blade.php` |
| Test | `tests/Feature/ReferenceRecordFeatureTest.php` |

Jalankan `php artisan migrate`, lalu buat user lokal melalui cara provisioning proyek Anda dan buka `/login`. Starter kit tidak menyediakan registrasi publik atau sistem role siap pakai. Akun development dari `DatabaseSeeder` adalah data contoh; jangan gunakan kredensial contoh untuk produksi.

## Pola yang perlu ditiru

1. Form Request memvalidasi filter dan data tulis. `authorize()` memeriksa hak akses, sementara policy menolak baca/ubah/hapus milik user lain.
2. Controller daftar memulai query dari `$request->user()->referenceRecords()`, kemudian menerapkan filter dan `paginate(15)->withQueryString()`. Tidak ada `data-table` karena dataset diproses server.
3. Controller menyimpan lewat relasi user agar `user_id` tidak berasal dari input. Model hanya mengizinkan field domain di `$fillable`.
4. View memakai `x-layouts.admin`, `x-ui.filter-bar`, `x-ui.card`, tabel `.table`, `x-ui.table-state`, `x-ui.field`, `x-ui.input`, `x-ui.select`, `x-ui.textarea`, `x-ui.detail-list`, dan `x-ui.confirm-action`.
5. Feature test meliputi guest, login/logout, validasi, CRUD, otorisasi pemilik, filter, dan pagination.

Saat menyalin pola ke proyek nyata, ganti nama domain, field, policy, menu, dan aturan role. Route dashboard/analytics/settings bawaan masih berupa showcase publik; jangan menganggap fitur referensi ini otomatis melindungi seluruh dashboard. Tentukan cakupan `auth` dan policy proyek pada [kontrak role](../standar/kontrak-role.md).
