@php
    $tgPegawai = isset($pegawai) ? $pegawai : null;
    $tgChatId = $tgPegawai && isset($tgPegawai->tg_chat_id) ? $tgPegawai->tg_chat_id : null;
    $tgLinked = !empty($tgChatId);
    $tgBot = env('TELEGRAM_BOT_USERNAME', '');
    $tgHasToken = (bool) session('telegram_link_token');
@endphp

<hr class="my-3">

{{-- Koneksi Telegram --}}
<div class="card shadow-sm" id="card-telegram"
     data-telegram-widget="1"
     data-status-url="{{ route('telegram.link.status') }}"
     data-start-url="{{ route('telegram.link.start') }}"
     data-test-url="{{ route('telegram.link.test') }}"
     data-unlink-url="{{ route('telegram.link.unlink') }}"
     data-token-active="{{ $tgHasToken ? '1' : '0' }}"
     data-linked="{{ $tgLinked ? '1' : '0' }}">
    <div class="card-body">

        <div id="telegram-alert"></div>

        <div id="telegram-body">
            @include('partials.telegram_connect_body', ['pegawai' => $tgPegawai])
        </div>

        <div id="telegram-token-container">
            @if($tgHasToken)
                @include('partials.telegram_token_box')
            @endif
        </div>
    </div>
</div>

<script>
(function () {
    var card = document.getElementById('card-telegram');
    if (!card || card.getAttribute('data-tg-init') === '1') return;
    card.setAttribute('data-tg-init', '1');

    var statusUrl  = card.getAttribute('data-status-url');
    var startUrl   = card.getAttribute('data-start-url');
    var testUrl    = card.getAttribute('data-test-url');
    var unlinkUrl  = card.getAttribute('data-unlink-url');

    var isLinked = card.getAttribute('data-linked') === '1';
    var tokenActive = card.getAttribute('data-token-active') === '1';
    var timer = null;
    var attempts = 0;
    var maxAttempts = 150; // ~5 menit @2 detik

    function ajaxHeaders() {
        return {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || ''
        };
    }

    function showAlert(type, msg) {
        var box = document.getElementById('telegram-alert');
        if (!box) return;
        box.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible fade show">' + msg +
            '<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
        setTimeout(function () { box.innerHTML = ''; }, 6000);
    }

    function stopPolling() {
        if (timer) { clearInterval(timer); timer = null; }
    }

    function renderBody(html) {
        var body = document.getElementById('telegram-body');
        if (body && html) body.innerHTML = html;
    }

    function hideTokenBox() {
        var box = document.getElementById('telegram-token-container');
        if (box) box.innerHTML = '';
        tokenActive = false;
        card.setAttribute('data-token-active', '0');
    }

    function applyStatus(data, opts) {
        opts = opts || {};
        if (!data) return;

        isLinked = !!data.linked;
        card.setAttribute('data-linked', isLinked ? '1' : '0');

        if (data.html) renderBody(data.html);

        if (isLinked) {
            hideTokenBox();
            stopPolling();
            if (opts.announce) {
                showAlert('success', '<span class="oi oi-check"></span> <b>Berhasil!</b> Akun Telegram Anda sudah terhubung.');
            }
        }
    }

    function fetchStatus(opts) {
        return fetch(statusUrl, { headers: ajaxHeaders(), cache: 'no-store' })
            .then(function (r) { return r.json(); })
            .then(function (data) { applyStatus(data, opts); return data; })
            .catch(function () { return null; });
    }

    function poll() {
        attempts++;
        if (attempts > maxAttempts) { stopPolling(); return; }
        fetchStatus({ announce: true });
    }

    function startPolling() {
        stopPolling();
        attempts = 0;
        timer = setInterval(poll, 2000);
        poll();
    }

    /* ---------- Aksi tanpa refresh ---------- */

    // Hubungkan ke Telegram
    card.addEventListener('click', function (e) {
        var btn = e.target.closest('a[href="' + startUrl + '"]');
        if (!btn) return;
        e.preventDefault();
        btn.disabled = true;

        fetch(startUrl, { headers: ajaxHeaders(), cache: 'no-store' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                btn.disabled = false;
                if (!data || !data.success) {
                    showAlert('danger', (data && data.message) || 'Gagal membuat token pairing.');
                    return;
                }
                var box = document.getElementById('telegram-token-container');
                if (box) box.innerHTML = data.token_html || '';
                tokenActive = true;
                card.setAttribute('data-token-active', '1');
                showAlert('info', '<span class="oi oi-key"></span> Token pairing dibuat. Buka Telegram dan kirim <code>/start ' + data.token + '</code>');
                startPolling();
            })
            .catch(function () {
                btn.disabled = false;
                showAlert('danger', 'Gagal menghubungi server. Coba lagi.');
            });
    });

    // Test koneksi
    card.addEventListener('click', function (e) {
        var btn = e.target.closest('a[href="' + testUrl + '"]');
        if (!btn) return;
        e.preventDefault();
        btn.disabled = true;

        fetch(testUrl, { headers: ajaxHeaders(), cache: 'no-store' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                btn.disabled = false;
                if (data && data.success) {
                    showAlert('success', '<span class="oi oi-check"></span> ' + (data.message || 'Test koneksi berhasil.'));
                } else {
                    showAlert('danger', (data && data.message) || 'Test koneksi gagal.');
                }
            })
            .catch(function () {
                btn.disabled = false;
                showAlert('danger', 'Gagal menghubungi server.');
            });
    });

    // Putuskan koneksi
    card.addEventListener('click', function (e) {
        var btn = e.target.closest('a[href="' + unlinkUrl + '"]');
        if (!btn) return;
        e.preventDefault();
        if (!confirm('Putuskan koneksi Telegram? Anda tidak akan menerima notifikasi lagi.')) return;

        btn.disabled = true;

        fetch(unlinkUrl, { headers: ajaxHeaders(), cache: 'no-store' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                btn.disabled = false;
                if (data && data.success) {
                    showAlert('warning', '<span class="oi oi-delete"></span> Koneksi Telegram berhasil diputus.');
                    fetchStatus();
                } else {
                    showAlert('danger', (data && data.message) || 'Gagal memutus koneksi.');
                }
            })
            .catch(function () {
                btn.disabled = false;
                showAlert('danger', 'Gagal menghubungi server.');
            });
    });

    /* ---------- Polling otomatis saat menunggu pairing ---------- */
    if (tokenActive && !isLinked) {
        startPolling();
    }

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden && !isLinked) fetchStatus({ announce: true });
    });
})();
</script>
