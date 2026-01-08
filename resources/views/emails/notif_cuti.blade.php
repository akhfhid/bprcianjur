<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Notifikasi Pengajuan Cuti</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            font-family: Arial, Helvetica, sans-serif;
            color: #1f2937;
        }

        .wrapper {
            padding: 30px 12px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .header {
            background-color: #0A2A52;
            padding: 28px 24px;
            text-align: center;
            color: #ffffff;
        }

        .header img {
            max-width: 150px;
            margin-bottom: 12px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            letter-spacing: 1px;
        }

        .sub-header {
            background-color: #E9F2FF;
            padding: 14px 20px;
            font-size: 14px;
            color: #0A2A52;
            text-align: center;
        }

        .content {
            padding: 26px 24px;
            font-size: 14px;
            line-height: 1.7;
        }

        .content p {
            margin: 0 0 18px;
        }

        .card {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 18px;
            margin-bottom: 22px;
        }

        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px dashed #e5e7eb;
            font-size: 14px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .label {
            width: 40%;
            color: #0A2A52;
            font-weight: bold;
        }

        .value {
            width: 60%;
            color: #374151;
        }

        .cta {
            text-align: center;
            margin-top: 24px;
        }

        .cta a {
            background-color: #0D6EFD;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 6px;
            font-weight: bold;
            display: inline-block;
        }

        .footer {
            background: #f8fafc;
            padding: 18px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">

            <div class="header">
                <img src="https://i.ibb.co.com/mVRfgHBP/BPR-CIANJUR-LOGO-PNG-highres.png" alt="SIKAP">
                {{-- <img src="http:localhost/img/logo-perusahaan.png" alt="SIKAP"> --}}
                <h1>NOTIFIKASI PENGAJUAN CUTI</h1>
            </div>
            <div class="sub-header">
                Sistem Kepegawaian dan Peraturan (SIKAP)
            </div>
            <div class="content">
                <p>Yth. Bapak/Ibu,</p>
                <p>
                    Terdapat <strong>pengajuan cuti baru</strong> yang memerlukan perhatian dan tindak lanjut.
                    Berikut rincian pengajuan:
                </p>
                <div class="card">
                    <div class="info-row">
                        <div class="label">Nama Pegawai</div>
                        <div class="value">{{ $order->pegawai->name ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="label">Jenis Cuti</div>
                        <div class="value">{{ $order->jeniscuti }}</div>
                    </div>
                    <div class="info-row">
                        <div class="label">Periode</div>
                        <div class="value">
                            {{ \Carbon\Carbon::parse($order->tglawal)->format('d M Y') }}
                            s/d
                            {{ \Carbon\Carbon::parse($order->tglakhir)->format('d M Y') }}
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="label">Durasi</div>
                        <div class="value">{{ $order->jmlcuti }} Hari Kerja</div>
                    </div>
                    <div class="info-row">
                        <div class="label">Alasan</div>
                        <div class="value">{{ $order->alasan }}</div>
                    </div>
                </div>

                <p>
                    Mohon untuk segera melakukan peninjauan sesuai dengan ketentuan yang berlaku.
                </p>

                {{-- CTA optional --}}
                {{-- 
            <div class="cta">
                <a href="{{ url('/ordercuti') }}">Buka Aplikasi SIKAP</a>
            </div>
            --}}
            </div>
            <div class="footer">
                Email ini dikirim otomatis oleh Sistem SIKAP – Bagian SDM.<br>
                &copy; {{ date('Y') }} BPR Cianjur
            </div>

        </div>
    </div>
</body>

</html>
