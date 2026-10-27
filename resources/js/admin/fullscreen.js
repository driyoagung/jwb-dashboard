// The fullscreen element remains mounted while its iframe navigates between POS pages.
const button = document.querySelector('[data-pos-fullscreen]');

if (button) {
    if (window.frameElement && window.parent !== window) {
        const syncLabel = () => {
            const label = window.parent.document.fullscreenElement === window.frameElement ? 'Keluar layar penuh' : 'Layar penuh';
            button.querySelector('[data-fullscreen-label]').textContent = label;
            button.setAttribute('aria-label', label);
            button.title = label;
        };
        window.parent.document.addEventListener('fullscreenchange', syncLabel);
        syncLabel();
    }
    button.addEventListener('click', async () => {
        if (window.frameElement && window.parent !== window && window.parent.document.fullscreenElement === window.frameElement) {
            await window.parent.document.exitFullscreen();
            return;
        }
        if (!document.fullscreenEnabled) return;

        const frame = document.createElement('iframe');
        frame.src = window.location.href;
        frame.title = 'Griyo POS layar penuh';
        frame.allow = 'fullscreen';
        frame.className = 'h-full w-full border-0 bg-background';
        document.body.append(frame);

        const syncOnExit = () => {
            if (document.fullscreenElement === frame) return;
            document.removeEventListener('fullscreenchange', syncOnExit);
            let destination = window.location.href;
            try { destination = frame.contentWindow.location.href; } catch (error) {}
            frame.remove();
            if (destination !== window.location.href) window.location.assign(destination);
        };
        document.addEventListener('fullscreenchange', syncOnExit);
        try {
            await frame.requestFullscreen({ navigationUI: 'hide' });
        } catch (error) {
            document.removeEventListener('fullscreenchange', syncOnExit);
            frame.remove();
        }
    });
}
