// Menampilkan spinner pada tombol submit login/register dan mencegah klik ganda.
(function () {
    function reset(form, btn) {
        delete form.dataset.submitting;
        btn.classList.remove('is-loading');
        btn.removeAttribute('aria-busy');
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form').forEach(function (form) {
            var btn = form.querySelector('.login-submit, .register-submit');
            if (!btn) return;

            form.addEventListener('submit', function (e) {
                // Form hanya sampai sini kalau validasi bawaan browser sudah lolos
                if (form.dataset.submitting === '1') {
                    e.preventDefault();
                    return;
                }
                form.dataset.submitting = '1';
                btn.classList.add('is-loading');
                btn.setAttribute('aria-busy', 'true');
            });

            // Tombol "Back" browser bisa memulihkan halaman dari cache dengan spinner masih aktif
            window.addEventListener('pageshow', function (e) {
                if (e.persisted) reset(form, btn);
            });
        });
    });
})();