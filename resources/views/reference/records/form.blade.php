@php($editing = $record !== null)

<x-layouts.admin :title="$editing ? 'Ubah entri' : 'Tambah entri'" group="Contoh halaman">
    <x-ui.page-header :title="$editing ? 'Ubah entri' : 'Tambah entri'"
        description="Form tersambung ke validasi dan penyimpanan Laravel." />

    <form method="POST" action="{{ $editing ? route('reference.records.update', $record) : route('reference.records.store') }}" class="space-y-6">
        @csrf
        @if ($editing) @method('PUT') @endif

        <x-ui.card title="Informasi utama">
            <div class="grid gap-5 md:grid-cols-2">
                <x-ui.field label="Judul entri" name="title" :required="true" class="md:col-span-2">
                    <x-ui.input name="title" :value="$record?->title" required maxlength="255" />
                </x-ui.field>
                <x-ui.field label="Kategori" name="category" :required="true">
                    <x-ui.select name="category" placeholder="Pilih kategori" :value="$record?->category"
                        :options="['guide' => 'Panduan', 'report' => 'Laporan', 'note' => 'Catatan', 'archive' => 'Arsip']" required />
                </x-ui.field>
                <x-ui.field label="Status" name="status" :required="true">
                    <x-ui.select name="status" :value="$record?->status ?? 'draft'"
                        :options="['draft' => 'Draf', 'review' => 'Ditinjau', 'active' => 'Aktif', 'archive' => 'Arsip']" required />
                </x-ui.field>
                <x-ui.field label="Ringkasan" name="summary" class="md:col-span-2">
                    <x-ui.textarea name="summary" :value="$record?->summary" rows="4" maxlength="5000" />
                </x-ui.field>
            </div>
        </x-ui.card>

        <div class="flex flex-wrap justify-end gap-2">
            <a href="{{ $editing ? route('reference.records.show', $record) : route('reference.records.index') }}" class="btn btn-outline">Batal</a>
            <x-ui.button type="submit">{{ $editing ? 'Simpan perubahan' : 'Buat entri' }}</x-ui.button>
        </div>
    </form>
</x-layouts.admin>
