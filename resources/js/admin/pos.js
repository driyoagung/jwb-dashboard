import { numberValue, setNumberValue } from './grouped-numbers';
import { cameraErrorMessage, refreshVideoDeviceOptions, scanBarcodeVideo } from './barcode-scanner';
import { clearCartKeys } from './cart-storage';

const root = document.querySelector('[data-pos-cart]');

if (root) {
    const storageKey = root.dataset.storageKey;
    const panel = root.closest('.card');
    const rows = root.querySelector('[data-cart-rows]');
    const inputs = root.querySelector('[data-cart-inputs]');
    const form = root.querySelector('[data-cart-form]');
    const discount = root.querySelector('[data-cart-discount]');
    const paid = root.querySelector('[data-cart-paid]');
    const modal = root.querySelector('#cashier-payment');
    const manualModal = root.querySelector('#manual-payment-confirmation');
    const manualConfirmedInput = root.querySelector('[data-manual-confirmed]');
    const methods = [...root.querySelectorAll('[data-cart-method]')];
    const quoteInput = root.querySelector('[data-cart-quote-hash]');
    const syncStatus = panel.querySelector('[data-cart-sync-status]');
    const money = value => `Rp ${Math.max(0, value).toLocaleString('id-ID')}`;
    const selectedMethod = () => methods.find(input => input.checked)?.value || 'cash';
    let currentGrand = 0;
    let manualConfirmed = false;
    let checkoutBusy = false;
    let syncing = false;
    let quoteSequence = 0;
    let cartRevision = 0;
    let cart = [];
    try {
        const saved = JSON.parse(localStorage.getItem(storageKey) || 'null');
        cart = Array.isArray(saved) ? saved : saved?.items || JSON.parse(root.dataset.initialCart || '[]');
        if (saved && !Array.isArray(saved) && saved.discount_amount !== undefined) {
            setNumberValue(discount, saved.discount_amount);
        }
    } catch { cart = JSON.parse(root.dataset.initialCart || '[]'); }
    if (!Array.isArray(cart)) cart = [];

    let fitFrame;
    function fitCartPanel() {
        cancelAnimationFrame(fitFrame);
        fitFrame = requestAnimationFrame(() => {
            const viewportHeight = window.visualViewport?.height || window.innerHeight;
            const top = window.innerWidth >= 1024 ? Math.max(0, panel.getBoundingClientRect().top) : 80;
            const available = Math.max(200, viewportHeight - top - 16);
            const headerHeight = panel.querySelector('.card-header').offsetHeight;
            panel.style.maxHeight = `${available}px`;
            form.style.maxHeight = `${Math.max(96, Math.min(viewportHeight * 0.65, available - headerHeight - 88))}px`;
        });
    }
    window.addEventListener('scroll', fitCartPanel, { passive: true });
    window.addEventListener('resize', fitCartPanel);
    window.visualViewport?.addEventListener('resize', fitCartPanel);
    new ResizeObserver(fitCartPanel).observe(panel.querySelector('.card-header'));

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
        const shortfall = selectedMethod() === 'cash' ? Math.max(0, grand - numberValue(paid)) : 0;
        root.querySelector('[data-cart-change-label]').textContent = shortfall > 0 ? 'Kurang bayar' : 'Kembalian';
        const change = root.querySelector('[data-cart-change]');
        change.textContent = money(shortfall > 0 ? shortfall : selectedMethod() === 'cash' ? numberValue(paid) - grand : 0);
        change.classList.toggle('text-danger', shortfall > 0);
        root.querySelector('[data-cart-shortfall]').hidden = shortfall === 0;
        root.querySelector('[data-cart-shortfall-message]').textContent = `Pembayaran masih kurang ${money(shortfall)}. Tambahkan nominal sebelum memproses transaksi.`;
        panel.querySelector('[data-cart-count]').textContent = `${cart.length} jenis produk · ${cart.reduce((sum, item) => sum + Number(item.qty), 0).toLocaleString('id-ID')} barang`;
        root.querySelector('[data-cart-pay-open]').disabled = cart.length === 0 || checkoutBusy;
        root.querySelector('[data-cart-submit]').disabled = cart.length === 0 || shortfall > 0 || syncing || checkoutBusy;
        root.querySelector('[data-cart-submit-label]').textContent = syncing ? 'Memeriksa...' : checkoutBusy ? 'Memproses...' : 'Proses pembayaran';
        root.querySelector('[data-cart-draft]').disabled = cart.length === 0 || checkoutBusy;
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
            const productInfo = document.createElement('div'); productInfo.className = 'min-w-0';
            const stock = document.createElement('span'); stock.className = 'block text-xs text-muted-foreground';
            stock.textContent = `Stok tersedia: ${Number(item.stock).toLocaleString('id-ID')}`;
            productInfo.append(productName, stock); product.append(productInfo);
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
            increase.disabled = qty >= Math.min(1000, Number(item.stock)); increase.append(icon('plus'));
            controls.append(decrease, input, increase);
            const subtotal = document.createElement('div'); subtotal.className = 'text-right';
            const subtotalLabel = document.createElement('span'); subtotalLabel.className = 'block text-xs text-muted-foreground'; subtotalLabel.textContent = 'Subtotal';
            const total = document.createElement('strong'); total.className = 'tabular-nums'; total.textContent = money(Number(item.price) * qty);
            subtotal.append(subtotalLabel, total);
            const setQty = value => {
                item.qty = Math.max(1, Math.min(1000, Number(item.stock), value));
                input.value = Number(item.qty).toLocaleString('id-ID');
                decrease.disabled = item.qty <= 1;
                increase.disabled = item.qty >= Math.min(1000, Number(item.stock));
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
        fitCartPanel();
    }

    function saveState() {
        try { localStorage.setItem(storageKey, JSON.stringify({ items: cart, discount_amount: numberValue(discount) })); } catch {}
    }
    function persist(redraw = true) {
        cartRevision++;
        quoteInput.disabled = true;
        quoteInput.value = '';
        manualConfirmed = false;
        manualConfirmedInput.disabled = true;
        saveState();
        if (redraw) render(); else refreshTotals();
    }
    function notice(message) {
        syncStatus.textContent = message;
    }
    async function syncCart() {
        if (!cart.length) return { can_checkout: false, changed: false, issues: ['Keranjang kosong.'] };
        const sequence = ++quoteSequence;
        const revision = cartRevision;
        syncing = true;
        syncStatus.textContent = 'Memeriksa harga dan stok...';
        refreshTotals();
        try {
            const response = await fetch(root.dataset.quoteUrl, {
                method: 'POST',
                headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ items: cart.map(item => ({ product_id: item.id, qty: item.qty })), discount_amount: numberValue(discount) }),
            });
            const quote = await response.json();
            if (!response.ok) throw new Error(Object.values(quote.errors || {})[0]?.[0] || 'Harga dan stok belum dapat diperiksa. Coba lagi.');
            if (sequence !== quoteSequence || revision !== cartRevision) throw new Error('Keranjang berubah saat diperiksa. Periksa kembali sebelum membayar.');
            let changed = Number(root.dataset.taxRate) !== Number(quote.tax_rate);
            for (const product of quote.items) {
                const item = cart.find(entry => Number(entry.id) === Number(product.id));
                if (!item) continue;
                if (product.price !== null) {
                    changed ||= Number(item.price) !== Number(product.price);
                    item.price = product.price;
                }
                item.stock = product.stock;
                item.name = product.name || item.name;
                item.image = product.image;
            }
            root.dataset.taxRate = quote.tax_rate;
            quoteInput.value = quote.quote_hash;
            quoteInput.disabled = false;
            saveState();
            render();
            notice(quote.issues.length ? 'Keranjang perlu disesuaikan. Buka pembayaran untuk melihat rinciannya.' : changed ? 'Harga atau pajak diperbarui. Periksa total terbaru sebelum membayar.' : 'Harga dan stok sudah diperiksa.');
            return { ...quote, changed };
        } catch (error) {
            if (sequence === quoteSequence) syncStatus.textContent = 'Pemeriksaan gagal. Coba lagi melalui pembayaran.';
            throw error;
        } finally {
            if (sequence === quoteSequence) { syncing = false; refreshTotals(); }
        }
    }
    function addProduct(product) {
        if (Number(product.stock) < 1) {
            window.App.toast({ title: 'Stok habis', description: `${product.name} tidak dapat ditambahkan.`, tone: 'warning' });
            return false;
        }
        const found = cart.find(item => Number(item.id) === Number(product.id));
        if (found) {
            if (Number(found.qty) >= Math.min(1000, Number(product.stock))) {
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
        manualConfirmed = false;
        manualConfirmedInput.disabled = true;
        root.querySelector('[data-cart-error-message]').textContent = message;
        root.querySelector('[data-cart-error]').hidden = false;
        if (!modal.open && !manualModal.open) modal.showModal();
    }
    function syncPayment() {
        manualConfirmed = false;
        manualConfirmedInput.disabled = true;
        const online = selectedMethod() === 'midtrans' || selectedMethod() === 'credit';
        const cash = selectedMethod() === 'cash';
        const creditGroup = root.querySelector('[data-cart-credit-group]');
        if (creditGroup) creditGroup.hidden = selectedMethod() !== 'credit';
        paid.disabled = online;
        paid.groupedRawInput.disabled = online;
        paid.required = !online;
        paid.readOnly = !cash && !online;
        root.querySelector('[data-cart-paid-group]').hidden = online;
        root.querySelector('[data-cart-presets]').hidden = !cash;
        render();
    }

    async function openManualPayment(method, reviewedQuote = null) {
        manualConfirmed = false;
        manualConfirmedInput.disabled = true;
        try {
            const quote = reviewedQuote || await syncCart();
            if (!quote.can_checkout) { showError(quote.issues.join(' ')); return; }
            if (selectedMethod() !== method || !modal.open) return;
            root.querySelector('[data-manual-total]').textContent = money(currentGrand);
            root.querySelector('[data-manual-transfer]').hidden = method !== 'transfer';
            root.querySelector('[data-manual-qris]').hidden = method !== 'qris';
            modal.close();
            manualModal.showModal();
        } catch (error) { showError(error.message); }
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

    async function lookupBarcode(code, fallbackToSearch = true) {
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
            if (response.status === 404 && fallbackToSearch) {
                barcodeInput.closest('form').requestSubmit();
                return;
            }
            if (!response.ok) throw new Error(product.message || 'Produk tidak dapat ditemukan.');
            if (addProduct(product)) {
                window.App.toast({ title: 'Produk ditambahkan', description: product.name, tone: 'success', duration: 2500 });
            }
            barcodeInput.value = '';
            barcodeInput.dispatchEvent(new Event('input', { bubbles: true }));
            barcodeInput.focus();
        } catch (error) {
            window.App.toast({ title: 'Scan gagal', description: error.message || 'Periksa barcode dan coba lagi.', tone: 'danger' });
            barcodeInput.select();
        }
    }

    function enqueueBarcode(code, fallbackToSearch = true) {
        lookupQueue = lookupQueue.then(() => lookupBarcode(code, fallbackToSearch));
    }

    barcodeInput.addEventListener('keydown', event => {
        if (event.key !== 'Enter') return;
        event.preventDefault();
        enqueueBarcode(barcodeInput.value);
    });
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
                enqueueBarcode(code, false);
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
    root.querySelector('[data-cart-pay-open]').addEventListener('click', async () => {
        root.querySelector('[data-cart-error]').hidden = true;
        try {
            const quote = await syncCart();
            if (!quote.can_checkout) showError(quote.issues.join(' '));
            if (selectedMethod() === 'cash' && !numberValue(paid)) setNumberValue(paid, currentGrand);
        } catch (error) { showError(error.message); }
    });
    discount.addEventListener('input', () => persist(false));
    paid.addEventListener('input', refreshTotals);
    methods.forEach(input => {
        input.addEventListener('change', syncPayment);
        input.addEventListener('click', () => {
            if (input.checked && ['transfer', 'qris'].includes(input.value)) void openManualPayment(input.value);
        });
    });
    root.querySelector('[data-cart-reconcile]').addEventListener('click', async () => {
        const button = root.querySelector('[data-cart-reconcile]');
        button.disabled = true;
        try {
            await syncCart();
            cart = cart.filter(item => Number(item.stock) > 0).map(item => ({ ...item, qty: Math.min(Number(item.qty), Number(item.stock), 1000) }));
            const subtotal = cart.reduce((sum, item) => sum + Number(item.price) * Number(item.qty), 0);
            if (numberValue(discount) > subtotal) setNumberValue(discount, subtotal);
            persist();
            root.querySelector('[data-cart-error]').hidden = true;
            if (cart.length) {
                const quote = await syncCart();
                if (!quote.can_checkout) showError(quote.issues.join(' '));
                else notice('Stok dan harga sudah disesuaikan. Periksa kembali jumlah barang dan total pembayaran.');
            } else { modal.close(); notice('Produk tidak tersedia sudah dihapus. Tambahkan produk lain untuk melanjutkan.'); }
        } catch (error) { showError(error.message); }
        finally { button.disabled = false; }
    });
    root.querySelector('[data-manual-back]').addEventListener('click', () => manualModal.close());
    manualModal.addEventListener('close', () => {
        if (!manualConfirmed && !modal.open) modal.showModal();
    });
    root.querySelector('[data-manual-confirm]').addEventListener('click', () => {
        manualConfirmed = true;
        manualConfirmedInput.disabled = false;
        manualModal.close();
        form.requestSubmit(root.querySelector('[data-cart-submit]'));
    });

    form.addEventListener('submit', async event => {
        if (cart.length === 0) { event.preventDefault(); return; }
        if (event.submitter?.hasAttribute('data-cart-draft')) return;
        event.preventDefault();
        if (checkoutBusy) return;
        const method = selectedMethod();
        let reviewedQuote;
        checkoutBusy = true;
        refreshTotals();
        try {
            const quote = await syncCart();
            reviewedQuote = quote;
            if (!quote.can_checkout) { showError(quote.issues.join(' ')); return; }
            if (quote.changed) {
                manualConfirmed = false;
                manualConfirmedInput.disabled = true;
                showError('Harga atau pajak berubah. Periksa total terbaru dan konfirmasi pembayaran kembali.');
                return;
            }
            if (method === 'cash' && numberValue(paid) < currentGrand) {
                showError(`Pembayaran masih kurang ${money(currentGrand - numberValue(paid))}.`);
                return;
            }
        } catch (error) { showError(error.message); return; }
        finally { checkoutBusy = false; refreshTotals(); }
        if ((method === 'transfer' || method === 'qris') && !manualConfirmed) {
            await openManualPayment(method, reviewedQuote);
            return;
        }
        root.querySelector('[data-cart-error]').hidden = true;
        checkoutBusy = true;
        refreshTotals();
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
            clearCartKeys(result.clear_cart_keys || [storageKey]);
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
            if (method === 'transfer' || method === 'qris') {
                manualConfirmed = false;
                manualConfirmedInput.disabled = true;
                if (!modal.open) modal.showModal();
            }
            showError(error.message || 'Checkout gagal diproses.');
        } finally {
            checkoutBusy = false;
            refreshTotals();
        }
    });

    syncPayment();
    if (cart.length) void syncCart().catch(() => {
        if (quoteInput.disabled) notice('Harga dan stok belum diperiksa. Buka pembayaran untuk mencoba lagi.');
    });
    barcodeInput.focus({ preventScroll: true });

    function clearDisplayedCart() {
        cart = [];
        cartRevision++;
        quoteSequence++;
        syncing = false;
        quoteInput.disabled = true;
        manualConfirmed = false;
        manualConfirmedInput.disabled = true;
        discount.value = '0';
        discount.groupedRawInput.value = '0';
        try { localStorage.removeItem(storageKey); } catch {}
        render();
    }
    window.addEventListener('storage', event => {
        if (event.key === storageKey && event.newValue === null || event.key === null) clearDisplayedCart();
    });
    window.addEventListener('pageshow', event => {
        if (!event.persisted) return;
        try { if (!localStorage.getItem(storageKey)) clearDisplayedCart(); } catch {}
    });
}
