<x-layouts.admin :title="$customer ? 'Ubah pelanggan' : 'Tambah pelanggan'" group="Penjualan">
    <x-ui.page-header :title="$customer ? 'Ubah pelanggan' : 'Tambah pelanggan'" description="Data pelanggan untuk pencatatan kasbon." />
    <x-ui.card title="Informasi pelanggan">
        <form method="POST" action="{{ $customer ? route('pos.customers.update', $customer) : route('pos.customers.store') }}" class="space-y-4">@csrf @if ($customer) @method('PUT') @endif
            @if (request('return_to') === 'cashier')<input type="hidden" name="return_to" value="cashier">@endif
            <x-ui.field label="Nama" name="name" required><x-ui.input name="name" :value="$customer?->name" placeholder="Contoh: Budi Santoso" required /></x-ui.field>
            <x-ui.field label="Nomor telepon (opsional)" name="phone"><x-ui.input name="phone" :value="$customer?->phone" placeholder="Contoh: 081234567890" /></x-ui.field>
            <x-ui.field label="Alamat (opsional)" name="address"><x-ui.textarea name="address" :value="$customer?->address" placeholder="Alamat pelanggan" /></x-ui.field>
            @if ($customer)<x-ui.field label="Status" name="is_active"><x-ui.select name="is_active" :value="$customer->is_active ? '1' : '0'" :options="['1' => 'Aktif', '0' => 'Nonaktif']" /></x-ui.field>@endif
            <div class="flex gap-2"><x-ui.button type="submit"><x-ui.icon name="check" />Simpan</x-ui.button><a href="{{ route('pos.customers.index') }}" class="btn btn-outline">Batal</a></div>
        </form>
    </x-ui.card>
</x-layouts.admin>
