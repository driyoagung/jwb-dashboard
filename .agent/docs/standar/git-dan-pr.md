# Konvensi commit dan pull request

Setelah menyelesaikan **setiap task yang mengubah repository**, berikan tepat **satu usulan commit message** pada laporan akhir. Tulis dalam **satu baris, satu kalimat bahasa Inggris**, dengan format Conventional Commits:

```text
<type>(<scope>): <imperative summary>
```

`scope` opsional. Pilih `type` sesuai perubahan utama: `feat`, `fix`, `docs`, `refactor`, `test`, `perf`, `build`, `ci`, atau `chore`. Ringkasan memakai kata kerja imperative, huruf kecil setelah titik dua, dan tanpa titik penutup. Jelaskan hasil perubahan, bukan aktivitas agent. Untuk perubahan yang memutus kompatibilitas, pakai tanda `!` dan jelaskan dampaknya pada body commit/PR jika commit atau PR benar-benar dibuat.

Contoh:

```text
docs(agent): define Laravel project conventions
feat(records): add owner-scoped CRUD reference
fix(pagination): render server-side page links
```

Jika task mencakup beberapa jenis perubahan, pilih jenis yang paling mewakili hasil utama; jangan menggabungkan beberapa usulan message. Jangan menyatakan commit sudah dibuat bila agent hanya menyiapkan usulannya. Saat pengguna meminta commit, gunakan usulan message tersebut atau perbaiki agar sesuai perubahan final.

## Pull request

Gunakan `.github/PULL_REQUEST_TEMPLATE.md` untuk body setiap PR. Judul PR juga mengikuti format Conventional Commits satu baris bahasa Inggris, misalnya `feat(records): add owner-scoped CRUD reference`. Isi ringkasan, perubahan utama, bukti verifikasi, dampak/risiko, dan pekerjaan lanjutan yang benar-benar ada. Hapus petunjuk placeholder sebelum mengirim PR; jangan menandai pengujian yang tidak dijalankan sebagai selesai.

Template PR adalah panduan penulisan; GitHub tidak otomatis memvalidasi format judul. Jika proyek membutuhkan penegakan otomatis, tambahkan pemeriksaan CI khusus saat infrastruktur CI tersedia.