<div
    style="font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f7f6; padding: 40px 20px; margin: 0; -webkit-font-smoothing: antialiased;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0"
        style="max-width: 560px; margin: 0 auto; background: transparent;">

        <tr>
            <td align="center" style="padding-bottom: 24px;">
                <span
                    style="color: #0369a1; font-size: 22px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase;">
                    SIKAP
                </span>
                <div style="color: #64748b; font-size: 13px; font-weight: 500; margin-top: 4px;">
                    Sistem Kepegawaian dan Peraturan
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                    style="background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">

                    <tr>
                        <td
                            style="height: 6px; background: linear-gradient(90deg, #0284c7 0%, #38bdf8 100%); font-size: 0; line-height: 0;">
                            &nbsp;</td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 40px 32px 10px;">
                            <table cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td
                                        style="width: 64px; height: 64px; background: #f0f9ff; border: 4px solid #e0f2fe; border-radius: 50%; text-align: center; vertical-align: middle;">
                                        <img src="https://cdn-icons-png.flaticon.com/512/2912/2912761.png"
                                            alt="Document" width="28"
                                            style="display: block; margin: 0 auto; opacity: 0.8;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 16px 40px 0;">
                            <h1
                                style="margin: 0; font-size: 22px; font-weight: 700; color: #0f172a; letter-spacing: -0.5px;">
                                Peraturan Baru Diterbitkan
                            </h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 24px 40px 0;">
                            <p style="margin: 0 0 12px; font-size: 15px; color: #334155; line-height: 1.6;">
                                Halo <strong>{{ $pegawai->name }}</strong>,
                            </p>
                            <p style="margin: 0; font-size: 15px; color: #475569; line-height: 1.7;">
                                Terdapat dokumen peraturan atau Surat Keputusan (SK) baru yang telah dipublikasikan di
                                sistem. Berikut adalah rinciannya:
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 24px 40px 0;">
                            <table width="100%" cellpadding="12" cellspacing="0" border="0"
                                style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                                <tr>
                                    <td width="35%"
                                        style="color: #64748b; font-size: 13px; border-bottom: 1px solid #e2e8f0;">Nama
                                        Peraturan</td>
                                    <td width="65%"
                                        style="color: #0f172a; font-size: 14px; font-weight: 600; border-bottom: 1px solid #e2e8f0;">
                                        {{ $peraturan->name }}</td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; font-size: 13px; border-bottom: 1px solid #e2e8f0;">No.
                                        SK</td>
                                    <td
                                        style="color: #0f172a; font-size: 14px; font-weight: 600; border-bottom: 1px solid #e2e8f0;">
                                        {{ $peraturan->nosk }}</td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; font-size: 13px; border-bottom: 1px solid #e2e8f0;">Tgl
                                        SK</td>
                                    <td
                                        style="color: #0f172a; font-size: 14px; font-weight: 600; border-bottom: 1px solid #e2e8f0;">
                                        {{ $peraturan->tglsk }}</td>
                                </tr>
                                <tr>
                                    <td
                                        style="color: #64748b; font-size: 13px; @if(empty($cabangName)) border-bottom: none; @else border-bottom: 1px solid #e2e8f0; @endif">
                                        Tgl Berlaku</td>
                                    <td
                                        style="color: #0f172a; font-size: 14px; font-weight: 600; @if(empty($cabangName)) border-bottom: none; @else border-bottom: 1px solid #e2e8f0; @endif">
                                        {{ $peraturan->tgllaku }}</td>
                                </tr>
                                @if (!empty($cabangName))
                                    <tr>
                                        <td style="color: #64748b; font-size: 13px;">Cabang</td>
                                        <td style="color: #0f172a; font-size: 14px; font-weight: 600;">{{ $cabangName }}
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 32px 40px 0;">
                            <a href="{{ url('/login') }}"
                                style="display: inline-block; background-color: #0284c7; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none; padding: 14px 28px; border-radius: 8px; box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2);">
                                Lihat Detail Dokumen
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 32px 40px 0;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="height: 1px; background: #e2e8f0; font-size: 0; line-height: 0;">&nbsp;
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                    </tr>

                </table>
            </td>
        </tr>

        <tr>
            <td align="center" style="padding: 24px 0 0;">
                <p style="margin: 0 0 8px; font-size: 13px; font-weight: 600; color: #94a3b8;">
                    &copy; {{ date('Y') }} SIKAP
                </p>
                <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">
                    Email ini dibuat otomatis oleh sistem.<br>Mohon untuk tidak membalas email ini.
                </p>
            </td>
        </tr>

    </table>
</div>