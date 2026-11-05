<?php

namespace App\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class ProductImages
{
    public static function disk(string $path): Filesystem
    {
        return Storage::disk(str_starts_with($path, 'uploads/products/') ? 'product_uploads' : 'public');
    }
}
