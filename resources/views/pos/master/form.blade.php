@php($editing = $record !== null)
<x-layouts.admin :title="($editing ? 'Ubah ' : 'Tambah ').$title" group="Master data">
    <x-ui.page-header :title="($editing ? 'Ubah ' : 'Tambah ').$title" />
    <form method="POST" action="{{ $editing ? route('pos.manage.'.$kind.'.update', $record) : route('pos.manage.'.$kind.'.store') }}" class="space-y-6">
        @csrf @if ($editing) @method('PUT') @endif
        <x-ui.card title="Informasi utama">
            <div class="grid gap-5 md:grid-cols-2">
                <x-ui.field label="Nama" name="name" :required="true"><x-ui.input name="name" :value="$record?->name" :placeholder="$kind === 'units' ? 'Contoh: Kilogram' : 'Contoh: Minuman'" required maxlength="255" /></x-ui.field>
                @if ($kind === 'units')<x-ui.field label="Singkatan" name="symbol" :required="true"><x-ui.input name="symbol" :value="$record?->symbol" placeholder="Contoh: kg" required maxlength="24" /></x-ui.field>@endif
                <x-ui.field label="Status" name="is_active"><x-ui.switch name="is_active" label="Aktif" :checked="$record?->is_active ?? true" /></x-ui.field>
            </div>
        </x-ui.card>
        <div class="flex justify-end gap-2"><a class="btn btn-outline" href="{{ route('pos.manage.'.$kind.'.index') }}"><x-ui.icon name="x" />Batal</a><x-ui.button type="submit"><x-ui.icon name="check" />Simpan</x-ui.button></div>
    </form>
</x-layouts.admin>
