// Menutup dropdown profil saat klik di luar atau menekan Escape
(function () {
    function closeAll(focusButton) {
        document.querySelectorAll('details.um[open]').forEach(function (d) {
            d.removeAttribute('open');
            if (focusButton) d.querySelector('summary').focus();
        });
    }
    document.addEventListener('click', function (e) {
        document.querySelectorAll('details.um[open]').forEach(function (d) {
            if (!d.contains(e.target)) d.removeAttribute('open');
        });
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeAll(true);
    });
})();