@extends('layouts.global')

@section('title')
    Pengaturan WhatsApp
@endsection

@section('content')
<style>
    .wa-page { padding: 1.5rem 0; }
    .wa-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid #e8edf3;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .wa-card-header {
        padding: 1.1rem 1.5rem;
        border-bottom: 1px solid #f0f4f8;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .wa-card-header .icon-wrap {
        width: 38px; height: 38px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; flex-shrink: 0;
    }
    .wa-card-header h5 { margin: 0; font-weight: 700; font-size: 0.95rem; color: #1a2332; }
    .wa-card-header p  { margin: 0; font-size: 0.78rem; color: #64748b; }
    .wa-card-body { padding: 1.5rem; }

    .form-label-sm { font-size: 0.82rem; font-weight: 600; color: #374151; margin-bottom: 0.3rem; }
    .input-sm-custom {
        border-radius: 10px; font-size: 0.875rem; border: 1.5px solid #e2e8f0;
        padding: 0.45rem 0.75rem; transition: border-color 0.2s;
    }
    .input-sm-custom:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }

    /* Cabang Order List */
    .cabang-list { list-style: none; padding: 0; margin: 0; }
    .cabang-item {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.65rem 0.9rem; background: #f8fafc;
        border-radius: 10px; border: 1.5px solid #e2e8f0;
        margin-bottom: 0.5rem; transition: background 0.15s, border-color 0.15s;
        cursor: grab;
        user-select: none;
    }
    .cabang-item:active { cursor: grabbing; background: #eff6ff; border-color: #6366f1; }
    .cabang-item .drag-handle { color: #94a3b8; font-size: 1rem; flex-shrink: 0; }
    .cabang-item .cabang-rank {
        width: 24px; height: 24px; border-radius: 50%;
        background: #6366f1; color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.7rem; font-weight: 700; flex-shrink: 0;
    }
    .cabang-item .cabang-name { flex: 1; font-size: 0.85rem; font-weight: 600; color: #1e293b; }
    .cabang-item .recipient-badge {
        background: #ecfdf5; color: #166534; border: 1px solid #bbf7d0;
        border-radius: 20px; padding: 2px 10px; font-size: 0.72rem; font-weight: 600;
    }
    .cabang-item .btn-move {
        padding: 3px 8px; font-size: 0.7rem; border-radius: 6px; line-height: 1;
        border: 1px solid #e2e8f0; background: #fff; color: #475569; cursor: pointer;
    }
    .cabang-item .btn-move:hover { background: #6366f1; color: #fff; border-color: #6366f1; }

    /* Test Send */
    #test-result {
        border-radius: 10px; padding: 0.75rem 1rem; margin-top: 1rem;
        font-size: 0.85rem; display: none;
    }
    .test-success { background: #f0fdf4; border: 1px solid #86efac; color: #166534; }
    .test-error   { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; }

    /* Toggle Switch */
    .form-switch .form-check-input { width: 2.5em; height: 1.35em; }
    .form-check-input:checked { background-color: #6366f1; border-color: #6366f1; }

    .btn-save-wa {
        border-radius: 10px; font-size: 0.85rem; font-weight: 600;
        padding: 0.5rem 1.25rem;
    }
    .section-alert {
        border-radius: 10px; font-size: 0.83rem; padding: 0.6rem 1rem;
        border: 0; margin-bottom: 1rem;
    }

    .cabang-item.dragging { opacity: 0.5; border: 2px dashed #6366f1; }
    .cabang-item.drag-over { border-color: #6366f1; background: #eff6ff; }
</style>

<div class="container-fluid wa-page">

    <div class="d-flex align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold mb-1" style="color:#1a2332">⚙️ Pengaturan WhatsApp Blast</h4>
            <p class="text-muted mb-0" style="font-size:0.83rem">Konfigurasi sistem notifikasi WA untuk broadcast peraturan</p>
        </div>
    </div>

    <div class="row">

        {{-- ===== CARD 1: KONFIGURASI API ===== --}}
        <div class="col-lg-6">
            <div class="wa-card">
                <div class="wa-card-header">
                    <div class="icon-wrap" style="background:#fef3c7"><i class="fas fa-key" style="color:#d97706"></i></div>
                    <div>
                        <h5>Konfigurasi API WhatsApp</h5>
                        <p>Ubah URL endpoint dan API Key. Perubahan akan disimpan ke file .env</p>
                    </div>
                </div>
                <div class="wa-card-body">
                    @if(session('status_env'))
                        <div class="alert alert-success section-alert">✅ {{ session('status_env') }}</div>
                    @endif
                    @if(session('status_env_error'))
                        <div class="alert alert-danger section-alert">❌ {{ session('status_env_error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('admin.wa-setting.env') }}">
                        @csrf

                        <div class="form-group mb-3">
                            <label class="form-label-sm">URL API WhatsApp</label>
                            <input type="url" name="wa_api_url" class="form-control input-sm-custom"
                                value="{{ old('wa_api_url', $waApiUrl) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label-sm">API Key (Code)</label>
                            <div class="input-group">
                                <input type="password" name="wa_api_code" id="api-code-input"
                                    class="form-control input-sm-custom" style="border-radius:10px 0 0 10px;"
                                    value="{{ old('wa_api_code', $waApiCode) }}" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="toggle-api-key"
                                        style="border-radius:0 10px 10px 0; font-size:0.8rem; border:1.5px solid #e2e8f0; border-left:0;">
                                        <i class="fas fa-eye" id="eye-icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mb-3" style="gap:1.5rem">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="wa_enabled" id="wa_enabled"
                                    {{ $waEnabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="wa_enabled" style="font-size:0.82rem">
                                    Aktifkan WA
                                </label>
                            </div>
                            <div class="form-check form-switch ml-3">
                                <input class="form-check-input" type="checkbox" name="blast_enabled" id="blast_enabled"
                                    {{ $blastEnabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="blast_enabled" style="font-size:0.82rem">
                                    Aktifkan Blast Peraturan
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning btn-save-wa">
                            <i class="fas fa-save mr-1"></i> Simpan Konfigurasi
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ===== CARD 2: DELAY ===== --}}
        <div class="col-lg-6">
            <div class="wa-card">
                <div class="wa-card-header">
                    <div class="icon-wrap" style="background:#ede9fe"><i class="fas fa-clock" style="color:#7c3aed"></i></div>
                    <div>
                        <h5>Pengaturan Delay / Jeda</h5>
                        <p>Atur jeda pengiriman agar tidak kena rate-limit API WA</p>
                    </div>
                </div>
                <div class="wa-card-body">
                    @if(session('status_delay'))
                        <div class="alert alert-success section-alert">✅ {{ session('status_delay') }}</div>
                    @endif

                    <form method="POST" action="{{ route('admin.wa-setting.delay') }}">
                        @csrf

                        <div class="form-group mb-3">
                            <label class="form-label-sm">
                                Delay per Orang
                                <span class="text-muted font-weight-normal">(detik)</span>
                            </label>
                            <input type="number" name="delay_per_person" class="form-control input-sm-custom"
                                value="{{ $setting->delay_per_person }}" min="0" max="3600" required>
                            <small class="text-muted">Jeda setelah setiap pesan dikirim ke satu orang</small>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label-sm">
                                Delay per Cabang
                                <span class="text-muted font-weight-normal">(detik)</span>
                            </label>
                            <input type="number" name="delay_per_cabang" class="form-control input-sm-custom"
                                value="{{ $setting->delay_per_cabang }}" min="0" max="3600" required>
                            <small class="text-muted">Jeda tambahan setelah seluruh penerima di satu cabang selesai</small>
                        </div>

                        <button type="submit" class="btn btn-purple btn-save-wa"
                            style="background:#7c3aed;color:#fff;border:0">
                            <i class="fas fa-save mr-1"></i> Simpan Delay
                        </button>
                    </form>
                </div>
            </div>

            {{-- ===== CARD 4: TEST KIRIM ===== --}}
            <div class="wa-card">
                <div class="wa-card-header">
                    <div class="icon-wrap" style="background:#dcfce7"><i class="fab fa-whatsapp" style="color:#16a34a; font-size:1.1rem"></i></div>
                    <div>
                        <h5>Test Kirim Pesan WA</h5>
                        <p>Kirim pesan percobaan ke nomor HP custom</p>
                    </div>
                </div>
                <div class="wa-card-body">
                    <div class="form-group mb-3">
                        <label class="form-label-sm">Nomor HP Penerima</label>
                        <input type="text" id="test-phone" class="form-control input-sm-custom"
                            placeholder="Contoh: 08123456789 atau 6281234..." value="{{ env('WA_TEST_PHONE', '') }}">
                        <small class="text-muted">Format 08xxx / 628xxx / +628xxx diterima</small>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label-sm">Pesan</label>
                        <textarea id="test-message" class="form-control input-sm-custom" rows="3"
                            placeholder="Tulis pesan test di sini...">Halo! Ini adalah pesan test dari sistem SIKAP BPR Cianjur. Waktu: {{ now()->format('d/m/Y H:i:s') }}</textarea>
                    </div>
                    <button type="button" id="btn-test-send" class="btn btn-success btn-save-wa">
                        <i class="fas fa-paper-plane mr-1"></i> Kirim Test
                    </button>
                    <div id="test-result" class="mt-3"></div>
                </div>
            </div>
        </div>

    </div>

    {{-- ===== CARD 3: URUTAN CABANG ===== --}}
    <div class="wa-card">
        <div class="wa-card-header">
            <div class="icon-wrap" style="background:#e0f2fe"><i class="fas fa-sort-amount-down" style="color:#0284c7"></i></div>
            <div>
                <h5>Urutan Prioritas Cabang Blast</h5>
                <p>Seret & lepas, atau gunakan tombol Naik/Turun untuk mengatur urutan cabang mana yang dikirimi WA lebih dulu</p>
            </div>
            <div class="ml-auto">
                <button class="btn btn-primary btn-save-wa" id="btn-save-order">
                    <i class="fas fa-save mr-1"></i> Simpan Urutan
                </button>
                <span id="order-status" class="ml-2" style="font-size:0.8rem; color:#64748b;"></span>
            </div>
        </div>
        <div class="wa-card-body">
            <ul class="cabang-list" id="cabang-sortable">
                @foreach($cabangList as $idx => $cabang)
                    <li class="cabang-item" data-id="{{ $cabang->id }}" draggable="true">
                        <span class="drag-handle"><i class="fas fa-grip-vertical"></i></span>
                        <span class="cabang-rank">{{ $idx + 1 }}</span>
                        <span class="cabang-name">{{ $cabang->name }}</span>
                        @php $count = $recipientCountByCabang[$cabang->id] ?? 0; @endphp
                        <span class="recipient-badge">{{ $count }} penerima</span>
                        <button class="btn-move btn-up" type="button" title="Naik"><i class="fas fa-chevron-up"></i></button>
                        <button class="btn-move btn-down" type="button" title="Turun"><i class="fas fa-chevron-down"></i></button>
                    </li>
                @endforeach
            </ul>
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
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';

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
            if (data.success) {
                statusEl.textContent = '✅ Urutan tersimpan!';
                statusEl.style.color = '#16a34a';
            } else {
                statusEl.textContent = '❌ Gagal menyimpan!';
                statusEl.style.color = '#dc2626';
            }
        })
        .catch(() => {
            statusEl.textContent = '❌ Koneksi error.';
            statusEl.style.color = '#dc2626';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan Urutan';
            setTimeout(() => { statusEl.textContent = ''; }, 4000);
        });
    });

    // ---- Test Send ----
    document.getElementById('btn-test-send').addEventListener('click', function () {
        const phone   = document.getElementById('test-phone').value.trim();
        const message = document.getElementById('test-message').value.trim();
        const result  = document.getElementById('test-result');
        const btn     = this;

        if (!phone || !message) {
            result.className = 'test-error';
            result.style.display = 'block';
            result.innerHTML = '❌ Nomor HP dan pesan tidak boleh kosong.';
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Mengirim...';
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
            result.style.display = 'block';
            if (data.success) {
                result.className = 'test-success';
                result.innerHTML = '✅ ' + data.message;
            } else {
                result.className = 'test-error';
                result.innerHTML = '❌ ' + data.message;
            }
        })
        .catch(() => {
            result.style.display = 'block';
            result.className = 'test-error';
            result.innerHTML = '❌ Terjadi kesalahan koneksi.';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane mr-1"></i> Kirim Test';
        });
    });

});
</script>
@endpush
@endsection
