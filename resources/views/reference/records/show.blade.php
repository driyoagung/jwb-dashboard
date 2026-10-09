<x-layouts.admin title="Detail entri" group="Contoh halaman">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <x-ui.page-header :title="$record->title" description="Detail entri yang tersimpan." />
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('reference.records.index') }}" class="btn btn-outline">Kembali</a>
            <a href="{{ route('reference.records.edit', $record) }}" class="btn btn-primary"><x-ui.icon name="edit" />Ubah</a>
            <x-ui.button variant="destructive" data-modal-open="#delete-record">Hapus</x-ui.button>
        </div>
    </div>

    @if (session('status'))
        <x-ui.alert variant="success" title="Berhasil">{{ session('status') }}</x-ui.alert>
    @endif

    <x-ui.detail-list title="Informasi entri" :items="[
        ['label' => 'Kategori', 'value' => ucfirst($record->category)],
        ['label' => 'Status', 'value' => ucfirst($record->status)],
        ['label' => 'Dibuat', 'value' => $record->created_at->format('d M Y H:i')],
        ['label' => 'Terakhir diubah', 'value' => $record->updated_at->format('d M Y H:i')],
    ]" />

    <x-ui.card title="Ringkasan">
        <p class="whitespace-pre-line">{{ $record->summary ?: 'Belum ada ringkasan.' }}</p>
    </x-ui.card>

    <x-ui.confirm-action id="delete-record" title="Hapus entri?"
        description="Entri yang dihapus tidak dapat dipulihkan."
        :action="route('reference.records.destroy', $record)" />
</x-layouts.admin>
