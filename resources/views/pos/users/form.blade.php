@php($editing = $user !== null)
<x-layouts.admin :title="$editing ? 'Ubah pengguna' : 'Tambah pengguna'" group="Pengaturan">
    <x-ui.page-header :title="$editing ? 'Ubah pengguna' : 'Tambah pengguna'" />
    <form method="POST" action="{{ $editing ? route('pos.manage.users.update', $user) : route('pos.manage.users.store') }}" class="space-y-6">@csrf @if ($editing) @method('PUT') @endif
        <x-ui.card title="Akun pengguna"><div class="grid gap-5 md:grid-cols-2">
            <x-ui.field label="Nama" name="name" :required="true"><x-ui.input name="name" :value="$user?->name" placeholder="Nama lengkap pengguna" required /></x-ui.field>
            <x-ui.field label="Email" name="email" :required="true"><x-ui.input name="email" type="email" :value="$user?->email" placeholder="nama@example.com" required /></x-ui.field>
            <x-ui.field label="Role" name="role" :required="true"><x-ui.select name="role" placeholder="Pilih role" :value="$user?->role ?? 'cashier'" :options="['cashier' => 'Kasir', 'admin' => 'Admin']" /></x-ui.field>
            <x-ui.field label="Status" name="is_active"><x-ui.switch name="is_active" label="Aktif" :checked="$user?->is_active ?? true" /></x-ui.field>
            <x-ui.field :label="$editing ? 'Kata sandi baru (opsional)' : 'Kata sandi'" name="password"><x-ui.input name="password" type="password" :required="! $editing" autocomplete="new-password" placeholder="Minimal 8 karakter" /></x-ui.field>
            <x-ui.field label="Konfirmasi kata sandi" name="password_confirmation"><x-ui.input name="password_confirmation" type="password" :required="! $editing" autocomplete="new-password" placeholder="Ulangi kata sandi" /></x-ui.field>
        </div></x-ui.card>
        <div class="flex justify-end gap-2"><a class="btn btn-outline" href="{{ route('pos.manage.users.index') }}"><x-ui.icon name="x" />Batal</a><x-ui.button type="submit"><x-ui.icon name="check" />Simpan</x-ui.button></div>
    </form>
</x-layouts.admin>
