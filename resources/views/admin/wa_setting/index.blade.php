@extends('layouts.global')

@section('title')
    Pengaturan WhatsApp
@endsection

@section('content')
<style>
    .wa-page { padding: 2rem 0; font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    
    .page-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .page-title {
        font-weight: 700;
        color: #1e293b;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .page-subtitle {
        color: #64748b;
        font-size: 0.9rem;
        margin-top: 0.25rem;
    }

    .wa-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .wa-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
    }
    .wa-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        background-color: #f8fafc;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }
    .wa-card-header .icon-wrap {
        width: 42px; height: 42px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; flex-shrink: 0;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .wa-card-header .header-text h5 {
        margin: 0 0 0.25rem 0;
        font-weight: 600;
        font-size: 1.05rem;
        color: #0f172a;
    }
    .wa-card-header .header-text p {
        margin: 0;
        font-size: 0.85rem;
        color: #64748b;
        line-height: 1.4;
    }
    .wa-card-body { padding: 1.5rem; }

    .form-label-custom {
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.4rem;
        display: block;
    }
    .form-control-custom {
        border-radius: 8px;
        font-size: 0.9rem;
        border: 1px solid #cbd5e1;
        padding: 0.5rem 0.75rem;
        transition: all 0.2s;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02) inset;
    }
    .form-control-custom:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        outline: none;
    }
    
    .input-group-custom .form-control-custom {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-right: 0;
    }
    .input-group-custom .btn {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-color: #cbd5e1;
        background: #f8fafc;
        color: #64748b;
    }
    .input-group-custom .btn:hover {
        background: #f1f5f9;
        color: #334155;
    }

    /* Switch Styles */
    .switch-container {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    .form-switch {
        padding-left: 3rem;
        margin-bottom: 0;
    }
    .form-switch .form-check-input {
        width: 2.75em;
        height: 1.4em;
        margin-left: -3rem;
        cursor: pointer;
    }
    .form-switch .form-check-label {
        font-size: 0.9rem;
        font-weight: 500;
        color: #334155;
        cursor: pointer;
        padding-top: 0.1rem;
    }
    .form-check-input:checked {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }

    /* Buttons */
    .btn-action {
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        padding: 0.5rem 1.25rem;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .btn-primary-custom {
        background-color: #2563eb;
        border-color: #2563eb;
        color: white;
    }
    .btn-primary-custom:hover {
        background-color: #1d4ed8;
        border-color: #1d4ed8;
        color: white;
    }
    .btn-success-custom {
        background-color: #059669;
        border-color: #059669;
        color: white;
    }
    .btn-success-custom:hover {
        background-color: #047857;
        border-color: #047857;
        color: white;
    }

    /* Alerts */
    .alert-custom {
        border-radius: 8px;
        font-size: 0.85rem;
        padding: 0.75rem 1rem;
        border: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
    }
    .alert-success-custom {
        background-color: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .alert-danger-custom {
        background-color: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    /* Cabang Order List */
    .cabang-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .cabang-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem 1rem;
        background: #ffffff;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        margin-bottom: 0.5rem;
        transition: all 0.2s;
        cursor: grab;
        user-select: none;
    }
    .cabang-item:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .cabang-item:active {
        cursor: grabbing;
        background: #f1f5f9;
        border-color: #94a3b8;
    }
    .cabang-item .drag-handle {
        color: #94a3b8;
        font-size: 1.1rem;
        flex-shrink: 0;
        cursor: grab;
    }
    .cabang-item .cabang-rank {
        width: 28px; height: 28px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 600;
        flex-shrink: 0;
        border: 1px solid #e2e8f0;
    }
    .cabang-item .cabang-name {
        flex: 1;
        font-size: 0.9rem;
        font-weight: 500;
        color: #1e293b;
    }
    .cabang-item .recipient-badge {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .cabang-item .btn-move-group {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }
    .cabang-item .btn-move {
        padding: 0.15rem 0.4rem;
        font-size: 0.65rem;
        border-radius: 4px;
        line-height: 1;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #64748b;
        cursor: pointer;
        transition: all 0.15s;
    }
    .cabang-item .btn-move:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    .cabang-item.dragging {
        opacity: 0.6;
        border: 2px dashed #94a3b8;
        background: #f8fafc;
    }

    /* Test Result Box */
    #test-result {
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1.25rem;
        font-size: 0.85rem;
        display: none;
        line-height: 1.5;
    }
</style>

<div class="container-fluid wa-page">

    <div class="page-header">
        <h4 class="page-title">
            <i class="fas fa-sliders-h text-primary"></i>
            Pengaturan WhatsApp
        </h4>
        <p class="page-subtitle">Kelola konfigurasi integrasi WhatsApp, jeda pengiriman, dan urutan prioritas cabang untuk fitur Broadcast Peraturan.</p>
    </div>

    <div class="row">
        {{-- ===== COLUMN LEFT ===== --}}
        <div class="col-lg-6">
            
            {{-- CARD 1: KONFIGURASI API --}}
            <div class="wa-card">
                <div class="wa-card-header">
                    <div class="icon-wrap text-warning">
                        <i class="fas fa-link"></i>
                    </div>
                    <div class="header-text">
                        <h5>Koneksi API WhatsApp</h5>
                        <p>Atur kredensial dan endpoint API. Perubahan akan langsung disimpan ke konfigurasi sistem (.env).</p>
                    </div>
                </div>
                <div class="wa-card-body">
                    @if(session('status_env'))
                        <div class="alert alert-success-custom">
                            <i class="fas fa-check-circle"></i>
                            {{ session('status_env') }}
                        </div>
                    @endif
                    @if(session('status_env_error'))
                        <div class="alert alert-danger-custom">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ session('status_env_error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.wa-setting.env') }}">
                        @csrf

                        <div class="form-group mb-3">
                            <label class="form-label-custom">URL Endpoint API</label>
                            <input type="url" name="wa_api_url" class="form-control form-control-custom"
                                value="{{ old('wa_api_url', $waApiUrl) }}" required placeholder="https://api.whatsapp.com/...">
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label-custom">API Key / Token</label>
                            <div class="input-group input-group-custom">
                                <input type="password" name="wa_api_code" id="api-code-input"
                                    class="form-control form-control-custom"
                                    value="{{ old('wa_api_code', $waApiCode) }}" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn" id="toggle-api-key" title="Tampilkan/Sembunyikan Token">
                                        <i class="fas fa-eye" id="eye-icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column gap-2 mb-4">
                            <div class="switch-container mb-2">
                                <div class="form-check form-switch w-100">
                                    <input class="form-check-input" type="checkbox" name="wa_enabled" id="wa_enabled"
                                        {{ $waEnabled ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="wa_enabled">
                                        Aktifkan Fitur WhatsApp Secara Global
                                    </label>
                                </div>
                            </div>
                            <div class="switch-container">
                                <div class="form-check form-switch w-100">
                                    <input class="form-check-input" type="checkbox" name="blast_enabled" id="blast_enabled"
                                        {{ $blastEnabled ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="blast_enabled">
                                        Aktifkan Broadcast Peraturan Otomatis
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100">
                            <i class="fas fa-save"></i> Simpan Konfigurasi Koneksi
                        </button>
                    </form>
                </div>
            </div>

            {{-- CARD 2: DELAY --}}
            <div class="wa-card">
                <div class="wa-card-header">
                    <div class="icon-wrap text-info">
                        <i class="fas fa-stopwatch"></i>
                    </div>
                    <div class="header-text">
                        <h5>Manajemen Jeda Waktu (Throttle)</h5>
                        <p>Mencegah pemblokiran oleh sistem WhatsApp dengan memberikan waktu istirahat antar pengiriman pesan.</p>
                    </div>
                </div>
                <div class="wa-card-body">
                    @if(session('status_delay'))
                        <div class="alert alert-success-custom">
                            <i class="fas fa-check-circle"></i>
                            {{ session('status_delay') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.wa-setting.delay') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group mb-4">
                                <label class="form-label-custom">Jeda Antar Individu</label>
                                <div class="input-group">
                                    <input type="number" name="delay_per_person" class="form-control form-control-custom"
                                        value="{{ $setting->delay_per_person }}" min="0" max="3600" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="background:#f8fafc; border-color:#cbd5e1; font-size:0.85rem">detik</span>
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block" style="font-size:0.75rem">Jeda setiap selesai mengirim 1 pesan.</small>
                            </div>

                            <div class="col-md-6 form-group mb-4">
                                <label class="form-label-custom">Jeda Transisi Cabang</label>
                                <div class="input-group">
                                    <input type="number" name="delay_per_cabang" class="form-control form-control-custom"
                                        value="{{ $setting->delay_per_cabang }}" min="0" max="3600" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="background:#f8fafc; border-color:#cbd5e1; font-size:0.85rem">detik</span>
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block" style="font-size:0.75rem">Jeda saat pindah pengiriman ke cabang berikutnya.</small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100">
                            <i class="fas fa-save"></i> Simpan Pengaturan Jeda
                        </button>
                    </form>
                </div>
            </div>
            
        </div>

        {{-- ===== COLUMN RIGHT ===== --}}
        <div class="col-lg-6">
            
            {{-- CARD 3: URUTAN CABANG --}}
            <div class="wa-card">
                <div class="wa-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-3">
                        <div class="icon-wrap text-primary">
                            <i class="fas fa-list-ol"></i>
                        </div>
                        <div class="header-text">
                            <h5>Prioritas Cabang Broadcast</h5>
                            <p>Atur cabang mana yang menerima pesan lebih dahulu.</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-light px-4 py-2 border-bottom d-flex justify-content-between align-items-center">
                    <span class="text-muted" style="font-size:0.8rem"><i class="fas fa-info-circle mr-1"></i> Tarik elemen untuk mengubah urutan</span>
                    <button class="btn btn-sm btn-primary-custom" id="btn-save-order" style="padding: 0.35rem 0.75rem; font-size:0.8rem">
                        <i class="fas fa-save"></i> Simpan Urutan
                    </button>
                </div>

                <div class="wa-card-body" style="max-height: 400px; overflow-y: auto; padding-top: 1rem;">
                    <div id="order-status" class="mb-3" style="display:none;"></div>
                    
                    <ul class="cabang-list" id="cabang-sortable">
                        @foreach($cabangList as $idx => $cabang)
                            <li class="cabang-item" data-id="{{ $cabang->id }}" draggable="true">
                                <span class="drag-handle" title="Tarik untuk memindahkan"><i class="fas fa-grip-vertical"></i></span>
                                <span class="cabang-rank">{{ $idx + 1 }}</span>
                                <span class="cabang-name">{{ $cabang->name }}</span>
                                @php $count = $recipientCountByCabang[$cabang->id] ?? 0; @endphp
                                <span class="recipient-badge"><i class="fas fa-users mr-1"></i> {{ $count }}</span>
                                <div class="btn-move-group">
                                    <button class="btn-move btn-up" type="button" title="Pindah ke Atas"><i class="fas fa-chevron-up"></i></button>
                                    <button class="btn-move btn-down" type="button" title="Pindah ke Bawah"><i class="fas fa-chevron-down"></i></button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- CARD 4: TEST KIRIM --}}
            <div class="wa-card">
                <div class="wa-card-header">
                    <div class="icon-wrap text-success">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div class="header-text">
                        <h5>Uji Coba Pengiriman</h5>
                        <p>Simulasi pengiriman pesan untuk memastikan konfigurasi API sudah benar.</p>
                    </div>
                </div>
                <div class="wa-card-body">
                    <div class="form-group mb-3">
                        <label class="form-label-custom">Nomor Ponsel Tujuan</label>
                        <input type="text" id="test-phone" class="form-control form-control-custom"
                            placeholder="Contoh: 08123456789" value="{{ env('WA_TEST_PHONE', '') }}">
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label-custom">Isi Pesan Uji Coba</label>
                        <textarea id="test-message" class="form-control form-control-custom" rows="3">Halo! Ini adalah pesan pengujian integrasi WhatsApp dari sistem SIKAP BPR Cianjur. Waktu: {{ now()->format('d/m/Y H:i:s') }}</textarea>
                    </div>
                    
                    <button type="button" id="btn-test-send" class="btn btn-success-custom w-100">
                        <i class="fas fa-paper-plane"></i> Kirim Pesan Uji Coba
                    </button>
                    
                    <div id="test-result"></div>
                </div>
            </div>
            
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ---- Toggle API Key Visibility ----
    const apiInput = document.getElementById('api-code-input');
    const eyeIcon  = document.getElementById('eye-icon');
    document.getElementById('toggle-api-key').addEventListener('click', function () {
        if (apiInput.type === 'password') {
            apiInput.type = 'text';
            eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            apiInput.type = 'password';
            eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });

    // ---- Drag-and-Drop for Cabang Sortable ----
    const list = document.getElementById('cabang-sortable');
    let dragItem = null;

    list.querySelectorAll('.cabang-item').forEach(item => {
        item.addEventListener('dragstart', () => {
            dragItem = item;
            setTimeout(() => item.classList.add('dragging'), 0);
        });
        item.addEventListener('dragend', () => {
            item.classList.remove('dragging');
            updateRanks();
            dragItem = null;
        });
        item.addEventListener('dragover', e => {
            e.preventDefault();
            const after = getDragAfterElement(list, e.clientY);
            if (after == null) {
                list.appendChild(dragItem);
            } else {
                list.insertBefore(dragItem, after);
            }
        });
    });

    function getDragAfterElement(container, y) {
        const items = [...container.querySelectorAll('.cabang-item:not(.dragging)')];
        return items.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
                return { offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    // ---- Up/Down Buttons ----
    list.addEventListener('click', function (e) {
        const upBtn   = e.target.closest('.btn-up');
        const downBtn = e.target.closest('.btn-down');

        if (upBtn) {
            const item = upBtn.closest('.cabang-item');
            const prev = item.previousElementSibling;
            if (prev) { list.insertBefore(item, prev); updateRanks(); }
        }
        if (downBtn) {
            const item = downBtn.closest('.cabang-item');
            const next = item.nextElementSibling;
            if (next) { list.insertBefore(next, item); updateRanks(); }
        }
    });

    function updateRanks() {
        list.querySelectorAll('.cabang-item').forEach((item, idx) => {
            item.querySelector('.cabang-rank').textContent = idx + 1;
        });
    }

    // ---- Save Order ----
    document.getElementById('btn-save-order').addEventListener('click', function () {
        const ids = [...list.querySelectorAll('.cabang-item')].map(el => parseInt(el.dataset.id));
        const btn = this;
        const statusEl = document.getElementById('order-status');

        btn.disabled = true;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        
        statusEl.style.display = 'none';

        fetch('{{ route("admin.wa-setting.cabang-order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ order: ids })
        })
        .then(r => r.json())
        .then(data => {
            statusEl.style.display = 'flex';
            if (data.success) {
                statusEl.className = 'alert-custom alert-success-custom';
                statusEl.innerHTML = '<i class="fas fa-check-circle"></i> Urutan cabang berhasil diperbarui.';
            } else {
                statusEl.className = 'alert-custom alert-danger-custom';
                statusEl.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Gagal menyimpan urutan.';
            }
        })
        .catch(() => {
            statusEl.style.display = 'flex';
            statusEl.className = 'alert-custom alert-danger-custom';
            statusEl.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan jaringan.';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            setTimeout(() => { statusEl.style.display = 'none'; }, 5000);
        });
    });

    // ---- Test Send ----
    document.getElementById('btn-test-send').addEventListener('click', function () {
        const phone   = document.getElementById('test-phone').value.trim();
        const message = document.getElementById('test-message').value.trim();
        const result  = document.getElementById('test-result');
        const btn     = this;

        if (!phone || !message) {
            result.className = 'alert-custom alert-danger-custom mt-3';
            result.style.display = 'flex';
            result.innerHTML = '<i class="fas fa-exclamation-circle"></i> Nomor ponsel dan isi pesan wajib diisi.';
            return;
        }

        btn.disabled = true;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sedang Mengirim...';
        result.style.display = 'none';

        fetch('{{ route("admin.wa-setting.test-send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ phone, message })
        })
        .then(r => r.json())
        .then(data => {
            result.style.display = 'flex';
            if (data.success) {
                result.className = 'alert-custom alert-success-custom mt-3';
                result.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
            } else {
                result.className = 'alert-custom alert-danger-custom mt-3';
                result.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + data.message;
            }
        })
        .catch(() => {
            result.style.display = 'flex';
            result.className = 'alert-custom alert-danger-custom mt-3';
            result.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan koneksi ke server.';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });

});
</script>
@endpush
@endsection
