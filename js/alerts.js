document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.alert').forEach(function (el) {
        requestAnimationFrame(function () {
            el.classList.add('show');
        });

        // ✅ Limpia el msg de la URL nada más mostrarlo
        if (window.location.search.includes('msg=')) {
            const url = new URL(window.location);
            url.searchParams.delete('msg');
            window.history.replaceState({}, '', url);
        }

        setTimeout(function () {
            el.classList.remove('show');
            el.classList.add('hiding');
            el.addEventListener('transitionend', function () {
                el.remove();
            }, { once: true });
        }, 3000);
    });
});