const payButton = document.querySelector('[data-snap-pay]');

if (payButton) {
    const showError = message => {
        window.App.toast({ title: 'Pembayaran belum diproses', description: message, tone: 'danger', duration: 6500 });
    };
    const refresh = async () => {
        try {
            const response = await fetch(payButton.dataset.checkUrl, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            });
            if (!response.ok) throw new Error('Status pembayaran belum dapat diperiksa.');
            window.location.reload();
        } catch (error) {
            showError(error.message);
        }
    };

    payButton.addEventListener('click', () => {
        if (!window.snap?.pay) {
            showError('Snap Midtrans belum tersedia. Periksa client key dan koneksi internet.');
            return;
        }
        window.snap.pay(payButton.dataset.snapToken, {
            onSuccess: refresh,
            onPending: refresh,
            onError: refresh,
            language: 'id',
        });
    });
}
