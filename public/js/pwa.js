(function () {
    // Register service worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        });
    }

    // Kalau udah jalan sebagai app terinstall (standalone), gak usah nawarin install lagi.
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches
        || window.navigator.standalone === true;
    if (isStandalone) return;

    // Jangan nawarin lagi kalau baru aja di-dismiss (7 hari)
    const DISMISS_KEY = 'berlianpay_install_dismissed_at';
    const dismissedAt = localStorage.getItem(DISMISS_KEY);
    if (dismissedAt && (Date.now() - parseInt(dismissedAt, 10)) < 7 * 24 * 60 * 60 * 1000) {
        return;
    }

    function buildBanner(message, actions) {
        const banner = document.createElement('div');
        banner.id = 'pwaInstallBanner';
        banner.style.cssText = [
            'position:fixed', 'left:12px', 'right:12px', 'bottom:12px', 'z-index:9999',
            'background:#1e293b', 'color:#fff', 'border-radius:14px', 'padding:14px 16px',
            'box-shadow:0 10px 30px rgba(0,0,0,0.25)', 'font-family:sans-serif',
            'display:flex', 'align-items:center', 'gap:12px', 'max-width:420px',
            'margin:0 auto',
        ].join(';');

        const icon = document.createElement('div');
        icon.style.cssText = 'width:36px;height:36px;border-radius:9px;background:#1a45c0;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:16px;';
        icon.textContent = 'B';

        const textWrap = document.createElement('div');
        textWrap.style.cssText = 'flex:1;font-size:13px;line-height:1.4;';
        textWrap.textContent = message;

        banner.appendChild(icon);
        banner.appendChild(textWrap);

        actions.forEach(({ label, primary, onClick }) => {
            const btn = document.createElement('button');
            btn.textContent = label;
            btn.style.cssText = [
                'border:0', 'border-radius:8px', 'padding:8px 12px', 'font-size:13px',
                'font-weight:500', 'cursor:pointer', 'white-space:nowrap',
                primary ? 'background:#fff;color:#1a45c0;' : 'background:transparent;color:#cbd5e1;',
            ].join(';');
            btn.onclick = onClick;
            banner.appendChild(btn);
        });

        return banner;
    }

    function dismiss() {
        localStorage.setItem(DISMISS_KEY, Date.now().toString());
        const el = document.getElementById('pwaInstallBanner');
        if (el) el.remove();
    }

    // --- Android / Desktop Chrome & Edge: pakai native install prompt ---
    let deferredPrompt = null;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;

        const banner = buildBanner('Install BerlianPay ke perangkat kamu buat akses lebih cepat.', [
            { label: 'Nanti', primary: false, onClick: dismiss },
            {
                label: 'Install', primary: true, onClick: async () => {
                    dismiss();
                    if (deferredPrompt) {
                        deferredPrompt.prompt();
                        await deferredPrompt.userChoice;
                        deferredPrompt = null;
                    }
                },
            },
        ]);
        document.body.appendChild(banner);
    });

    // --- iOS Safari: gak ada API prompt otomatis, kasih instruksi manual ---
    const isIos = /iphone|ipad|ipod/.test(window.navigator.userAgent.toLowerCase());
    const isSafari = /safari/.test(window.navigator.userAgent.toLowerCase())
        && !/crios|fxios|opios/.test(window.navigator.userAgent.toLowerCase());

    if (isIos && isSafari) {
        const banner = buildBanner('Tap tombol Share (kotak panah ke atas), lalu pilih "Add to Home Screen" buat install BerlianPay.', [
            { label: 'Oke', primary: true, onClick: dismiss },
        ]);
        document.body.appendChild(banner);
    }
})();
