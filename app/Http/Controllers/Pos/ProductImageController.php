<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductImageController extends Controller
{
    public function __invoke(Product $product): StreamedResponse
    {
        $path = $product->image_path;
        abort_unless(is_string($path) && str_starts_with($path, 'products/') && ! str_contains($path, '..'), 404);
        $disk = Storage::disk('public');
        abort_unless($disk->exists($path), 404);
        abort_unless(in_array($disk->mimeType($path), ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true), 404);

        return $disk->response($path, null, ['Cache-Control' => 'private, max-age=86400', 'X-Content-Type-Options' => 'nosniff']);
    }
}
