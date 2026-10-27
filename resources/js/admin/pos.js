import { numberValue, setNumberValue } from './grouped-numbers';
import { cameraErrorMessage, refreshVideoDeviceOptions, scanBarcodeVideo } from './barcode-scanner';

const root = document.querySelector('[data-pos-cart]');

if (root) {
    const storageKey = root.dataset.storageKey;
    const rows = root.querySelector('[data-cart-rows]');
    const inputs = root.querySelector('[data-cart-inputs]');
    const form = root.querySelector('[data-cart-form]');
    const discount = root.querySelector('[data-cart-discount]');
    const paid = root.querySelector('[data-cart-paid]');
    const modal = root.querySelector('#cashier-payment');
    const methods = [...root.querySelectorAll('[data-cart-method]')];
    const money = value => `Rp ${Math.max(0, value).toLocaleString('id-ID')}`;
    const selectedMethod = () => methods.find(input => input.checked)?.value || 'cash';
    let currentGrand = 0;
    let cart = [];
    try { cart = JSON.parse(localStorage.getItem(storageKey) || root.dataset.initialCart || '[]'); } catch { cart = []; }
    if (!Array.isArray(cart)) cart = [];

    function refreshTotals() {
        const subtotal = cart.reduce((sum, item) => sum + Number(item.price) * Number(item.qty), 0);
        const reduction = Math.min(subtotal, numberValue(discount));
        const tax = Math.round((subtotal - reduction) * Number(root.dataset.taxRate) / 100);
        const grand = subtotal - reduction + tax;
        currentGrand = grand;
        root.querySelector('[data-cart-subtotal]').textContent = money(subtotal);
        root.querySelector('[data-cart-discount-total]').textContent = money(reduction);
        root.querySelector('[data-cart-tax]').textContent = money(tax);
        root.querySelector('[data-cart-total]').textContent = money(grand);
        root.querySelector('[data-cart-modal-total]').textContent = money(grand);
        if (selectedMethod() !== 'cash' && selectedMethod() !== 'midtrans' && selectedMethod() !== 'credit') {
            paid.value = grand.toLocaleString('id-ID');
            paid.groupedRawInput.value = String(grand);
        }
        root.querySelector('[data-cart-change]').textContent = money(selectedMethod() === 'cash' ? numberValue(paid) - grand : 0);
        root.querySelector('[data-cart-pay-open]').disabled = cart.length === 0;
        root.querySelector('[data-cart-submit]').disabled = cart.length === 0;
        root.querySelector('[data-cart-draft]').disabled = cart.length === 0;
    }

    function icon(name) {
        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.classList.add('h-4', 'w-4'); svg.setAttribute('aria-hidden', 'true');
        const use = document.createElementNS('http://www.w3.org/2000/svg', 'use');
        use.setAttribute('href', `/icons.svg#i-${name}`); svg.append(use);
        return svg;
    }

    function render() {
        rows.replaceChildren();
        inputs.replaceChildren();
        cart.forEach((item, index) => {
            const qty = Number(item.qty) || 1;
            const row = document.createElement('div'); row.className = 'rounded-lg border p-3';
            const header = document.createElement('div'); header.className = 'flex items-start justify-between gap-3';
            const product = document.createElement('div'); product.className = 'flex min-w-0 items-center gap-3';
            if (item.image) {
                const image = document.createElement('img'); image.src = item.image; image.alt = '';
                image.className = 'h-10 w-10 shrink-0 rounded-md object-cover';
                product.append(image);
            } else {
                const fallback = document.createElement('span'); fallback.className = 'avatar avatar-soft avatar-square shrink-0';
                fallback.append(icon('package')); product.append(fallback);
            }
            const productName = document.createElement('span'); productName.className = 'min-w-0 break-words font-medium'; productName.textContent = item.name;
            product.append(productName);
            const details = document.createElement('div'); details.className = 'mt-3 flex flex-wrap items-center justify-between gap-3';
            let qtyField;
            const controls = document.createElement('div'); controls.className = 'flex items-center gap-1';
            const decrease = document.createElement('button'); decrease.type = 'button';
            decrease.className = 'btn btn-outline btn-icon btn-sm'; decrease.setAttribute('aria-label', `Kurangi jumlah ${item.name}`);
            decrease.disabled = qty <= 1; decrease.append(icon('minus'));
            const input = document.createElement('input');
            input.type = 'text'; input.inputMode = 'numeric'; input.value = qty.toLocaleString('id-ID'); input.placeholder = 'Jumlah';
            input.className = 'input w-16 text-center'; input.setAttribute('aria-label', `Jumlah ${item.name}`);
            input.addEventListener('input', () => { const raw = input.value.replace(/\D/g, ''); input.value = raw ? Number(raw).toLocaleString('id-ID') : ''; });
            const increase = document.createElement('button'); increase.type = 'button';
            increase.className = 'btn btn-outline btn-icon btn-sm'; increase.setAttribute('aria-label', `Tambah jumlah ${item.name}`);
            increase.disabled = qty >= Number(item.stock); increase.append(icon('plus'));
            controls.append(decrease, input, increase);
            const subtotal = document.createElement('div'); subtotal.className = 'text-right';
            const subtotalLabel = document.createElement('span'); subtotalLabel.className = 'block text-xs text-muted-foreground'; subtotalLabel.textContent = 'Subtotal';
            const total = document.createElement('strong'); total.className = 'tabular-nums'; total.textContent = money(Number(item.price) * qty);
            subtotal.append(subtotalLabel, total);
            const setQty = value => {
                item.qty = Math.max(1, Math.min(Number(item.stock), value));
                input.value = Number(item.qty).toLocaleString('id-ID');
                decrease.disabled = item.qty <= 1;
                increase.disabled = item.qty >= Number(item.stock);
                total.textContent = money(Number(item.price) * item.qty);
                qtyField.value = String(item.qty);
                persist(false);
            };
            decrease.addEventListener('click', () => setQty(Number(item.qty) - 1));
            increase.addEventListener('click', () => setQty(Number(item.qty) + 1));
            input.addEventListener('change', () => setQty(numberValue(input) || 1));
            const remove = document.createElement('button'); remove.type = 'button'; remove.className = 'btn btn-destructive btn-sm';
            remove.append(icon('trash'), 'Hapus');
            remove.addEventListener('click', () => { cart.splice(index, 1); persist(); });
            header.append(product, remove);
            details.append(controls, subtotal);
            row.append(header, details); rows.append(row);
            for (const [key, value] of [['product_id', item.id], ['qty', qty]]) {
                const hidden = document.createElement('input'); hidden.type = 'hidden'; hidden.name = `items[${index}][${key}]`; hidden.value = String(value); inputs.append(hidden);
                if (key === 'qty') qtyField = hidden;
            }
        });
        root.querySelector('[data-cart-empty]').hidden = cart.length > 0;
        refreshTotals();
    }

    function persist(redraw = true) { localStorage.setItem(storageKey, JSON.stringify(cart)); if (redraw) render(); else refreshTotals(); }
    function addProduct(product) {
        if (Number(product.stock) < 1) {
            window.App.toast({ title: 'Stok habis', description: `${product.name} tidak dapat ditambahkan.`, tone: 'warning' });
            return false;
        }
        const found = cart.find(item => Number(item.id) === Number(product.id));
        if (found) {
            if (Number(found.qty) >= Number(product.stock)) {
                window.App.toast({ title: 'Stok tidak mencukupi', description: `${product.name} sudah mencapai stok tersedia.`, tone: 'warning' });
                return false;
            }
            found.qty = Number(found.qty) + 1;
            Object.assign(found, { price: Number(product.price), stock: Number(product.stock), image: product.image });
        } else {
            cart.push({ id: Number(product.id), name: product.name, price: Number(product.price), stock: Number(product.stock), image: product.image, qty: 1 });
        }
        persist();
        return true;
    }
    function showError(message) {
        root.querySelector('[data-cart-error-message]').textContent = message;
        root.querySelector('[data-cart-error]').hidden = false;
    }
    function syncPayment() {
        const online = selectedMethod() === 'midtrans' || selectedMethod() === 'credit';
        const cash = selectedMethod() === 'cash';
        root.querySelector('[data-cart-credit-group]').hidden = selectedMethod() !== 'credit';
        paid.disabled = online;
        paid.groupedRawInput.disabled = online;
        paid.required = !online;
        paid.readOnly = !cash && !online;
        root.querySelector('[data-cart-paid-group]').hidden = online;
        root.querySelector('[data-cart-presets]').hidden = !cash;
        render();
    }

    document.addEventListener('click', event => {
        const button = event.target.closest('[data-cart-add]');
        if (!button) return;
        addProduct(button.dataset);
    });

    const scanner = document.querySelector('[data-pos-barcode]');
    const barcodeInput = scanner.querySelector('[data-barcode-input]');
    const cameraDialog = document.querySelector('#barcode-camera');
    const cameraSource = cameraDialog.querySelector('[data-barcode-camera-source]');
    let lookupQueue = Promise.resolve();
    let cameraControls = null;
    let cameraRun = 0;
    let scanHintTimer = null;

    async function lookupBarcode(code) {
        const barcode = code.trim();
        if (!barcode) {
            window.App.toast({ title: 'Barcode kosong', description: 'Scan atau ketik barcode produk.', tone: 'warning' });
            barcodeInput.focus();
            return;
        }
        try {
            const url = new URL(scanner.dataset.lookupUrl, window.location.origin);
            url.searchParams.set('barcode', barcode);
            const response = await fetch(url, { headers: { Accept: 'application/json' }, cache: 'no-store' });
            const product = await response.json();
            if (!response.ok) throw new Error(product.message || 'Produk tidak dapat ditemukan.');
            if (addProduct(product)) {
                window.App.toast({ title: 'Produk ditambahkan', description: product.name, tone: 'success', duration: 2500 });
            }
            barcodeInput.value = '';
            barcodeInput.focus();
        } catch (error) {
            window.App.toast({ title: 'Scan gagal', description: error.message || 'Periksa barcode dan coba lagi.', tone: 'danger' });
            barcodeInput.select();
        }
    }

    function enqueueBarcode(code) {
        lookupQueue = lookupQueue.then(() => lookupBarcode(code));
    }

    barcodeInput.addEventListener('keydown', event => {
        if (event.key !== 'Enter') return;
        event.preventDefault();
        enqueueBarcode(barcodeInput.value);
    });
    scanner.querySelector('[data-barcode-add]').addEventListener('click', () => enqueueBarcode(barcodeInput.value));
    function stopCamera() {
        cameraRun++;
        cameraAbort?.abort();
        cameraAbort = null;
        clearTimeout(scanHintTimer);
        clearTimeout(waitingHintTimer);
        cameraControls?.stop();
        cameraControls = null;
        const video = cameraDialog.querySelector('[data-barcode-video]');
        video.srcObject?.getTracks().forEach(track => track.stop());
        video.srcObject = null;
    }
    let cameraAbort = null;
    let waitingHintTimer = null;
    async function startCamera() {
        if (!window.isSecureContext) {
            cameraDialog.querySelector('[data-camera-status]').textContent = 'Scan otomatis memerlukan HTTPS atau localhost. Browser tidak mengizinkan kamera live pada HTTP biasa.';
            return;
        }
        if (!navigator.mediaDevices?.getUserMedia) {
            cameraDialog.querySelector('[data-camera-status]').textContent = 'Browser atau perangkat ini tidak menyediakan akses kamera live.';
            return;
        }
        stopCamera();
        const run = cameraRun;
        cameraAbort = new AbortController();
        const status = cameraDialog.querySelector('[data-camera-status]');
        const video = cameraDialog.querySelector('[data-barcode-video]');
        status.textContent = 'Menunggu izin kamera dari Chrome. Pilih Izinkan pada popup browser.';
        waitingHintTimer = setTimeout(() => {
            if (run === cameraRun) status.textContent = 'Masih menunggu kamera. Jika memakai Iriun atau OBS, pastikan video sudah tampil di aplikasinya dan sumber kamera yang benar dipilih.';
        }, 6000);
        void refreshVideoDeviceOptions(cameraSource).catch(() => {});
        try {
            if (run !== cameraRun || !cameraDialog.open) return;
            const controls = await scanBarcodeVideo(video, code => {
                if (run !== cameraRun) return;
                stopCamera();
                cameraDialog.close();
                barcodeInput.value = code;
                enqueueBarcode(code);
            }, { deviceId: cameraSource.value, signal: cameraAbort.signal });
            if (run !== cameraRun || !cameraDialog.open) controls.stop();
            else {
                clearTimeout(waitingHintTimer);
                cameraControls = controls;
                status.textContent = 'Kamera aktif, mencari barcode... Produk diproses otomatis saat terbaca.';
                void refreshVideoDeviceOptions(cameraSource).catch(() => {});
                scanHintTimer = setTimeout(() => {
                    if (run === cameraRun) status.textContent = 'Barcode belum terbaca. Jauhkan sedikit produk sampai garisnya tajam, pastikan seluruh barcode dan tepi putih terlihat, lalu tahan beberapa detik.';
                }, 6000);
            }
        } catch (error) {
            if (run !== cameraRun) return;
            stopCamera();
            status.textContent = cameraErrorMessage(error);
            void refreshVideoDeviceOptions(cameraSource).catch(() => {});
            window.App.toast({ title: 'Kamera gagal dibuka', description: status.textContent, tone: 'danger' });
        }
    }
    scanner.querySelector('[data-barcode-camera]').addEventListener('click', () => setTimeout(startCamera, 0));
    cameraDialog.querySelector('[data-barcode-camera-retry]').addEventListener('click', startCamera);
    cameraSource.addEventListener('change', () => { if (cameraDialog.open) void startCamera(); });
    navigator.mediaDevices?.addEventListener?.('devicechange', () => { if (cameraDialog.open) void refreshVideoDeviceOptions(cameraSource).catch(() => {}); });
    cameraDialog.addEventListener('cancel', stopCamera);
    cameraDialog.addEventListener('click', event => {
        if (event.target === cameraDialog || event.target.closest('[data-modal-close]')) stopCamera();
    });
    cameraDialog.addEventListener('close', stopCamera);
    root.querySelectorAll('[data-pay-preset]').forEach(button => button.addEventListener('click', () => setNumberValue(paid, button.dataset.payPreset)));
    root.querySelector('[data-cart-pay-open]').addEventListener('click', () => {
        if (selectedMethod() === 'cash' && !numberValue(paid)) setNumberValue(paid, currentGrand);
    });
    discount.addEventListener('input', refreshTotals);
    paid.addEventListener('input', refreshTotals);
    methods.forEach(input => input.addEventListener('change', syncPayment));

    form.addEventListener('submit', async event => {
        if (cart.length === 0) { event.preventDefault(); return; }
        if (event.submitter?.hasAttribute('data-cart-draft')) return;
        event.preventDefault();
        root.querySelector('[data-cart-error]').hidden = true;
        const submit = root.querySelector('[data-cart-submit]');
        submit.disabled = true;
        let checkoutUrl = null;
        try {
            if (selectedMethod() === 'midtrans' && !window.snap?.pay) {
                throw new Error('Snap Midtrans belum tersedia. Periksa client key dan koneksi internet.');
            }
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: new FormData(form),
            });
            const result = await response.json();
            if (!response.ok) throw new Error(Object.values(result.errors || {})[0]?.[0] || result.message || 'Checkout gagal diproses.');
            checkoutUrl = result.transaction_url;
            try { localStorage.removeItem(storageKey); } catch {}
            if (!result.snap_token) { window.location.assign(result.transaction_url); return; }
            modal.close();
            const showResult = async () => {
                try {
                    await fetch(result.check_url, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    });
                } finally {
                    window.location.assign(result.transaction_url);
                }
            };
            window.snap.pay(result.snap_token, {
                onSuccess: showResult,
                onPending: showResult,
                onError: showResult,
                onClose: () => window.location.assign(result.transaction_url),
                language: 'id',
            });
        } catch (error) {
            if (checkoutUrl) { window.location.assign(checkoutUrl); return; }
            showError(error.message || 'Checkout gagal diproses.');
        } finally {
            submit.disabled = false;
        }
    });

    syncPayment();
    barcodeInput.focus({ preventScroll: true });
}
