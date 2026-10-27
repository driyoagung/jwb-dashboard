// Kenanga table markup with database-backed filtering and pagination.
document.querySelectorAll('[data-pos-server-table]').forEach(table => {
    const form = table.querySelector('[data-pos-live-filter]');
    const results = [...table.querySelectorAll('[data-pos-results]')];
    if (!form || !results.length) return;

    let pending;
    let timer;
    let syncing = false;
    const basePath = new URL(form.action, window.location.href).pathname;

    function syncForm(freshForm) {
        syncing = true;
        [...form.elements].forEach(field => {
            if (!field.name) return;
            const fresh = freshForm.elements.namedItem(field.name);
            if (!fresh) return;
            const value = fresh.value;
            if (field._flatpickr) {
                if (value) field._flatpickr.setDate(value, false);
                else field._flatpickr.clear(false);
            } else if (field._kenangaChoices) {
                field._kenangaChoices.removeActiveItems();
                field._kenangaChoices.setChoiceByValue(value);
            } else {
                field.value = value;
            }
        });
        form.querySelectorAll('[data-pos-filter-group]').forEach(group => {
            const value = group.querySelector('[data-pos-filter-value]')?.value;
            group.querySelectorAll('[data-pos-filter-option]').forEach(button => {
                button.setAttribute('aria-pressed', String(button.dataset.posFilterOption === value));
            });
        });
        syncing = false;
    }

    async function load(url, { history = true, filters = false } = {}) {
        if (pending) pending.abort();
        const controller = new AbortController();
        pending = controller;
        table.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                signal: controller.signal,
            });
            if (!response.ok || response.redirected) throw new Error('Gagal memuat tabel');
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            const freshTable = page.querySelector('[data-pos-server-table]');
            const freshResults = [...(freshTable?.querySelectorAll('[data-pos-results]') || [])];
            if (freshResults.length !== results.length) throw new Error('Hasil tabel tidak tersedia');
            results.forEach((section, index) => section.replaceChildren(...freshResults[index].childNodes));
            if (filters) syncForm(freshTable.querySelector('[data-pos-live-filter]'));
            if (history && window.location.href !== String(url)) window.history.pushState({}, '', url);
            results.forEach(section => window.App?.initPage(section));
        } catch (error) {
            if (error.name !== 'AbortError') window.location.assign(url);
        } finally {
            if (pending === controller) {
                pending = null;
                table.removeAttribute('aria-busy');
            }
        }
    }

    function filter() {
        if (syncing) return;
        const from = form.elements.namedItem('from')?.value;
        const to = form.elements.namedItem('to')?.value;
        if (from && to && from > to) return;
        const url = new URL(form.action, window.location.href);
        const current = new URL(window.location.href);
        ['sort', 'direction'].forEach(key => {
            if (!form.elements.namedItem(key) && current.searchParams.has(key)) {
                url.searchParams.set(key, current.searchParams.get(key));
            }
        });
        new FormData(form).forEach((value, key) => {
            if (String(value).trim() !== '') url.searchParams.set(key, value);
        });
        load(url);
    }

    form.addEventListener('submit', event => {
        event.preventDefault();
        clearTimeout(timer);
        filter();
    });
    form.addEventListener('input', event => {
        if (event.target.matches('input[type="search"]')) {
            clearTimeout(timer);
            timer = setTimeout(filter, 300);
        }
    });
    form.addEventListener('change', event => {
        if (!event.target.matches('input[type="search"]')) {
            clearTimeout(timer);
            filter();
        }
    });
    form.addEventListener('click', event => {
        const option = event.target.closest('[data-pos-filter-option]');
        if (option && form.contains(option)) {
            const group = option.closest('[data-pos-filter-group]');
            const input = group?.querySelector('[data-pos-filter-value]');
            if (input && input.value !== option.dataset.posFilterOption) {
                input.value = option.dataset.posFilterOption;
                group.querySelectorAll('[data-pos-filter-option]').forEach(button => {
                    button.setAttribute('aria-pressed', String(button === option));
                });
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
        if (event.target.closest('[data-range-days], [data-range-clear]')) {
            clearTimeout(timer);
            timer = setTimeout(filter, 0);
        }
    });
    table.addEventListener('click', event => {
        const link = event.target.closest('a[href]');
        if (!link || !table.contains(link) || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.target) return;
        const url = new URL(link.href);
        if (url.origin !== window.location.origin || url.pathname !== basePath) return;
        if (!link.closest('[data-pos-results]') && !link.closest('[data-pos-live-filter]')) return;
        event.preventDefault();
        clearTimeout(timer);
        load(url, { filters: Boolean(link.closest('[data-pos-live-filter]')) });
    });
    window.addEventListener('popstate', () => {
        if (window.location.pathname === basePath) load(window.location.href, { history: false, filters: true });
    });
});
