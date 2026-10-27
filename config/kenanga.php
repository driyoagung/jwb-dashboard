<?php

$names = [
    'Dewi Anggraini', 'Bagas Prasetyo', 'Maya Kusuma', 'Rizky Hidayat', 'Lestari Wulandari', 'Fajar Nugroho',
    'Putri Maharani', 'Andika Saputra', 'Nadia Permata', 'Yoga Pratama', 'Siti Aisyah', 'Hendra Wijaya',
    'Ratna Sari', 'Dimas Aryo', 'Citra Lestari', 'Eko Susanto', 'Indah Kurnia', 'Galih Ramadhan',
    'Wulan Safitri', 'Taufik Hidayah', 'Anisa Rahma', 'Bayu Setiawan', 'Kartika Dewi', 'Rendra Mahesa',
];
$roles = ['Admin', 'Editor', 'Kasir', 'Gudang', 'Pelanggan'];
$statuses = [['Aktif', 'success'], ['Aktif', 'success'], ['Aktif', 'success'], ['Menunggu', 'warning'], ['Nonaktif', 'neutral']];
$avatarTones = ['soft', 'info', 'success', 'warning', 'danger'];
$seen = ['Baru saja', '5 menit lalu', '1 jam lalu', 'Kemarin', '3 hari lalu', '1 minggu lalu', '2 minggu lalu'];
$users = array_map(function (string $name, int $index) use ($roles, $statuses, $avatarTones, $seen): array {
    $status = $statuses[$index % count($statuses)];
    $parts = preg_split('/\s+/', $name) ?: [];
    $initials = implode('', array_map(static fn (string $part): string => mb_substr($part, 0, 1), array_slice($parts, 0, 2)));

    return [
        'name' => $name,
        'initials' => $initials,
        'email' => strtolower(str_replace(' ', '.', $name)).'@email.id',
        'avTone' => $avatarTones[$index % count($avatarTones)],
        'role' => $roles[($index * 3) % count($roles)],
        'status' => $status[0],
        'statusKey' => strtolower($status[0]),
        'tone' => $status[1],
        'orders' => (($index * 37) % 90) + 3,
        'seen' => $seen[$index % count($seen)],
    ];
}, $names, array_keys($names));

return [
    'name' => 'Griyo POS',
    'brand' => [
        'name' => 'Griyo POS',
        'workspace' => 'Griyo POS',
        'description' => 'Point of Sale',
        'footer' => 'Point of Sale',
    ],
    'showcase' => (bool) env('ADMIN_SHOWCASE', true),
    'appearance' => ['theme' => 'light', 'accent' => 'indigo', 'radius' => '0.3'],
    'accents' => [['id' => 'indigo', 'name' => 'Indigo', 'hex' => '#4f46e5']],
    'radii' => [['value' => '0.3', 'name' => 'Kecil']],
    'navigation' => [
        ['label' => 'Ringkasan', 'items' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'dashboard'],
            ['label' => 'Notifikasi', 'route' => 'pos.notifications.index', 'active' => 'pos.notifications.*', 'icon' => 'bell'],
        ]],
        ['label' => 'Penjualan', 'items' => [
            ['label' => 'Kasir', 'route' => 'pos.cashier', 'active' => 'pos.cashier', 'icon' => 'cart'],
            ['label' => 'Draft', 'route' => 'pos.drafts.index', 'active' => 'pos.drafts.*', 'icon' => 'file-text'],
            ['label' => 'Riwayat transaksi', 'route' => 'pos.transactions.index', 'active' => 'pos.transactions.*', 'icon' => 'table'],
            ['label' => 'Kasbon', 'route' => 'pos.receivables.index', 'active' => 'pos.receivables.*', 'icon' => 'card'],
            ['label' => 'Pelanggan', 'route' => 'pos.customers.index', 'active' => 'pos.customers.*', 'icon' => 'users'],
        ]],
        ['label' => 'Master data', 'ability' => 'pos.admin', 'items' => [
            ['label' => 'Produk', 'route' => 'pos.manage.products.index', 'active' => 'pos.manage.products.*', 'icon' => 'package'],
            ['label' => 'Kategori', 'route' => 'pos.manage.categories.index', 'active' => 'pos.manage.categories.*', 'icon' => 'grid'],
            ['label' => 'Satuan', 'route' => 'pos.manage.units.index', 'active' => 'pos.manage.units.*', 'icon' => 'layers'],
        ]],
        ['label' => 'Inventori', 'ability' => 'pos.admin', 'items' => [
            ['label' => 'Stok', 'route' => 'pos.manage.stock.index', 'active' => 'pos.manage.stock.index', 'icon' => 'package'],
            ['label' => 'Riwayat stok', 'route' => 'pos.manage.stock.movements', 'active' => 'pos.manage.stock.movements', 'icon' => 'activity'],
        ]],
        ['label' => 'Laporan', 'ability' => 'pos.admin', 'items' => [
            ['label' => 'Penjualan', 'route' => 'pos.manage.reports.show', 'params' => ['report' => 'sales'], 'active' => 'pos.manage.reports.*', 'icon' => 'chart'],
            ['label' => 'Produk terlaris', 'route' => 'pos.manage.reports.show', 'params' => ['report' => 'products'], 'icon' => 'package'],
            ['label' => 'Stok', 'route' => 'pos.manage.reports.show', 'params' => ['report' => 'stock'], 'icon' => 'layers'],
            ['label' => 'Draft', 'route' => 'pos.manage.reports.show', 'params' => ['report' => 'drafts'], 'icon' => 'file-text'],
        ]],
        ['label' => 'Pengaturan', 'ability' => 'pos.admin', 'items' => [
            ['label' => 'Pengguna', 'route' => 'pos.manage.users.index', 'active' => 'pos.manage.users.*', 'icon' => 'users'],
            ['label' => 'Toko', 'route' => 'pos.manage.settings.edit', 'active' => 'pos.manage.settings.*', 'icon' => 'settings'],
        ]],
    ],
    'demo' => [
        'records' => [
            ['code' => 'ENT-001', 'title' => 'Panduan onboarding', 'category' => 'Panduan', 'owner' => 'Ayu Rahmawati', 'status' => 'Aktif', 'statusKey' => 'aktif', 'tone' => 'success', 'updated' => 'Hari ini'],
            ['code' => 'ENT-002', 'title' => 'Catatan rapat tim', 'category' => 'Catatan', 'owner' => 'Bagas Prasetyo', 'status' => 'Draf', 'statusKey' => 'draf', 'tone' => 'warning', 'updated' => 'Kemarin'],
            ['code' => 'ENT-003', 'title' => 'Checklist peluncuran', 'category' => 'Panduan', 'owner' => 'Maya Kusuma', 'status' => 'Ditinjau', 'statusKey' => 'ditinjau', 'tone' => 'info', 'updated' => '28 Sep 2026'],
            ['code' => 'ENT-004', 'title' => 'Laporan bulanan', 'category' => 'Laporan', 'owner' => 'Rizky Hidayat', 'status' => 'Aktif', 'statusKey' => 'aktif', 'tone' => 'success', 'updated' => '27 Sep 2026'],
            ['code' => 'ENT-005', 'title' => 'Arsip prosedur lama', 'category' => 'Arsip', 'owner' => 'Fajar Nugroho', 'status' => 'Arsip', 'statusKey' => 'arsip', 'tone' => 'neutral', 'updated' => '25 Sep 2026'],
            ['code' => 'ENT-006', 'title' => 'Rencana kuartal depan', 'category' => 'Catatan', 'owner' => 'Ayu Rahmawati', 'status' => 'Draf', 'statusKey' => 'draf', 'tone' => 'warning', 'updated' => '24 Sep 2026'],
            ['code' => 'ENT-007', 'title' => 'Standar layanan', 'category' => 'Panduan', 'owner' => 'Bagas Prasetyo', 'status' => 'Aktif', 'statusKey' => 'aktif', 'tone' => 'success', 'updated' => '23 Sep 2026'],
        ],
        'users' => $users,
        'orders' => [
            ['id' => '#KK-2841', 'customer' => 'Dewi Anggraini', 'product' => 'Kopi Arabika Gayo 1 kg × 2', 'status' => 'Selesai', 'tone' => 'success', 'date' => '28 Sep 2026', 'total' => 'Rp 340.000', 'struck' => ''],
            ['id' => '#KK-2840', 'customer' => 'Bagas Prasetyo', 'product' => 'Paket coba 5 varian', 'status' => 'Dikirim', 'tone' => 'info', 'date' => '28 Sep 2026', 'total' => 'Rp 215.000', 'struck' => ''],
            ['id' => '#KK-2839', 'customer' => 'Maya Kusuma', 'product' => 'Kopi Toraja 500 g', 'status' => 'Diproses', 'tone' => 'warning', 'date' => '27 Sep 2026', 'total' => 'Rp 128.000', 'struck' => ''],
            ['id' => '#KK-2838', 'customer' => 'Rizky Hidayat', 'product' => 'French press 600 ml', 'status' => 'Selesai', 'tone' => 'success', 'date' => '27 Sep 2026', 'total' => 'Rp 265.000', 'struck' => ''],
            ['id' => '#KK-2837', 'customer' => 'Lestari Wulandari', 'product' => 'Kopi Kintamani 1 kg', 'status' => 'Dibatalkan', 'tone' => 'danger', 'date' => '26 Sep 2026', 'total' => 'Rp 189.000', 'struck' => 'text-muted-foreground line-through'],
            ['id' => '#KK-2836', 'customer' => 'Fajar Nugroho', 'product' => 'Grinder manual', 'status' => 'Dikirim', 'tone' => 'info', 'date' => '26 Sep 2026', 'total' => 'Rp 475.000', 'struck' => ''],
        ],
        'sales' => [
            ['initials' => 'DA', 'name' => 'Dewi Anggraini', 'email' => 'dewi.anggraini@email.com', 'amount' => '+Rp 340.000', 'tone' => 'soft'],
            ['initials' => 'BP', 'name' => 'Bagas Prasetyo', 'email' => 'bagas.p@email.com', 'amount' => '+Rp 215.000', 'tone' => 'info'],
            ['initials' => 'MK', 'name' => 'Maya Kusuma', 'email' => 'maya.kusuma@email.com', 'amount' => '+Rp 128.000', 'tone' => 'success'],
            ['initials' => 'RH', 'name' => 'Rizky Hidayat', 'email' => 'rizky.h@email.com', 'amount' => '+Rp 265.000', 'tone' => 'warning'],
            ['initials' => 'FN', 'name' => 'Fajar Nugroho', 'email' => 'fajar.nugroho@email.com', 'amount' => '+Rp 475.000', 'tone' => 'danger'],
        ],
        'products' => [
            ['name' => 'Arabika Gayo 1 kg', 'sku' => 'KP-001', 'category' => 'Biji kopi', 'price' => 'Rp 170.000', 'stock' => 128, 'pct' => 86, 'bar' => 'bg-positive', 'rating' => '4,9', 'status' => 'Tersedia', 'tone' => 'success'],
            ['name' => 'Robusta Temanggung 1 kg', 'sku' => 'KP-002', 'category' => 'Biji kopi', 'price' => 'Rp 120.000', 'stock' => 74, 'pct' => 52, 'bar' => 'bg-positive', 'rating' => '4,7', 'status' => 'Tersedia', 'tone' => 'success'],
            ['name' => 'Toraja Sapan 500 g', 'sku' => 'KP-003', 'category' => 'Biji kopi', 'price' => 'Rp 128.000', 'stock' => 18, 'pct' => 14, 'bar' => 'bg-caution', 'rating' => '4,8', 'status' => 'Menipis', 'tone' => 'warning'],
            ['name' => 'French press 600 ml', 'sku' => 'AL-104', 'category' => 'Peralatan', 'price' => 'Rp 265.000', 'stock' => 41, 'pct' => 34, 'bar' => 'bg-positive', 'rating' => '4,6', 'status' => 'Tersedia', 'tone' => 'success'],
            ['name' => 'Grinder manual', 'sku' => 'AL-108', 'category' => 'Peralatan', 'price' => 'Rp 475.000', 'stock' => 0, 'pct' => 0, 'bar' => 'bg-destructive', 'rating' => '4,8', 'status' => 'Habis', 'tone' => 'danger'],
            ['name' => 'Paket coba 5 varian', 'sku' => 'PK-201', 'category' => 'Paket', 'price' => 'Rp 215.000', 'stock' => 96, 'pct' => 70, 'bar' => 'bg-positive', 'rating' => '4,9', 'status' => 'Tersedia', 'tone' => 'success'],
        ],
        'activity' => [
            ['icon' => 'check-circle', 'tile' => 'bg-success-soft text-success', 'text' => 'Pesanan #KK-2841 selesai', 'time' => '10 menit lalu'],
            ['icon' => 'truck', 'tile' => 'bg-info-soft text-info', 'text' => 'Pesanan #KK-2840 dikirim via JNE', 'time' => '42 menit lalu'],
            ['icon' => 'alert-triangle', 'tile' => 'bg-warning-soft text-warning', 'text' => 'Stok Toraja Sapan 500 g menipis', 'time' => '2 jam lalu'],
            ['icon' => 'userplus', 'tile' => 'bg-soft text-soft-foreground', 'text' => 'Pelanggan baru: Putri Maharani', 'time' => '5 jam lalu'],
            ['icon' => 'alert-circle', 'tile' => 'bg-danger-soft text-danger', 'text' => 'Pesanan #KK-2837 dibatalkan', 'time' => 'Kemarin'],
        ],
    ],
];
