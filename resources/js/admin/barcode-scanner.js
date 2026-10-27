import { BarcodeFormat, DecodeHintType } from '@zxing/library';

async function barcodeReader() {
    const { BrowserMultiFormatOneDReader } = await import('@zxing/browser');
    const hints = new Map([
        [DecodeHintType.POSSIBLE_FORMATS, [
            BarcodeFormat.EAN_13,
            BarcodeFormat.UPC_A,
            BarcodeFormat.EAN_8,
            BarcodeFormat.UPC_E,
            BarcodeFormat.CODE_128,
            BarcodeFormat.CODE_39,
            BarcodeFormat.ITF,
        ]],
    ]);
    return new BrowserMultiFormatOneDReader(hints, { delayBetweenScanAttempts: 100 });
}

export async function listVideoDevices() {
    if (!navigator.mediaDevices?.enumerateDevices) return [];
    return (await navigator.mediaDevices.enumerateDevices())
        .filter(device => device.kind === 'videoinput' && device.deviceId);
}

export async function refreshVideoDeviceOptions(select) {
    const devices = await listVideoDevices();
    const selected = select.value;
    select.replaceChildren(new Option('Kamera otomatis', ''));
    devices.forEach((device, index) => {
        select.add(new Option(device.label || `Kamera ${index + 1}`, device.deviceId));
    });
    if (selected && devices.some(device => device.deviceId === selected)) select.value = selected;
    return devices;
}

export function cameraErrorMessage(error) {
    if (error?.name === 'NotAllowedError' || error?.name === 'PermissionDeniedError') {
        return 'Akses kamera belum diizinkan. Pilih Izinkan pada popup Chrome atau ubah izin kamera untuk situs ini.';
    }
    if (error?.name === 'NotFoundError' || error?.name === 'OverconstrainedError') {
        return 'Sumber kamera tidak ditemukan. Pilih kamera lain, lalu coba lagi.';
    }
    if (error?.name === 'NotReadableError' || error?.name === 'AbortError') {
        return 'Kamera ditemukan tetapi tidak bisa mengirim video. Periksa koneksi Iriun atau aktifkan OBS Virtual Camera, lalu coba sumber lain.';
    }
    if (error?.name === 'CameraTimeoutError') {
        return 'Kamera terlalu lama merespons. Periksa popup izin Chrome, sambungan Iriun, atau OBS Virtual Camera; pilih sumber lain lalu coba lagi.';
    }
    if (error?.message?.startsWith('Aliran video kamera')) return error.message;
    return 'Kamera tidak dapat dibuka. Periksa izin browser dan sumber kamera, lalu coba lagi.';
}

function withCameraTimeout(promise, milliseconds, onLateResult, signal, onAbort = () => {}) {
    let timer;
    let cancelled = false;
    let removeAbort = () => {};
    promise.then(result => {
        if (cancelled) onLateResult(result);
    }).catch(() => {});
    const timeout = new Promise((_, reject) => {
        timer = setTimeout(() => {
            cancelled = true;
            onAbort();
            reject(Object.assign(new Error('Kamera terlalu lama merespons.'), { name: 'CameraTimeoutError' }));
        }, milliseconds);
    });
    const aborted = new Promise((_, reject) => {
        const listener = () => {
            cancelled = true;
            onAbort();
            reject(Object.assign(new Error('Pemindaian dibatalkan.'), { name: 'AbortError' }));
        };
        signal?.addEventListener('abort', listener, { once: true });
        removeAbort = () => signal?.removeEventListener('abort', listener);
        if (signal?.aborted) listener();
    });
    return Promise.race([promise, timeout, aborted]).finally(() => {
        clearTimeout(timer);
        removeAbort();
    });
}

export async function scanBarcodeVideo(video, onResult, { deviceId = '', signal } = {}) {
    const reader = await barcodeReader();
    let stopped = false;
    let scanTimer = null;
    const found = code => {
        if (stopped || !code) return;
        stopped = true;
        onResult(code);
    };
    const constraints = { video: deviceId ? { deviceId: { exact: deviceId } } : { facingMode: { ideal: 'environment' } }, audio: false };
    const capture = navigator.mediaDevices.getUserMedia(constraints);
    const stream = await withCameraTimeout(capture, 20000, lateStream => lateStream.getTracks().forEach(track => track.stop()), signal);
    let controls;
    try {
        controls = await withCameraTimeout(
            reader.decodeFromStream(stream, video, result => { if (result) found(result.getText()); }),
            12000,
            lateControls => lateControls.stop(),
            signal,
            () => stream.getTracks().forEach(track => track.stop()),
        );
    } catch (error) {
        stream.getTracks().forEach(track => track.stop());
        video.srcObject = null;
        throw error;
    }
    if (!stopped && !video.srcObject?.getVideoTracks().some(track => track.readyState === 'live')) {
        controls.stop();
        throw new Error('Aliran video kamera berhenti sebelum pemindaian dimulai.');
    }

    // Where available, the browser's native decoder can recognize a frame that ZXing misses.
    const NativeDetector = window.BarcodeDetector;
    if (NativeDetector?.getSupportedFormats && !stopped) {
        void NativeDetector.getSupportedFormats().then(supported => {
            if (stopped) return;
            const formats = ['ean_13', 'upc_a', 'ean_8', 'upc_e', 'code_128', 'code_39', 'itf']
                .filter(format => supported.includes(format));
            if (!formats.length) return;
            const detector = new NativeDetector({ formats });
            const scan = async () => {
                if (stopped) return;
                if (video.readyState >= 2) {
                    try {
                        const codes = await detector.detect(video);
                        if (codes.length) found(codes[0].rawValue);
                    } catch { /* Keep ZXing scanning if the native detector fails. */ }
                }
                if (!stopped) scanTimer = setTimeout(scan, 150);
            };
            scanTimer = setTimeout(scan, 0);
        }).catch(() => {});
    }

    return {
        ...controls,
        stop() {
            stopped = true;
            clearTimeout(scanTimer);
            controls.stop();
        },
    };
}
