/**
 * Navigasi app shell: progress bar + animasi konten + prefetch link.
 * Tidak mengganti full page reload (masih Laravel biasa), hanya bikin terasa lebih halus.
 */

function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

function createProgressBar() {
    let bar = document.getElementById('nprogress-bar');
    if (!bar) {
        bar = document.createElement('div');
        bar.id = 'nprogress-bar';
        bar.setAttribute('aria-hidden', 'true');
        document.body.prepend(bar);
    }
    return bar;
}

function startProgress() {
    const bar = createProgressBar();
    bar.classList.remove('is-done');
    bar.classList.add('is-active');
    bar.style.width = '12%';

    window.clearTimeout(startProgress._t1);
    window.clearTimeout(startProgress._t2);
    window.clearTimeout(startProgress._t3);

    startProgress._t1 = window.setTimeout(() => {
        bar.style.width = '42%';
    }, 120);
    startProgress._t2 = window.setTimeout(() => {
        bar.style.width = '68%';
    }, 420);
    startProgress._t3 = window.setTimeout(() => {
        bar.style.width = '82%';
    }, 900);
}

function finishProgress() {
    const bar = createProgressBar();
    window.clearTimeout(startProgress._t1);
    window.clearTimeout(startProgress._t2);
    window.clearTimeout(startProgress._t3);
    bar.classList.add('is-active', 'is-done');
    bar.style.width = '100%';
    window.setTimeout(() => {
        bar.classList.remove('is-active', 'is-done');
        bar.style.width = '0';
    }, 420);
}

function isInternalNavigationLink(anchor) {
    if (!anchor || anchor.tagName !== 'A') {
        return false;
    }

    if (anchor.target && anchor.target !== '_self') {
        return false;
    }

    if (anchor.hasAttribute('download')) {
        return false;
    }

    const href = anchor.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
        return false;
    }

    let url;
    try {
        url = new URL(anchor.href, window.location.origin);
    } catch {
        return false;
    }

    if (url.origin !== window.location.origin) {
        return false;
    }

    // Same URL (hash only) — jangan animasi
    if (url.pathname === window.location.pathname && url.search === window.location.search) {
        return false;
    }

    return true;
}

function markLeaving(anchor) {
    if (prefersReducedMotion()) {
        startProgress();
        return;
    }

    document.body.classList.add('is-page-leaving');
    startProgress();

    if (anchor?.classList.contains('sidebar-nav-link')) {
        document.querySelectorAll('.sidebar-nav-link.is-navigating').forEach((el) => {
            el.classList.remove('is-navigating');
        });
        anchor.classList.add('is-navigating');
    }
}

function setupNavigationFeel() {
    // Progress selesai saat halaman baru siap
    finishProgress();

    document.addEventListener(
        'click',
        (event) => {
            if (event.defaultPrevented || event.button !== 0) {
                return;
            }
            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            const anchor = event.target.closest('a');
            if (!isInternalNavigationLink(anchor)) {
                return;
            }

            markLeaving(anchor);
        },
        true,
    );

    // Prefetch saat hover menu sidebar → halaman berikutnya lebih cepat
    const prefetched = new Set();
    document.querySelectorAll('a.sidebar-nav-link[href]').forEach((link) => {
        const prefetch = () => {
            const href = link.href;
            if (!href || prefetched.has(href)) {
                return;
            }
            prefetched.add(href);

            const existing = document.head.querySelector(`link[rel="prefetch"][href="${href}"]`);
            if (existing) {
                return;
            }

            const tag = document.createElement('link');
            tag.rel = 'prefetch';
            tag.href = href;
            tag.as = 'document';
            document.head.appendChild(tag);
        };

        link.addEventListener('mouseenter', prefetch, { passive: true });
        link.addEventListener('focus', prefetch, { passive: true });
        link.addEventListener('touchstart', prefetch, { passive: true });
    });

    // Kalau user back/forward dari cache, bersihkan state leaving
    window.addEventListener('pageshow', (event) => {
        document.body.classList.remove('is-page-leaving');
        document.querySelectorAll('.sidebar-nav-link.is-navigating').forEach((el) => {
            el.classList.remove('is-navigating');
        });
        if (event.persisted) {
            finishProgress();
        }
    });
}

function setupSidebar() {
    const toggleBtn = document.getElementById('sidebarToggle');
    const closeBtn = document.getElementById('sidebarClose');
    const backdrop = document.getElementById('sidebarBackdrop');
    const sidebar = document.getElementById('sidebar');

    if (!sidebar || !backdrop) {
        return;
    }

    const openSidebar = () => {
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
    };

    const closeSidebar = () => {
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
    };

    toggleBtn?.addEventListener('click', openSidebar);
    closeBtn?.addEventListener('click', closeSidebar);
    backdrop.addEventListener('click', closeSidebar);
}

function setupIcons() {
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons({
            attrs: {
                'stroke-width': 1.75,
            },
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    setupSidebar();
    setupNavigationFeel();
    setupIcons();
});
