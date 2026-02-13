@extends('layouts.global')

@section('title')
    Data Cuti Pegawai
@endsection

@section('content')
    <style>
        /* --- UI Global & Page Styles --- */
        .page-header {
            background: #fff;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            border: 1px solid #edf2f7;
        }

        .filter-section {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border: 1px solid #e2e8f0;
        }

        .table-container {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.07);
            overflow: hidden;
        }

        .table thead th {
            background: #f1f5f9;
            color: #475569;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
            border: none;
            padding: 15px;
        }

        .table td {
            padding: 12px 15px;
            vertical-align: middle !important;
        }

        .btn-rounded {
            border-radius: 50px;
            padding-left: 1.2rem;
            padding-right: 1.2rem;
        }

        .form-control-custom {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .badge-count {
            background: #e0e7ff;
            color: #4338ca;
            font-weight: bold;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.85rem;
        }

        /* --- MODERN MODAL STYLES --- */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .modal-box {
            background: white;
            border-radius: 20px;
            width: 480px;
            max-width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            transform: scale(0.95);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-overlay.active .modal-box {
            transform: scale(1);
            opacity: 1;
        }

        .modal-header-custom {
            padding: 24px 30px 15px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .modal-icon-circle {
            width: 45px;
            height: 45px;
            background: #fee2e2;
            color: #dc2626;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .modal-title-text h5 {
            margin: 0;
            font-weight: 800;
            color: #1e293b;
        }

        .modal-body-custom {
            padding: 0 30px 20px;
        }

        /* Info Alert Boxes inside Modal */
        .info-alert {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #475569;
        }

        .warning-alert {
            background: #fff7ed;
            border: 1px solid #fdba74;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #9a3412;
        }

        /* --- CUSTOM CHECKBOX STYLING --- */
        .checkbox-container {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            cursor: pointer;
            user-select: none;
            padding: 12px;
            border-radius: 10px;
            transition: background 0.2s;
        }

        .checkbox-container:hover {
            background: #f1f5f9;
        }

        .checkbox-container input {
            display: none;
        }

        .custom-check {
            min-width: 22px;
            height: 22px;
            border: 2px solid #cbd5e1;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            background: white;
            margin-top: 2px;
        }

        .checkbox-container input:checked+.custom-check {
            background: #22c55e;
            border-color: #22c55e;
        }

        .custom-check::after {
            content: "✓";
            color: white;
            font-weight: bold;
            font-size: 14px;
            display: none;
        }

        .checkbox-container input:checked+.custom-check::after {
            display: block;
        }

        .checkbox-label {
            font-size: 13.5px;
            line-height: 1.5;
            color: #334155;
            font-weight: 500;
        }

        .btn-danger:disabled {
            background-color: #fda4af !important;
            border-color: #fda4af !important;
            cursor: not-allowed;
            box-shadow: none !important;
            transform: none !important;
        }

        .modal-footer-custom {
            background: #f8fafc;
            padding: 20px 30px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        /* --- TOAST STYLES (Existing) --- */
        #toastBox {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .toast-card {
            background: #fff;
            width: 380px;
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: flex-start;
            position: relative;
            overflow: hidden;
            border-left: 6px solid;
            animation: slideInRight 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .toast-card.success {
            border-left-color: #22c55e;
        }

        .toast-card.error {
            border-left-color: #ef4444;
        }

        .toast-icon-container {
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 24px;
            width: 24px;
            border-radius: 50%;
        }

        .toast-card.success .toast-icon-container {
            color: #22c55e;
            background: #dcfce7;
        }

        .toast-card.error .toast-icon-container {
            color: #ef4444;
            background: #fee2e2;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 700;
            font-size: 15px;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .toast-message {
            font-size: 13px;
            color: #64748b;
        }

        .toast-progress-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 100%;
        }

        .toast-card.success .toast-progress-bar {
            background-color: #22c55e;
        }

        .toast-card.error .toast-progress-bar {
            background-color: #ef4444;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOutRight {
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    </style>

    <div class="container-fluid pb-5">
        <div class="page-header mb-4">
            <h4 class="font-weight-bold text-dark mb-1">Data Cuti Pegawai</h4>
            <p class="text-muted mb-0">Daftar akumulasi pengajuan cuti berdasarkan pegawai</p>
        </div>

        <div class="filter-section shadow-sm">
            <form method="GET">
                <div class="row align-items-end">
                    <div class="col-md-5 mb-2 mb-md-0">
                        <label class="small font-weight-bold text-muted">CARI PEGAWAI</label>
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="form-control form-control-custom" placeholder="Masukkan nama pegawai...">
                    </div>
                    <div class="col-md-4 mb-2 mb-md-0">
                        <label class="small font-weight-bold text-muted">KANTOR / CABANG</label>
                        <select name="cabang" class="form-control form-control-custom">
                            <option value="">-- Semua Kantor --</option>
                            @foreach ($cabangs as $id => $name)
                                <option value="{{ $id }}" {{ request('cabang') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary btn-block btn-rounded shadow-sm">
                            <span class="oi oi-magnifying-glass mr-1"></span> Filter Data
                        </button>
                    </div>
                    <div class="col-md-12 mt-3 text-right">
                        <button type="button" class="btn btn-outline-danger btn-rounded shadow-sm"
                            onclick="openResetModal()">
                            <span class="oi oi-reload mr-1"></span> Reset Sisa Cuti Tahunan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-container shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="text-center">
                        <tr>
                            <th width="60">No</th>
                            <th class="text-left">Nama Pegawai</th>
                            <th class="text-left">Sisa Cuti</th>
                            <th class="text-left">Kantor / Cabang</th>
                            <th>Total Pengajuan</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cutis as $pegawaiId => $items)
                            @php
                                $pegawai = optional($items->first())->pegawai;
                                $cabang = optional($pegawai)->relCabang;
                            @endphp
                            <tr>
                                <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                                <td class="text-left">
                                    @if ($pegawai)
                                        <div class="font-weight-bold text-dark" style="font-size:0.95rem;">
                                            {{ $pegawai->name }}</div>
                                        <small class="text-muted text-uppercase" style="font-size:0.7rem;">ID:
                                            {{ $pegawai->id }}</small>
                                    @else
                                        <span class="text-muted">Pegawai tidak ditemukan</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($pegawai)
                                        <span class="scuti-display font-weight-bold" data-id="{{ $pegawai->id }}"
                                            data-value="{{ $pegawai->scuti ?? 0 }}"
                                            style="cursor:pointer; border-bottom:1px dashed #cbd5e1;">
                                            {{ $pegawai->scuti ?? 0 }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-left">
                                    <span><span class="oi oi-map-marker mr-1"></span>{{ $cabang->name ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-count">{{ $items->count() }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($pegawai)
                                        <a href="{{ route('cuti.pegawai', ['pegawai' => $pegawai->id, 'cabang' => request('cabang')]) }}"
                                            class="btn btn-info btn-sm btn-rounded shadow-sm px-3">Detail</a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Tidak ada data cuti ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="toastBox"></div>

    <div id="resetModal" class="modal-overlay" onclick="handleOverlayClick(event)">
        <div class="modal-box">
            <div class="modal-header-custom">
                <div class="modal-icon-circle">
                    <span class="oi oi-warning"></span>
                </div>
                <div class="modal-title-text">
                    <h5>Reset Sisa Cuti</h5>
                </div>
            </div>

            <div class="modal-body-custom">
                <div id="resetInfo" class="info-alert">
                </div>

                <div id="warningBox" class="warning-alert" style="display:none;">
                </div>

                <label class="checkbox-container">
                    <input type="checkbox" id="confirmReset">
                    <span class="custom-check"></span>
                    <span class="checkbox-label">
                        Saya memahami tindakan ini dan setuju untuk mereset seluruh sisa cuti pegawai.
                    </span>
                </label>
            </div>
            <div class="modal-footer-custom">
                <button onclick="closeResetModal()" class="btn btn-light btn-rounded font-weight-bold text-muted px-4">
                    Batal
                </button>
                <button id="btnSubmitReset" onclick="submitReset()"
                    class="btn btn-danger btn-rounded font-weight-bold px-4 shadow-sm" disabled>
                    Proses Reset Sekarang
                </button>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.body.addEventListener("click", function(e) {
            if (!e.target.classList.contains("scuti-display")) return;

            let span = e.target;
            let oldValue = span.dataset.value;
            let id = span.dataset.id;
            let isSaving = false;

            let input = document.createElement("input");
            input.type = "number";
            input.value = oldValue;
            input.className = "form-control form-control-sm text-center";
            input.style.width = "70px";
            input.style.display = "inline-block";
            input.min = "0";
            input.max = "12";

            input.addEventListener("input", function() {
                if (parseInt(this.value) > 12) this.value = 12;
                if (parseInt(this.value) < 0) this.value = 0;
            });

            span.replaceWith(input);
            input.focus();

            function saveValue() {
                if (isSaving) return;
                let newValue = input.value;
                if (parseInt(newValue) > 12) newValue = 12;
                if (newValue == oldValue) {
                    revertToSpan(oldValue);
                    return;
                }

                isSaving = true;
                fetch(`/pegawai/${id}/scuti`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            scuti: newValue
                        })
                    })
                    .then(r => r.json())
                    .then(res => {
                        revertToSpan(res.new_value);
                        showToast(res.message, "success", "Berhasil!");
                    })
                    .catch(() => {
                        isSaving = false;
                        revertToSpan(oldValue);
                        showToast("Gagal mengupdate data.", "error", "Gagal");
                    });
            }

            function revertToSpan(val) {
                let newSpan = document.createElement("span");
                newSpan.className = "scuti-display font-weight-bold";
                newSpan.dataset.id = id;
                newSpan.dataset.value = val;
                newSpan.style.cursor = "pointer";
                newSpan.style.borderBottom = "1px dashed #cbd5e1";
                newSpan.textContent = val;
                input.replaceWith(newSpan);
            }

            input.addEventListener("keydown", (e) => {
                if (e.key === "Enter") {
                    e.preventDefault();
                    input.blur();
                }
                if (e.key === "Escape") {
                    isSaving = true;
                    revertToSpan(oldValue);
                }
            });
            input.addEventListener("blur", saveValue);
        });
        const checkReset = document.getElementById("confirmReset");
        const btnReset = document.getElementById("btnSubmitReset");

        if (checkReset && btnReset) {
            checkReset.addEventListener("change", function() {
                // Jika dicentang, disabled = false. Jika tidak, disabled = true.
                btnReset.disabled = !this.checked;
            });
        }
    });

    function openResetModal() {
        let now = new Date();
        let newYear = new Date(now.getFullYear() + 1, 0, 1);
        let diff = Math.ceil((newYear - now) / (1000 * 60 * 60 * 24));
        let month = now.getMonth();

        let infoMsg =
            `Sistem akan mereset sisa cuti <b>seluruh pegawai</b> menjadi <b>12 hari</b>. Saat ini tersisa <b>${diff} hari</b> sebelum pergantian tahun baru.`;
        document.getElementById("resetInfo").innerHTML = infoMsg;

        let warningBox = document.getElementById("warningBox");
        if (month === 11) {
            warningBox.style.display = "block";
            warningBox.innerHTML =
                `<strong>⚠ Perhatian:</strong> Bulan Desember terdeteksi. Pastikan Anda melakukan reset ini sesuai dengan kebijakan tutup buku tahunan perusahaan.`;
        } else {
            warningBox.style.display = "block";
            warningBox.className = "warning-alert";
            warningBox.style.background = "#eff6ff";
            warningBox.style.borderColor = "#bfdbfe";
            warningBox.style.color = "#1e40af";
            warningBox.innerHTML =
                `<strong>💡 Info:</strong> Melakukan reset di luar bulan Desember akan menghapus akumulasi sisa cuti berjalan pegawai secara permanen.`;
        }

        const overlay = document.getElementById("resetModal");
        overlay.style.display = "flex";
        setTimeout(() => overlay.classList.add("active"), 10);
    }

    function closeResetModal() {
        const overlay = document.getElementById("resetModal");
        overlay.classList.remove("active");
        setTimeout(() => {
            overlay.style.display = "none";
            document.getElementById("confirmReset").checked = false;
            document.getElementById("btnSubmitReset").disabled = true; // Reset tombol jadi disabled lagi
        }, 300);
    }

    function handleOverlayClick(e) {
        if (e.target.id === "resetModal") closeResetModal();
    }

    function submitReset() {
        const check = document.getElementById("confirmReset");
        if (!check.checked) {
            showToast("Anda harus menyetujui persyaratan sebelum melanjutkan.", "error", "Persetujuan Diperlukan");
            return;
        }

        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = "Sedang Memproses...";

        fetch("{{ route('pegawai.reset.scuti') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            })
            .then(r => r.json())
            .then(res => {
                showToast(res.message, "success", "Reset Berhasil");
                closeResetModal();
                setTimeout(() => location.reload(), 1500);
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                showToast("Terjadi kesalahan sistem saat mencoba reset.", "error", "Reset Gagal");
            });
    }

    function showToast(message, type = "success", title = null, duration = 4000) {
        const box = document.getElementById("toastBox");
        const icons = {
            success: `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>`,
            error: `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="3" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>`
        };
        let toast = document.createElement("div");
        toast.className = `toast-card ${type}`;
        toast.innerHTML = `
            <div class="toast-icon-container">${icons[type]}</div>
            <div class="toast-content">
                <div class="toast-title">${title || (type === 'success' ? 'Sukses' : 'Error')}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
            <div class="toast-progress-bar"></div>
        `;
        box.appendChild(toast);
        let progressBar = toast.querySelector(".toast-progress-bar");
        progressBar.style.transition = `width ${duration}ms linear`;
        setTimeout(() => {
            progressBar.style.width = "0%";
        }, 10);
        setTimeout(() => {
            toast.style.animation = "fadeOutRight 0.4s forwards";
            toast.addEventListener('animationend', () => toast.remove());
        }, duration);
    }
</script>
