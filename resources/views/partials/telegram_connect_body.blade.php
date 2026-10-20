@php
    $tgPegawai = isset($pegawai) ? $pegawai : null;
    $tgChatId = $tgPegawai && isset($tgPegawai->tg_chat_id) ? $tgPegawai->tg_chat_id : null;
    $tgLinked = !empty($tgChatId);
    $tgBot = env('TELEGRAM_BOT_USERNAME', '');
@endphp

<div class="d-flex justify-content-between align-items-center flex-wrap">
    <div>
        <h5 class="mb-1"><b><span class="oi oi-chat"></span> Notifikasi Telegram</b></h5>
        <small class="text-muted">Terima notifikasi cuti, permintaan download, dan peraturan baru langsung di Telegram.</small>
    </div>
    <div class="mt-2 mt-md-0">
        @if($tgLinked)
            <span class="badge badge-success p-2" style="font-size:0.9em;">
                <span class="oi oi-check"></span> Terhubung
            </span>
        @else
            <span class="badge badge-secondary p-2" style="font-size:0.9em;">
                <span class="oi oi-x"></span> Belum Terhubung
            </span>
        @endif
    </div>
</div>

<hr>

@if($tgLinked)
    <div class="row">
        <div class="col-md-6">
            <p class="mb-1"><b>Status:</b> <span class="text-success">Akun Telegram Anda sudah terhubung.</span></p>
            <p class="mb-0"><small class="text-muted">Chat ID: {{ $tgChatId }}</small></p>
        </div>
        <div class="col-md-6 text-md-right mt-3 mt-md-0">
            <a href="{{ route('telegram.link.test') }}" class="btn btn-primary btn-sm mr-1">
                <span class="oi oi-signal"></span> Test Koneksi
            </a>
            <a href="{{ route('telegram.link.unlink') }}" class="btn btn-outline-danger btn-sm"
               data-telegram-unlink="1"
               onclick="return confirm('Putuskan koneksi Telegram? Anda tidak akan menerima notifikasi lagi.')">
                <span class="oi oi-delete"></span> Putuskan Koneksi
            </a>
        </div>
    </div>
@else
    <div class="row align-items-center">
        <div class="col-md-8">
            <p class="mb-1"><b>Cara menghubungkan:</b></p>
            <ol class="mb-0 pl-3">
                <li>Klik tombol <b>Hubungkan ke Telegram</b>.</li>
                <li>Salin token yang muncul.</li>
                <li>Buka Telegram, cari
                    @if($tgBot)
                        <a href="https://t.me/{{ $tgBot }}" target="_blank"><b>&#64;{{ $tgBot }}</b></a>
                    @else
                        bot BPR Cianjur
                    @endif
                    lalu kirim <code>/start TOKEN</code>.
                </li>
                <li>Status di halaman ini akan berubah otomatis menjadi <b>Terhubung</b> tanpa perlu refresh.</li>
            </ol>
        </div>
        <div class="col-md-4 text-md-right mt-3 mt-md-0">
            <a href="{{ route('telegram.link.start') }}" class="btn btn-success">
                <span class="oi oi-link-intact"></span> Hubungkan ke Telegram
            </a>
        </div>
    </div>
@endif
