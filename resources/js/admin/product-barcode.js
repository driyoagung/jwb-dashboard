import { cameraErrorMessage, refreshVideoDeviceOptions, scanBarcodeVideo } from './barcode-scanner';

const barcodeInput = document.querySelector('[data-product-barcode]');

if (barcodeInput) {
    const dialog = document.querySelector('#product-barcode-camera');
    const video = dialog.querySelector('[data-product-camera-video]');
    const status = dialog.querySelector('[data-product-camera-status]');
    const source = dialog.querySelector('[data-product-camera-source]');
    let controls = null;
    let cameraAbort = null;
    let run = 0;
    let scanHintTimer = null;
    let waitingHintTimer = null;

    barcodeInput.addEventListener('keydown', event => {
        if (event.key === 'Enter') event.preventDefault();
    });

    function stopCamera() {
        run++;
        cameraAbort?.abort();
        cameraAbort = null;
        clearTimeout(scanHintTimer);
        clearTimeout(waitingHintTimer);
        controls?.stop();
        controls = null;
        video.srcObject?.getTracks().forEach(track => track.stop());
        video.srcObject = null;
    }

    function fillBarcode(code) {
        barcodeInput.value = code;
        barcodeInput.dispatchEvent(new Event('input', { bubbles: true }));
        dialog.close();
        barcodeInput.focus({ preventScroll: true });
        window.App.toast({ title: 'Barcode terbaca', description: code, tone: 'success' });
    }

    async function startCamera() {
        if (!window.isSecureContext) {
            status.textContent = 'Scan otomatis memerlukan HTTPS atau localhost. Browser tidak mengizinkan kamera live pada HTTP biasa.';
            return;
        }
        if (!navigator.mediaDevices?.getUserMedia) {
            status.textContent = 'Browser atau perangkat ini tidak menyediakan akses kamera live.';
            return;
        }
        stopCamera();
        const currentRun = run;
        cameraAbort = new AbortController();
        status.textContent = 'Menunggu izin kamera dari Chrome. Pilih Izinkan pada popup browser.';
        waitingHintTimer = setTimeout(() => {
            if (currentRun === run) status.textContent = 'Masih menunggu kamera. Jika memakai Iriun atau OBS, pastikan video sudah tampil di aplikasinya dan sumber kamera yang benar dipilih.';
        }, 6000);
        void refreshVideoDeviceOptions(source).catch(() => {});
        try {
            const scannerControls = await scanBarcodeVideo(video, code => {
                if (currentRun !== run) return;
                stopCamera();
                fillBarcode(code);
            }, { deviceId: source.value, signal: cameraAbort.signal });
            if (currentRun !== run || !dialog.open) scannerControls.stop();
            else {
                clearTimeout(waitingHintTimer);
                controls = scannerControls;
                status.textContent = 'Kamera aktif, mencari barcode... Kode akan terisi otomatis saat terbaca.';
                void refreshVideoDeviceOptions(source).catch(() => {});
                scanHintTimer = setTimeout(() => {
                    if (currentRun === run) status.textContent = 'Barcode belum terbaca. Jauhkan sedikit produk sampai garisnya tajam, pastikan seluruh barcode dan tepi putih terlihat, lalu tahan beberapa detik.';
                }, 6000);
            }
        } catch (error) {
            if (currentRun !== run) return;
            stopCamera();
            status.textContent = cameraErrorMessage(error);
            void refreshVideoDeviceOptions(source).catch(() => {});
        }
    }

    dialog.addEventListener('cancel', stopCamera);
    dialog.addEventListener('click', event => {
        if (event.target === dialog || event.target.closest('[data-modal-close]')) stopCamera();
    });
    dialog.addEventListener('close', stopCamera);
    document.querySelector('[data-product-camera-open]').addEventListener('click', () => setTimeout(startCamera, 0));
    dialog.querySelector('[data-product-camera-retry]').addEventListener('click', startCamera);
    source.addEventListener('change', () => { if (dialog.open) void startCamera(); });
    navigator.mediaDevices?.addEventListener?.('devicechange', () => { if (dialog.open) void refreshVideoDeviceOptions(source).catch(() => {}); });
}
