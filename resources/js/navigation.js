// Navegación visual compartida entre los cuatro portales.
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const trigger = document.getElementById('menuButton');
    const close = document.getElementById('sidebarClose');
    const overlay = document.getElementById('sidebarOverlay');
    if (!sidebar || !trigger || !close || !overlay) return;

    const setOpen = (open) => {
        sidebar.classList.toggle('open', open);
        overlay.classList.toggle('show', open);
        trigger.setAttribute('aria-expanded', String(open));
        if (open) close.focus();
        else trigger.focus();
    };
    trigger.addEventListener('click', () => setOpen(true));
    close.addEventListener('click', () => setOpen(false));
    overlay.addEventListener('click', () => setOpen(false));
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && sidebar.classList.contains('open')) setOpen(false);
    });
});
