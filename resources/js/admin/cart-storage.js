const cartKey = /^pos-cart-\d+$|^pos-draft-\d+-\d+$/;

export function clearCartKeys(keys) {
    if (!Array.isArray(keys)) return;
    try {
        keys.filter(key => typeof key === 'string' && cartKey.test(key)).forEach(key => localStorage.removeItem(key));
    } catch {}
}

try {
    clearCartKeys(JSON.parse(document.body.dataset.posClearCartKeys || '[]'));
    const userId = document.body.dataset.posClearCartUser;
    if (/^\d+$/.test(userId || '')) {
        const keys = [];
        for (let index = 0; index < localStorage.length; index++) {
            const key = localStorage.key(index);
            if (key === `pos-cart-${userId}` || /^pos-draft-\d+-\d+$/.test(key || '') && key.endsWith(`-${userId}`)) keys.push(key);
        }
        clearCartKeys(keys);
    }
} catch {}
