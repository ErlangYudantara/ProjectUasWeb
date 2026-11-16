(function () {
    var root = document.getElementById('kLayout');
    if (!root) return;

    // ===== Pengaturan biaya (ubah sesuai aturan kantin) =====
    var DISKON_PER_ITEM = 500;      // Diskon Wadah Hijau per item
    var BIAYA_BUNGKUS = 0;          // Biaya pengemasan bila memilih Take Away

    var uid = (document.querySelector('meta[name="user-id"]') || {}).content || 'guest';
    var KEY = 'kantin_keranjang_' + uid;
    var OPSI_KEY = 'kantin_opsi_' + uid;

    var elList = document.getElementById('kList');
    var elCount = document.getElementById('kCount');
    var elSubLabel = document.getElementById('kSubLabel');
    var elSubtotal = document.getElementById('kSubtotal');
    var elDiskon = document.getElementById('kDiskon');
    var elPack = document.getElementById('kPack');
    var elTotal = document.getElementById('kTotal');
    var elPay = document.getElementById('kPay');
    var elWarn = document.getElementById('kWarn');

    var items = load();
    var data = {};          // detail menu dari server, kunci = id
    var opsi = loadOpsi();
    var loaded = false;

    // ----- penyimpanan -----
    function load() {
        try { return JSON.parse(localStorage.getItem(KEY)) || []; } catch (e) { return []; }
    }
    function save() {
        try { localStorage.setItem(KEY, JSON.stringify(items)); } catch (e) {}
    }
    function loadOpsi() {
        try { return localStorage.getItem(OPSI_KEY) === 'bungkus' ? 'bungkus' : 'makan'; } catch (e) { return 'makan'; }
    }
    function saveOpsi() {
        try { localStorage.setItem(OPSI_KEY, opsi); } catch (e) {}
    }

    // ----- util -----
    function rupiah(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); }
    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function pad2(n) { return String(n).padStart(2, '0'); }

    var ICON_TRASH = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v6M14 11v6"/></svg>';
    var ICON_NOTE = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>';

    // ----- ambil detail terbaru dari server -----
    function fetchDetail() {
        if (!items.length) { loaded = true; render(); return; }
        var ids = items.map(function (i) { return i.id; }).join(',');
        fetch(root.dataset.detailUrl + '?ids=' + encodeURIComponent(ids), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (r) { return r.json(); })
            .then(function (json) { data = json || {}; loaded = true; render(); })
            .catch(function () {
                loaded = true;
                elList.innerHTML = '<div class="k-card k-loading">Gagal memuat keranjang. Muat ulang halaman.</div>';
            });
    }

    // ----- tampilan -----
    function render() {
        if (!items.length) {
            elList.innerHTML =
                '<div class="k-card k-empty">' +
                '<h3>Keranjangmu masih kosong</h3>' +
                '<p>Pilih menu favoritmu dulu, lalu kembali ke sini.</p>' +
                '<a href="' + esc(document.querySelector('.k-back').href) + '" class="btn btn-primary btn-sm">Lihat Menu</a>' +
                '</div>';
            summary();
            return;
        }

        // rapikan jumlah terhadap stok terbaru
        var changed = false;
        items.forEach(function (it) {
            var d = data[it.id];
            if (d && d.tersedia && it.qty > d.stok) { it.qty = d.stok; changed = true; }
        });
        if (changed) save();

        // kelompokkan per stan
        var groups = [], index = {};
        items.forEach(function (it) {
            var d = data[it.id];
            var stanId = d ? d.stanId : it.stanId;
            var stanNama = d ? d.stan : it.stan;
            if (!(stanId in index)) {
                index[stanId] = groups.length;
                groups.push({ id: stanId, nama: stanNama, items: [] });
            }
            groups[index[stanId]].items.push(it);
        });

        elList.innerHTML = groups.map(function (g) {
            return '<section class="k-card k-stan">' +
                '<header class="k-stan-head">' +
                '<span class="k-stan-no">' + pad2(g.id) + '</span>' +
                '<div><strong>' + esc(g.nama) + '</strong><small>STAN #' + pad2(g.id) + '</small></div>' +
                '</header>' +
                g.items.map(rowHtml).join('') +
                '</section>';
        }).join('');

        summary();
    }

    function rowHtml(it) {
        var d = data[it.id];
        var ok = !!(d && d.tersedia);
        var nama = d ? d.nama : it.nama;
        var harga = d ? d.harga : it.harga;
        var foto = d && d.foto ? '<img src="' + esc(d.foto) + '" alt="' + esc(nama) + '" loading="lazy">' : '<div class="k-noimg"></div>';
        var maxed = ok && it.qty >= d.stok;

        return '<article class="k-item' + (ok ? '' : ' off') + '" data-id="' + it.id + '">' +
            '<div class="k-photo">' + foto + '</div>' +
            '<div class="k-info">' +
                '<div class="k-title">' +
                    '<div><h3>' + esc(nama) + '</h3>' +
                    '<p>' + esc(d ? d.deskripsi : '') + '</p></div>' +
                    '<button type="button" class="k-del" data-act="del" aria-label="Hapus ' + esc(nama) + '">' + ICON_TRASH + '</button>' +
                '</div>' +
                (ok
                    ? '<label class="k-note-box">' + ICON_NOTE + '<span>Catatan:</span>' +
                      '<input type="text" class="k-note-input" maxlength="100" placeholder="Contoh: pedas level sedang" value="' + esc(it.catatan || '') + '"></label>'
                    : '<p class="k-off">' + (d ? 'Menu ini sedang habis atau tidak tersedia. Hapus dari keranjang untuk melanjutkan.' : 'Menu ini sudah tidak ada. Hapus dari keranjang untuk melanjutkan.') + '</p>') +
                '<div class="k-price-row">' +
                    '<strong class="k-price">' + rupiah(harga) + '</strong>' +
                    (ok
                        ? '<div class="k-qty">' +
                          '<button type="button" data-act="minus" aria-label="Kurangi"' + (it.qty <= 1 ? ' disabled' : '') + '>&minus;</button>' +
                          '<span>' + it.qty + '</span>' +
                          '<button type="button" class="plus" data-act="plus" aria-label="Tambah"' + (maxed ? ' disabled' : '') + '>+</button>' +
                          '</div>'
                        : '') +
                '</div>' +
            '</div>' +
        '</article>';
    }

    function summary() {
        var qty = 0, subtotal = 0, bermasalah = 0;
        items.forEach(function (it) {
            var d = data[it.id];
            if (d && d.tersedia) { qty += it.qty; subtotal += it.qty * d.harga; }
            else if (loaded) { bermasalah++; }
        });

        var diskon = Math.min(qty * DISKON_PER_ITEM, subtotal);
        var pack = (opsi === 'bungkus' && qty) ? BIAYA_BUNGKUS : 0;
        var total = subtotal - diskon + pack;

        elCount.textContent = qty + ' Item';
        elSubLabel.textContent = 'Subtotal Menu (' + qty + ' item)';
        elSubtotal.textContent = rupiah(subtotal);
        elDiskon.textContent = '-' + rupiah(diskon);
        elPack.textContent = rupiah(pack);
        elTotal.textContent = rupiah(total);

        elPay.disabled = !(loaded && qty > 0 && bermasalah === 0);
        if (bermasalah) {
            elWarn.hidden = false;
            elWarn.textContent = bermasalah + ' menu tidak tersedia. Hapus dulu untuk melanjutkan.';
        } else {
            elWarn.hidden = true;
        }

        document.querySelectorAll('.k-toggle button').forEach(function (b) {
            b.classList.toggle('on', b.dataset.opsi === opsi);
        });
    }

    // ----- interaksi -----
    elList.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-act]');
        if (!btn || btn.disabled) return;
        var row = btn.closest('.k-item');
        var id = Number(row.dataset.id);
        var it = items.find(function (i) { return i.id === id; });
        if (!it) return;

        var act = btn.dataset.act;
        if (act === 'plus') it.qty += 1;
        if (act === 'minus' && it.qty > 1) it.qty -= 1;
        if (act === 'del') items = items.filter(function (i) { return i.id !== id; });

        save();
        render();
    });

    elList.addEventListener('input', function (e) {
        if (!e.target.classList.contains('k-note-input')) return;
        var id = Number(e.target.closest('.k-item').dataset.id);
        var it = items.find(function (i) { return i.id === id; });
        if (it) { it.catatan = e.target.value; save(); }
    });

    document.querySelector('.k-toggle').addEventListener('click', function (e) {
        var b = e.target.closest('button[data-opsi]');
        if (!b) return;
        opsi = b.dataset.opsi;
        saveOpsi();
        summary();
    });

    elPay.addEventListener('click', function () {
        if (root.dataset.auth !== '1') {
            window.location.href = root.dataset.loginUrl;
            return;
        }
        // Halaman pembayaran QRIS dibuat di langkah berikutnya
        alert('Halaman pembayaran QRIS belum dibuat.');
    });

    // Tombol Back dari cache: ambil data keranjang terbaru
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) { items = load(); fetchDetail(); }
    });

    fetchDetail();
})();