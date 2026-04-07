<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Reset Password</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:26px 10px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;background:#ffffff;border-radius:14px;border:1px solid #dbe3ec;overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#0f766e 0%,#0c4a6e 100%);padding:20px 24px;color:#ffffff;">
                            <div style="font-size:19px;font-weight:700;letter-spacing:.2px;">{{ $systemName }}</div>
                            <div style="font-size:13px;opacity:.92;margin-top:6px;">{{ $companyName }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 12px 0;font-size:15px;">Halo <strong>{{ $namaUser }}</strong>,</p>
                            <p style="margin:0 0 14px 0;font-size:14px;line-height:1.7;color:#334155;">
                                Anda meminta reset password akun. Gunakan kode berikut untuk melanjutkan proses reset password:
                            </p>

                            <div style="display:inline-block;background:#ecfeff;border:1px dashed #0f766e;border-radius:12px;padding:10px 16px;margin:4px 0 16px 0;">
                                <span style="font-size:28px;letter-spacing:7px;font-weight:800;color:#0f766e;">{{ $code }}</span>
                            </div>

                            <p style="margin:0 0 10px 0;font-size:13px;color:#475569;line-height:1.7;">
                                Kode berlaku selama <strong>{{ $expiredMinutes }} menit</strong>.
                            </p>
                            <p style="margin:0 0 16px 0;font-size:13px;color:#475569;line-height:1.7;">
                                Demi keamanan, jangan berikan kode ini kepada siapa pun.
                            </p>

                            <div style="border-top:1px solid #e2e8f0;padding-top:14px;margin-top:8px;font-size:12px;color:#64748b;line-height:1.7;">
                                Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini.
                                <br>
                                Butuh bantuan? Hubungi: {{ $supportEmail }}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
