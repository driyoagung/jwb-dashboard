<x-layouts.admin title="Ubah entri" group="Contoh halaman">
<div><a href="{{ route('examples.records.show') }}" class="mb-3 inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground"><x-ui.icon name="arrow-left" class="h-4 w-4" />Kembali ke detail</a><x-ui.page-header title="Ubah entri" description="Contoh keadaan formulir saat data sudah terisi." /></div>
<x-examples.record-form mode="edit" />
</x-layouts.admin>
