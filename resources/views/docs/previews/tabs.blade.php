<x-ui.tabs id="preview-tabs" variant="pill" :tabs="['summary'=>'Ringkasan','history'=>'Riwayat']">
    <x-slot:tab_summary><p>Ringkasan pesanan terbaru.</p></x-slot:tab_summary>
    <x-slot:tab_history><p>Pesanan dibuat, dibayar, dan dikirim.</p></x-slot:tab_history>
</x-ui.tabs>
<x-ui.tabs id="preview-underline" variant="underline" :tabs="['summary'=>'Ringkasan','history'=>'Riwayat']">
    <x-slot:tab_summary><p>Tab underline.</p></x-slot:tab_summary>
    <x-slot:tab_history><p>Riwayat pesanan.</p></x-slot:tab_history>
</x-ui.tabs>
<x-ui.tabs id="preview-soft" variant="soft" :tabs="['summary'=>'Semua','history'=>'Aktif']">
    <x-slot:tab_summary><p>Tab soft.</p></x-slot:tab_summary>
    <x-slot:tab_history><p>Entri aktif.</p></x-slot:tab_history>
</x-ui.tabs>
