<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Telegram Pulse API
    |--------------------------------------------------------------------------
    | Digunakan untuk mengirim pesan/notifikasi ke Telegram melalui server
    | Telegram Pulse (bot-tele).
    |
    */

    'base_url' => env('TELEGRAM_PULSE_URL', 'http://192.168.21.24:5173'),

    'api_key' => env('TELEGRAM_PULSE_API_KEY', ''),

    'bot_username' => env('TELEGRAM_BOT_USERNAME', 'BPR_CIANJUR_BOT'),

    /*
    |--------------------------------------------------------------------------
    | Kunci Verifikasi Tautan Telegram (link-verify)
    |--------------------------------------------------------------------------
    | API key yang dikirim server bot Telegram pada header X-API-KEY saat
    | memverifikasi token pairing ke endpoint /api/v1/telegram/link-verify.
    | Nilai ini harus sama dengan API_KEY yang dipakai server bot.
    | Kosongkan untuk menonaktifkan pengecekan key (tidak disarankan).
    |
    */

    'verify_api_key' => env('TELEGRAM_BOT_VERIFY_KEY', 'tg_live_d1826e53_dcb6430572136cd73ec107bd1ae89080b34a182d'),

    /*
    |--------------------------------------------------------------------------
    | Masa Berlaku Token Pairing (menit)
    |--------------------------------------------------------------------------
    */

    'token_ttl_minutes' => (int) env('TELEGRAM_LINK_TOKEN_TTL', 15),

    /*
    |--------------------------------------------------------------------------
    | Aturan Blast Notifikasi Peraturan Baru (Telegram)
    |--------------------------------------------------------------------------
    | Pengiriman dikelompokkan per cabang. Dalam satu detik maksimal
    | blast_batch_size pesan, lalu jeda blast_batch_delay detik sebelum
    | batch berikutnya. Nilai default 15 pesan/detik + jeda 1 detik agar
    | tetap aman di bawah batas Telegram (20 pesan/detik).
    */

    'blast_batch_size' => (int) env('TELEGRAM_BLAST_BATCH_SIZE', 15),

    'blast_batch_delay' => (int) env('TELEGRAM_BLAST_BATCH_DELAY', 1),

];
