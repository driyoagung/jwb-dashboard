# Kenanga Admin: template dashboard (HTML + Tailwind)

Template admin bergaya shadcn/ui: bersih, tanpa gradasi dan glassmorphism, dark/light mode,
12 warna aksen yang bisa dipilih langsung, ikon SVG, dan 11 halaman contoh.

## Cara pakai

```bash
node build.mjs        # membuat dist/*.html (butuh Node 18+, tanpa npm install)
```

Buka `dist/index.html` langsung di browser (double-click). Tailwind dan font dimuat dari CDN,
jadi perlu koneksi internet.

`node build.mjs --preview hasil.html` membuat satu file pratinjau berisi semua halaman.

## Struktur proyek

```
src/
  layouts/     app.html (sidebar + header), blank.html (login, 404)
  partials/    komponen yang bisa dipakai ulang (lihat tabel di bawah)
  pages/       satu file per halaman; baris pertama = metadata halaman
  assets/
    app.js     perilaku interaktif (tema, sidebar, tab, dropdown, modal, tabel, toast)
    charts.js  grafik SVG tanpa dependensi
  data.mjs     data contoh (users, orders, products, ...)
  icons.mjs    pustaka ikon (gaya Lucide)
build.mjs      skrip build tanpa dependensi
dist/          hasil build
```

### Sintaks template

| Sintaks | Fungsi |
| --- | --- |
| `{{> nama prop="nilai"}}` | sisipkan `src/partials/nama.html` dengan props |
| `{{prop}}`, `{{prop\|\|bawaan}}` | nilai prop di dalam partial |
| `{{#if prop}}...{{/if}}` | tampil bila prop diisi |
| `{{#each users}}...{{this.name}}...{{/each}}` | ulang data dari `data.mjs` |

Metadata halaman: `<!--page title="Tabel" group="Komponen" layout="app"-->` di baris pertama.

## Halaman

| File | Isi |
| --- | --- |
| `index` | Dashboard: stat card + sparkline, grafik, penjualan, tabel pesanan, donat |
| `analytics` | Galeri grafik: area, garis, batang, tumpuk, donat, radial, hbar, sparkline |
| `tables` | Tabel interaktif (cari, filter, urut, pilih, paginasi), striped, padat + total, produk, kosong |
| `cards` | Stat card (4 gaya), profil, harga, produk, aktivitas, tugas, pesan, kutipan |
| `forms` | Input, grup input, validasi, select, checkbox, radio, switch, slider, dropzone |
| `buttons` | Tombol (10 varian, ukuran, ikon, loading), grup, lencana, avatar, chip, tooltip |
| `feedback` | Alert, toast, modal, drawer, progres, spinner, skeleton |
| `navigation` | Tab (3 gaya), akordeon, dropdown, paginasi, breadcrumb, stepper, menu vertikal |
| `settings` | Halaman pengaturan akun dengan tab |
| `login`, `404` | Halaman tanpa shell |

## Kustomisasi

Tombol palet di header membuka panel: tema (terang, gelap, sistem), **12 warna aksen**, radius sudut,
dan mode sidebar (penuh atau ikon). Semua tersimpan di `localStorage`.

Menambah warna aksen: tambahkan satu baris `:root[data-accent="nama"] { ... }` di `src/partials/head.html`
(8 variabel, ikuti contoh yang ada) dan satu entri di `accents` pada `src/data.mjs`.

Token warna memakai format HSL ala shadcn (`--primary`, `--card`, `--muted`, dst), ditambah warna semantik
(`--success`, `--danger`, `--warning`, `--info`) dan warna grafik (`--chart-1..5`).

## Migrasi ke React

Struktur ini sengaja dibuat 1:1 dengan komponen React.

**Layout dan bagian halaman**

| Sekarang | Di React |
| --- | --- |
| `layouts/app.html` | `components/layout/AppLayout.tsx` (`<Outlet />`) |
| `partials/sidebar.html`, `header.html` | `Sidebar.tsx`, `Header.tsx` |
| `partials/customizer.html` | `ThemeCustomizer.tsx` |
| `pages/*.html` | route/halaman (`pages/Dashboard.tsx`, dst) |

**Komponen (class semantik menjadi komponen dengan varian)**

| Class | Komponen React |
| --- | --- |
| `btn btn-primary/outline/...` | `<Button variant size />` |
| `card`, `card-header`, `card-content` | `<Card>`, `<CardHeader>`, `<CardContent>` |
| `badge badge-success` | `<Badge variant="success" />` |
| `avatar`, `avatar-group` | `<Avatar>`, `<AvatarGroup>` |
| `input`, `select`, `textarea`, `switch` | `<Input>`, `<Select>`, `<Textarea>`, `<Switch>` |
| `table`, `data-table` | `<DataTable columns data />` (TanStack Table) |
| `tabs-*` + `data-tabs` | `<Tabs>` |
| `menu` + `data-dropdown` | `<DropdownMenu>` |
| `dialog.modal`, `dialog.drawer` | `<Dialog>`, `<Sheet>` |
| `alert-*` | `<Alert variant>` |
| `partials/stat-card.html` | `<StatCard title value icon tone delta trend spark />` |

**Perilaku (`app.js`)**

- Tema, aksen, radius, sidebar: `PreferencesContext` + `localStorage`, tulis ke `document.documentElement.dataset`.
- Dropdown, tab, modal: pakai shadcn/ui (Radix), atau state lokal `useState`.
- Toast: `sonner`.
- Tabel: TanStack Table (sorting, filtering, pagination bawaan).
- Grafik: Recharts atau Chart.js. Format data (`labels`, `series`, `items`) sengaja dibuat sama dengan prop
  yang dipakai library tersebut.

**Styling**

Class semantik ada di `<style type="text/tailwindcss">` pada `partials/head.html`. Saat pindah ke React
dengan Tailwind terpasang, salin blok itu ke `globals.css` (di dalam `@layer components`) dan salin
`tailwind.config` (warna dan radius) ke `tailwind.config.ts`. Token warna (blok `:root`) ikut disalin apa adanya.
Cara lain: ubah tiap class menjadi varian `cva()` seperti shadcn/ui.

**Ikon**

Sprite SVG bergaya Lucide. Di React pakai `lucide-react`; nama ikon di `icons.mjs` sama dengan nama Lucide
(kecuali beberapa alias: `edit` = `Pencil`, `pointer` = `MousePointer2`, `message` = `MessageSquare`,
`grid` = `LayoutGrid`, `login` = `LogIn`, `logout` = `LogOut`, `updown` = `ChevronsUpDown`).
