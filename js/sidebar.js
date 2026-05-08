/**
 * InvoiceOptimizer Ai — Sidebar colapsable + hamburguesa móvil
 */
document.addEventListener('DOMContentLoaded', function () {
    const sidebar   = document.getElementById('sidebar');
    const main      = document.getElementById('main');
    const btnToggle = document.getElementById('btn-toggle-sidebar');
    const overlay   = document.getElementById('sidebar-overlay');
    const btnHamburger = document.getElementById('btn-hamburger');

    const isDesktop = () => window.innerWidth > 768;

    // Restaurar estado colapsado en desktop
    if (localStorage.getItem('sidebar_collapsed') === 'true' && isDesktop()) {
        sidebar.classList.add('collapsed');
        main.classList.add('sidebar-collapsed');
    }

    document.documentElement.classList.remove('sidebar-pre-collapsed');

    // Botón del sidebar (colapsar en desktop / cerrar en móvil)
    if (btnToggle) {
        btnToggle.addEventListener('click', function () {

            if (isDesktop()) {
                // 💻 Desktop → colapsar
                const isCollapsed = sidebar.classList.toggle('collapsed');
                main.classList.toggle('sidebar-collapsed', isCollapsed);
                localStorage.setItem('sidebar_collapsed', isCollapsed);
            } else {
                // 📱 Móvil → cerrar sidebar
                sidebar.classList.remove('mobile-open');
                overlay?.classList.remove('visible');
            }

            if (window.lucide) lucide.createIcons();
        });
    }

    // Botón hamburger (abrir en móvil)
    if (btnHamburger) {
        btnHamburger.addEventListener('click', function () {
            sidebar.classList.add('mobile-open');
            overlay?.classList.add('visible');
        });
    }

    // Cerrar al hacer click en overlay
    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('visible');
        });
    }

    // Cerrar al hacer click en un link (solo móvil)
    document.querySelectorAll('.nav-item').forEach(function(link) {
        link.addEventListener('click', function () {
            if (!isDesktop()) {
                sidebar.classList.remove('mobile-open');
                overlay?.classList.remove('visible');
            }
        });
    });

    // Ajuste al redimensionar ventana
    window.addEventListener('resize', function () {
        if (!isDesktop()) {
            main.style.marginLeft = '';
            sidebar.classList.remove('collapsed');
            main.classList.remove('sidebar-collapsed');
        } else {
            // Cerrar sidebar móvil si se amplía la ventana
            sidebar.classList.remove('mobile-open');
            overlay?.classList.remove('visible');

            const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
            sidebar.classList.toggle('collapsed', isCollapsed);
            main.classList.toggle('sidebar-collapsed', isCollapsed);
        }
    });

    if (window.lucide) lucide.createIcons();
});