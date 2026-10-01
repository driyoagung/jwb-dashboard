@props(['mode' => 'create'])

@php
    $editing = $mode === 'edit';
@endphp

<form data-demo-form data-demo-message="{{ $editing ? 'Pratinjau perubahan selesai. Data tidak disimpan.' : 'Pratinjau entri baru selesai. Data tidak disimpan.' }}" class="space-y-6">
    <div class="card">
        <div class="card-header"><h2 class="card-title">Informasi utama</h2><p class="card-desc">Contoh struktur formulir untuk berbagai jenis data.</p></div>
        <div class="card-content grid gap-5 md:grid-cols-2">
            <x-ui.field label="Judul entri" name="title" required class="md:col-span-2">
                <x-ui.input name="title" :value="$editing ? 'Panduan onboarding' : null" placeholder="Contoh: Panduan onboarding" required />
            </x-ui.field>
            <x-ui.field label="Kategori" name="category" required>
                <x-ui.select name="category" placeholder="Pilih kategori" :value="$editing ? 'panduan' : null" :options="['panduan' => 'Panduan', 'laporan' => 'Laporan', 'catatan' => 'Catatan', 'arsip' => 'Arsip']" required />
            </x-ui.field>
            <x-ui.field label="Status" name="status" required>
                <x-ui.select name="status" :value="$editing ? 'aktif' : 'draf'" :options="['draf' => 'Draf', 'ditinjau' => 'Ditinjau', 'aktif' => 'Aktif', 'arsip' => 'Arsip']" required />
            </x-ui.field>
            <x-ui.field label="Ringkasan" name="summary" help="Satu atau dua kalimat yang menjelaskan isi entri." class="md:col-span-2">
                <x-ui.textarea name="summary" :value="$editing ? 'Langkah awal bagi anggota tim yang baru bergabung.' : null" rows="4" placeholder="Tulis ringkasan singkat…" />
            </x-ui.field>
        </div>
    </div>
    <div class="card p-5 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div><p class="font-medium">Pratinjau frontend</p><p class="mt-1 text-sm text-muted-foreground">Formulir ini tidak mengirim atau menyimpan data ke server.</p></div>
            <div class="flex flex-wrap gap-2"><a href="{{ route('examples.records.index') }}" class="btn btn-outline">Batal</a><button type="submit" class="btn btn-primary">{{ $editing ? 'Simpan perubahan' : 'Buat entri' }}</button></div>
        </div>
    </div>
</form>
