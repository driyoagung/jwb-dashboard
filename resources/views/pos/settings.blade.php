<x-layouts.admin title="Pengaturan toko" group="Pengaturan">
    <x-ui.page-header title="Pengaturan toko" description="Identitas toko dan aturan transaksi baru." />
    <form method="POST" action="{{ route('pos.manage.settings.update') }}" class="space-y-6">@csrf @method('PUT')
        <x-ui.card title="Identitas toko"><div class="grid gap-5 md:grid-cols-2">
            <x-ui.field label="Nama toko" name="store_name" :required="true"><x-ui.input name="store_name" :value="$settings->store_name" placeholder="Nama toko pada struk" required /></x-ui.field>
            <x-ui.field label="Telepon" name="phone"><x-ui.input name="phone" :value="$settings->phone" placeholder="Contoh: 021-555-0188" /></x-ui.field>
            <x-ui.field label="Alamat" name="address" class="md:col-span-2"><x-ui.textarea name="address" :value="$settings->address" placeholder="Alamat lengkap toko" /></x-ui.field>
            <x-ui.field label="Footer struk" name="receipt_footer" class="md:col-span-2"><x-ui.textarea name="receipt_footer" :value="$settings->receipt_footer" placeholder="Pesan penutup pada struk" /></x-ui.field>
        </div></x-ui.card>
        <x-ui.card title="Aturan transaksi"><div class="grid gap-5 md:grid-cols-2">
            <x-ui.field label="Pajak (%)" name="tax_rate"><x-ui.input name="tax_rate" type="number" min="0" max="100" step="0.01" :value="$settings->tax_rate" placeholder="Contoh: 11" required /></x-ui.field>
            <x-ui.field label="Kedaluwarsa draft (jam)" name="draft_expiry_hours"><x-ui.input name="draft_expiry_hours" type="number" min="1" max="720" :value="$settings->draft_expiry_hours" placeholder="Contoh: 24" required data-grouped-number /></x-ui.field>
            <x-ui.field label="Maksimal draft aktif per kasir" name="max_active_drafts"><x-ui.input name="max_active_drafts" type="number" min="1" max="100" :value="$settings->max_active_drafts" placeholder="Contoh: 20" required data-grouped-number /></x-ui.field>
            <x-ui.field label="Zona waktu" name="timezone"><x-ui.select name="timezone" placeholder="Pilih zona waktu" :value="$settings->timezone" :options="['Asia/Jakarta' => 'Asia/Jakarta', 'Asia/Makassar' => 'Asia/Makassar', 'Asia/Jayapura' => 'Asia/Jayapura']" /></x-ui.field>
        </div></x-ui.card>
        <div class="flex justify-end"><x-ui.button type="submit"><x-ui.icon name="check" />Simpan pengaturan</x-ui.button></div>
    </form>
</x-layouts.admin>
