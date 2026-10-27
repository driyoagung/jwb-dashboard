const container = document.querySelector('[data-notifications]');

if (container) {
    const list = container.querySelector('[data-notification-list]');
    const count = container.querySelector('[data-notification-count]');
    const indicator = container.querySelector('[data-notification-indicator]');

    function action(url, label, icon, variant, method = 'POST') {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        const csrf = document.createElement('input');
        csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = container.dataset.csrf;
        form.append(csrf);
        if (method !== 'POST') {
            const spoof = document.createElement('input');
            spoof.type = 'hidden'; spoof.name = '_method'; spoof.value = method;
            form.append(spoof);
        }
        const button = document.createElement('button');
        button.type = 'submit';
        button.className = `btn btn-${variant} btn-sm`;
        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('class', 'h-4 w-4'); svg.setAttribute('aria-hidden', 'true');
        const use = document.createElementNS('http://www.w3.org/2000/svg', 'use');
        use.setAttribute('href', `/icons.svg#i-${icon}`);
        svg.append(use);
        button.append(svg, document.createTextNode(label));
        form.append(button);
        return form;
    }

    async function refresh() {
        try {
            const response = await fetch(container.dataset.feedUrl, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
            if (!response.ok) return;
            const data = await response.json();
            count.textContent = data.unread ? `Notifikasi (${data.unread} baru)` : 'Notifikasi';
            indicator.hidden = data.unread === 0;
            container.querySelector('[data-notification-read-all]').disabled = data.unread === 0;
            container.querySelector('[data-notification-delete-all]').disabled = data.total === 0;
            list.replaceChildren();

            if (data.items.length === 0) {
                const empty = document.createElement('p');
                empty.className = 'px-3 py-2 text-sm text-muted-foreground';
                empty.textContent = 'Belum ada notifikasi.';
                list.append(empty);
                return;
            }

            for (const item of data.items) {
                const row = document.createElement('div');
                row.className = 'border-b px-3 py-2 last:border-b-0';
                const title = document.createElement('p');
                title.className = 'text-sm font-medium';
                title.textContent = item.title + (item.unread ? ' •' : '');
                const message = document.createElement('p');
                message.className = 'text-xs text-muted-foreground';
                message.textContent = item.message;
                const actions = document.createElement('div');
                actions.className = 'mt-2 flex flex-wrap gap-1';
                actions.append(action(item.read_url, 'Buka', 'eye', 'info'));
                if (item.unread) actions.append(action(item.mark_url, 'Tandai dibaca', 'check', 'outline'));
                actions.append(action(item.delete_url, 'Hapus', 'trash', 'destructive', 'DELETE'));
                row.append(title, message, actions);
                list.append(row);
            }
        } catch {
            // Keep the server-rendered notifications available when offline.
        }
    }

    window.setInterval(refresh, 20000);
}
