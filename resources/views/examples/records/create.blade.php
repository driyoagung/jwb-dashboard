<x-layouts.admin title="Tambah entri" group="Contoh halaman">
<div><a href="{{ route('examples.records.index') }}" class="mb-3 inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground"><x-ui.icon name="arrow-left" class="h-4 w-4" />Kembali ke daftar</a><x-ui.page-header title="Tambah entri" description="Contoh formulir pembuatan data tanpa penyimpanan backend." /></div>
<x-examples.record-form />
</x-layouts.admin>
