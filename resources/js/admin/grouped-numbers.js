const digits = value => String(value ?? '').replace(/\D/g, '');

export const numberValue = input => Number(digits(input.value)) || 0;

export function setNumberValue(input, value) {
    input.value = digits(value);
    input.dispatchEvent(new Event('input', { bubbles: true }));
}

function attach(input) {
    if (input.groupedRawInput) return;
    const name = input.name;
    if (!name) return;

    const hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = name;
    input.after(hidden);
    input.removeAttribute('name');
    input.type = 'text';
    input.inputMode = 'numeric';
    input.autocomplete = 'off';
    input.groupedRawInput = hidden;

    const sync = () => {
        const raw = digits(input.value);
        const normalized = raw ? String(Number(raw)) : '';
        hidden.value = normalized;
        input.value = normalized ? Number(normalized).toLocaleString('id-ID') : '';
    };
    input.addEventListener('input', sync);
    sync();
}

document.querySelectorAll('[data-grouped-number]').forEach(attach);
document.addEventListener('focusin', event => {
    if (event.target.matches?.('[data-grouped-number]')) attach(event.target);
});
