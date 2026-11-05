document.querySelectorAll('[data-password-field]').forEach(field => {
    const input = field.querySelector('input[type="password"]');
    const toggle = field.querySelector('[data-password-toggle]');
    if (!input || !toggle) return;

    toggle.addEventListener('click', () => {
        const visible = input.type === 'password';
        input.type = visible ? 'text' : 'password';
        toggle.setAttribute('aria-pressed', String(visible));
        toggle.setAttribute('aria-label', visible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        input.focus({ preventScroll: true });
    });
});
