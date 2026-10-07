<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('docs:previews', function (): void {
    View::share('errors', new ViewErrorBag);
    $manifest = json_decode(File::get(public_path('build/manifest.json')), true, flags: JSON_THROW_ON_ERROR);
    $css = $manifest['resources/css/app.css']['file'];
    $js = $manifest['resources/js/app.js']['file'];
    $destination = base_path('docs/public/preview');

    File::ensureDirectoryExists($destination);
    File::copyDirectory(public_path('build'), $destination.'/build');
    File::copy(public_path('icons.svg'), $destination.'/icons.svg');
    foreach (File::glob($destination.'/build/assets/*.css') as $stylesheet) {
        File::put($stylesheet, str_replace('/build/assets/', './', File::get($stylesheet)));
    }

    foreach (['alert', 'badge', 'button', 'card', 'chart', 'combobox', 'confirm-action',
        'date-range', 'detail-list', 'drawer', 'empty', 'field', 'file-preview',
        'filter-bar', 'icon', 'input', 'modal', 'page-header', 'pagination',
        'select', 'stat-card', 'switch', 'table-state', 'tabs', 'textarea', 'timeline'] as $previewName) {
        $html = view('docs.preview', compact('previewName', 'css', 'js'))->render();
        $html = str_replace(asset('icons.svg'), './icons.svg', $html);
        if (! str_starts_with(ltrim($html), '<!DOCTYPE html>') || ! str_contains($html, '</html>')) {
            throw new RuntimeException("Preview {$previewName} tidak menghasilkan dokumen HTML utuh.");
        }
        File::put($destination.'/'.$previewName.'.html', $html);
    }

    $this->info('26 preview Blade tersedia di docs/public/preview.');
})->purpose('Render preview Blade mandiri untuk VitePress');
