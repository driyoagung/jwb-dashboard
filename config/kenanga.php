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
    'name' => env('ADMIN_NAME', 'Kenanga Admin'),
    'brand' => [
        'name' => env('ADMIN_BRAND_NAME', 'Kenanga Admin'),
        'workspace' => env('ADMIN_WORKSPACE', 'Kopi Kenanga'),
        'description' => env('ADMIN_WORKSPACE_DESCRIPTION', 'Toko online'),
        'footer' => 'Starter kit Laravel Blade',
    ],
    'showcase' => (bool) env('ADMIN_SHOWCASE', true),
    'navigation' => [
        ['label' => 'Platform', 'items' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'dashboard'],
            ['label' => 'Analitik', 'route' => 'admin.analytics', 'active' => 'admin.analytics', 'icon' => 'chart', 'badge' => 'Baru'],
        ]],
        ['label' => 'Komponen', 'showcase' => true, 'items' => [
            ['label' => 'Kartu', 'route' => 'showcase.cards', 'active' => 'showcase.cards', 'icon' => 'grid'],
            ['label' => 'Tabel', 'route' => 'showcase.tables', 'active' => 'showcase.tables', 'icon' => 'table'],
            ['label' => 'Formulir', 'route' => 'showcase.forms', 'active' => 'showcase.forms', 'icon' => 'file-text'],
            ['label' => 'Tombol dan lencana', 'route' => 'showcase.buttons', 'active' => 'showcase.buttons', 'icon' => 'pointer'],
            ['label' => 'Umpan balik', 'route' => 'showcase.feedback', 'active' => 'showcase.feedback', 'icon' => 'message'],
            ['label' => 'Navigasi', 'route' => 'showcase.navigation', 'active' => 'showcase.navigation', 'icon' => 'compass'],
        ]],
        ['label' => 'Halaman', 'items' => [
            ['label' => 'Pengaturan akun', 'route' => 'admin.settings', 'active' => 'admin.settings', 'icon' => 'settings'],
            ['id' => 'auth', 'label' => 'Otentikasi', 'icon' => 'lock', 'children' => [
                ['label' => 'Masuk', 'route' => 'login', 'active' => 'login'],
                ['label' => 'Kesalahan 404', 'route' => 'demo.404', 'active' => 'demo.404'],
            ]],
        ]],
    ],
    'accents' => [
        ['id' => 'zinc', 'name' => 'Zinc', 'hex' => '#18181b'], ['id' => 'blue', 'name' => 'Biru', 'hex' => '#2563eb'],
        ['id' => 'indigo', 'name' => 'Indigo', 'hex' => '#4f46e5'], ['id' => 'violet', 'name' => 'Violet', 'hex' => '#7c3aed'],
        ['id' => 'pink', 'name' => 'Pink', 'hex' => '#db2777'], ['id' => 'rose', 'name' => 'Rose', 'hex' => '#e11d48'],
        ['id' => 'orange', 'name' => 'Oranye', 'hex' => '#c2410c'], ['id' => 'amber', 'name' => 'Amber', 'hex' => '#f59e0b'],
        ['id' => 'green', 'name' => 'Hijau', 'hex' => '#15803d'], ['id' => 'emerald', 'name' => 'Emerald', 'hex' => '#059669'],
        ['id' => 'teal', 'name' => 'Teal', 'hex' => '#0d9488'], ['id' => 'cyan', 'name' => 'Cyan', 'hex' => '#0e7490'],
    ],
    'radii' => [
        ['value' => '0', 'name' => 'Tajam'], ['value' => '0.3', 'name' => 'Kecil'],
        ['value' => '0.5', 'name' => 'Sedang'], ['value' => '0.75', 'name' => 'Besar'], ['value' => '1', 'name' => 'Bulat'],
    ],
    'demo' => [
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
