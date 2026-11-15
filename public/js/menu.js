// ===== Keranjang sederhana di browser (localStorage), terpisah per akun =====
(function () {
    var uid = (document.querySelector('meta[name="user-id"]') || {}).content || 'guest';
    var KEY = 'kantin_keranjang_' + uid;
    var bar = document.getElementById('cartBar');
    if (!bar) return;

    var elCount = document.getElementById('cartCount');
    var elStan = document.getElementById('cartStan');
    var elTotal = document.getElementById('cartTotal');
    var items = load();

    function load() {
        try { return JSON.parse(localStorage.getItem(KEY)) || []; } catch (e) { return []; }
    }
    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(items)); } catch (e) {}
    }
    function rupiah(n) {
        return 'Rp ' + n.toLocaleString('id-ID');
    }

    function render() {
        var qty = 0, total = 0, stans = {}, jumlahStan = 0, first = '';
        items.forEach(function (i) {
            qty += i.qty;
            total += i.qty * i.harga;
            if (!stans[i.stanId]) {
                stans[i.stanId] = true;
                jumlahStan++;
                if (!first) first = i.stan;
            }
        });

        if (!qty) {
            bar.classList.remove('show');
            bar.setAttribute('aria-hidden', 'true');
            return;
        }
        elCount.textContent = qty + ' Menu Dipilih';
        elStan.textContent = first + (jumlahStan > 1 ? ' + ' + (jumlahStan - 1) + ' Stan' : '');
        elTotal.textContent = rupiah(total);
        bar.classList.add('show');
        bar.removeAttribute('aria-hidden');
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.m-add');
        if (!btn || btn.disabled) return;

        var id = Number(btn.dataset.id);
        var found = items.find(function (i) { return i.id === id; });
        if (found) {
            found.qty += 1;
        } else {
            items.push({
                id: id,
                nama: btn.dataset.nama,
                harga: Number(btn.dataset.harga),
                stanId: Number(btn.dataset.stanId),
                stan: btn.dataset.stan,
                qty: 1
            });
        }
        save();
        render();

        btn.classList.add('added');
        setTimeout(function () { btn.classList.remove('added'); }, 600);
    });

    // Halaman dari cache (tombol Back) tetap menampilkan keranjang terbaru
    window.addEventListener('pageshow', function () {
        items = load();
        render();
    });

    render();
})();

// ===== Pencarian, kategori, dan pagination tanpa refresh (AJAX) =====
(function () {
    var box = document.getElementById('menuAjax');
    if (!box) return;

    var timer, ctrl;

    function load(url, fromTyping) {
        if (ctrl) ctrl.abort();
        ctrl = new AbortController();
        box.classList.add('is-loading');

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, signal: ctrl.signal })
            .then(function (r) { return r.text(); })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var fresh = doc.getElementById('menuAjax');
                if (!fresh) { window.location.href = url; return; }

                var search = box.querySelector('.m-search');
                var freshSearch = fresh.querySelector('.m-search');
                var input = document.getElementById('searchInput');

                // 1) Ganti semua bagian KECUALI kolom pencarian
                Array.from(box.children).forEach(function (el) {
                    if (el !== search) el.remove();
                });
                Array.from(fresh.children).forEach(function (el) {
                    if (el.classList.contains('m-search')) return;
                    box.appendChild(document.importNode(el, true));
                });

                // 2) Sinkronkan bagian pencarian tanpa menyentuh input
                if (search && freshSearch) {
                    var pop = search.querySelector('.m-popular');
                    var freshPop = freshSearch.querySelector('.m-popular');
                    if (pop && freshPop) pop.innerHTML = freshPop.innerHTML;

                    var form = search.querySelector('form');
                    var hid = form.querySelector('input[name="kategori"]');
                    var freshHid = freshSearch.querySelector('input[name="kategori"]');
                    if (hid) hid.remove();
                    if (freshHid) form.insertBefore(document.importNode(freshHid, true), form.firstChild);

                    var label = form.querySelector('.m-input');
                    var clear = label.querySelector('.m-clear');
                    var freshClear = freshSearch.querySelector('.m-clear');
                    if (clear) clear.remove();
                    if (freshClear) label.appendChild(document.importNode(freshClear, true));
                }

                // 3) Jika bukan dari mengetik (klik chip, hapus, Back), isi kolom ikut hasil
                if (!fromTyping && input) {
                    var freshInput = fresh.querySelector('#searchInput');
                    input.value = freshInput ? freshInput.value : '';
                }

                box.classList.remove('is-loading');
                if (url !== window.location.href) history.pushState(null, '', url);
            })
            .catch(function (err) {
                if (err.name !== 'AbortError') box.classList.remove('is-loading');
            });
    }

    function buildUrl(form) {
        var params = new URLSearchParams(new FormData(form));
        var q = (params.get('q') || '').trim();
        if (q === '') params.delete('q'); else params.set('q', q);
        Array.from(params.keys()).forEach(function (k) {
            if (params.get(k) === '') params.delete(k);
        });
        var qs = params.toString();
        return form.getAttribute('action') + (qs ? '?' + qs : '');
    }

    // Ketik: cari otomatis 200 ms setelah berhenti mengetik
    box.addEventListener('input', function (e) {
        if (e.target.id !== 'searchInput') return;
        clearTimeout(timer);
        var form = e.target.closest('form');
        timer = setTimeout(function () { load(buildUrl(form), true); }, 200);
    });

    // Enter / tombol "Cari Menu"
    box.addEventListener('submit', function (e) {
        e.preventDefault();
        clearTimeout(timer);
        load(buildUrl(e.target), true);
    });

    // Klik chip populer, tab kategori, tombol hapus, dan pagination
    box.addEventListener('click', function (e) {
        var a = e.target.closest('.m-popular a, .m-chips a, .m-clear, .m-results a, .m-empty a');
        if (!a || !a.href) return;
        e.preventDefault();
        clearTimeout(timer);
        load(a.href, false);
    });

    // Tombol Back/Forward browser
    window.addEventListener('popstate', function () {
        load(window.location.href, false);
    });
})();