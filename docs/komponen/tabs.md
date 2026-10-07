# 🗂️ Tabs

<ComponentPreview name="tabs" :height="330" />

`<x-ui.tabs>` menyediakan tab Blade dengan panel yang semuanya dirender server, lalu perpindahannya ditangani `resources/js/admin/interactions.js`. Sumber: `resources/views/components/ui/tabs.blade.php`.

## 🧩 Prop & slot bernama

| Prop | Default | Kegunaan |
| --- | --- | --- |
| `id` | wajib | Prefix ID tab dan panel; harus unik |
| `tabs` | wajib | Array `key => label`, misalnya `['summary' => 'Ringkasan']` |
| `variant` | `underline` | `underline`, `pill`, `soft` |
| `active` | key pertama | Key panel yang terlihat saat pertama dirender |
| `aria-label` | `Navigasi tab` | Nama tablist |

Setiap key pada `tabs` membutuhkan slot `tab_{key}`. Gunakan key alfanumerik/underscore yang valid untuk nama slot dan ID HTML.

## 📖 Underline dan pill

```blade
<x-ui.tabs id="detail" aria-label="Detail entri" :tabs="['summary' => 'Ringkasan', 'history' => 'Riwayat']">
    <x-slot:tab_summary><p>Ringkasan data entri.</p></x-slot:tab_summary>
    <x-slot:tab_history><p>Riwayat pembaruan entri.</p></x-slot:tab_history>
</x-ui.tabs>

<x-ui.tabs id="laporan" variant="pill" active="table" :tabs="['chart' => 'Grafik', 'table' => 'Tabel']">
    <x-slot:tab_chart><p>Grafik.</p></x-slot:tab_chart>
    <x-slot:tab_table><p>Tabel.</p></x-slot:tab_table>
</x-ui.tabs>
```

## 🎨 Varian soft dan interaksi

```blade
<x-ui.tabs id="pengaturan" variant="soft" :tabs="['profile' => 'Profil', 'access' => 'Akses']">
    <x-slot:tab_profile><p>Pengaturan profil.</p></x-slot:tab_profile>
    <x-slot:tab_access><p>Pengaturan akses.</p></x-slot:tab_access>
</x-ui.tabs>
```

Klik dan tombol panah kiri/kanan berpindah panel; grafik pada panel diinisialisasi setelah tab dipilih. Seluruh konten panel tetap tersedia di HTML sejak awal; tidak ada lazy load/AJAX bawaan. Untuk data sensitif, pastikan akses diperiksa di server sebelum view dirender.
