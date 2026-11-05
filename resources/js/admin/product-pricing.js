import { numberValue } from './grouped-numbers';

document.querySelectorAll('[data-product-pricing]').forEach(form => {
    const cost = form.querySelector('[data-product-cost]');
    const sell = form.querySelector('[data-product-sell]');
    const warning = form.querySelector('[data-price-warning]');
    const validate = () => {
        const invalid = cost.value !== '' && sell.value !== '' && numberValue(cost) > numberValue(sell);
        warning.hidden = !invalid;
        cost.setCustomValidity(invalid ? warning.textContent : '');
        cost.setAttribute('aria-invalid', String(invalid));
    };
    cost.addEventListener('input', validate);
    sell.addEventListener('input', validate);
    validate();
});
