@php
    $tgToken = isset($token) ? $token : session('telegram_link_token');
    $tgLinkUrl = isset($linkUrl) ? $linkUrl : session('telegram_link_url');
    $tgBotUsername = isset($botUsername) ? $botUsername : session('telegram_bot_username');
@endphp

<div class="alert alert-info mt-3" id="telegram-token-box">
    <b><span class="oi oi-key"></span> Token Pairing:</b>
    <div class="my-2" style="font-size:1.5em; letter-spacing:4px;"><b id="telegram-token-value">{{ $tgToken }}</b></div>

    @if($tgLinkUrl)
        <a href="{{ $tgLinkUrl }}" target="_blank" rel="noopener" class="btn btn-success btn-sm mb-2">
            <span class="oi oi-external-link"></span> Buka Telegram &amp; Hubungkan Otomatis
        </a>
    @endif

    <small class="d-block">
        Token berlaku <b>15 menit</b>. Buka Telegram, cari bot
        @if($tgBotUsername)
            <a href="https://t.me/{{ $tgBotUsername }}" target="_blank"><b>&#64;{{ $tgBotUsername }}</b></a>
        @else
            <b>bot BPR Cianjur</b>
        @endif
        lalu kirim pesan: <code>/start {{ $tgToken }}</code>
    </small>

    <div class="mt-2 small text-muted" id="telegram-waiting">
        <span class="oi oi-loop-circular"></span> Menunggu konfirmasi dari Telegram&hellip; status akan berubah otomatis.
    </div>
</div>
