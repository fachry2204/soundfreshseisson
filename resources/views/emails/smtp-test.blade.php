<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;background:#080d19;font-family:Arial,sans-serif;color:#f8fafc;padding:32px 14px">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0"><tr><td align="center">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px;background:#111827;border:1px solid #263044;border-radius:22px;overflow:hidden">
        <tr><td style="height:7px;background:linear-gradient(90deg,#ff4d1c,#ff8a22)"></td></tr>
        <tr><td style="padding:42px 40px">
            <div style="display:inline-block;background:#173a31;color:#4ade80;border-radius:999px;padding:8px 13px;font-size:12px;font-weight:bold">✓ SMTP TERHUBUNG</div>
            <h1 style="font-size:30px;line-height:1.2;margin:22px 0 12px">Test email berhasil diterima.</h1>
            <p style="color:#aeb8c8;font-size:16px;line-height:1.7;margin:0">Konfigurasi SMTP <strong style="color:#fff">{{ $senderName }}</strong> sudah dapat mengirim email melalui sistem Original Sessions.</p>
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:28px;background:#0b1220;border-radius:14px">
                <tr><td style="padding:18px;color:#748196;font-size:12px">EMAIL TUJUAN<br><strong style="display:block;color:#fff;font-size:15px;margin-top:7px">{{ $recipient }}</strong></td></tr>
                <tr><td style="padding:0 18px 18px;color:#748196;font-size:12px">WAKTU PENGUJIAN<br><strong style="display:block;color:#fff;font-size:15px;margin-top:7px">{{ now()->timezone(config('app.timezone'))->format('d M Y, H:i') }}</strong></td></tr>
            </table>
            <p style="color:#657187;font-size:12px;line-height:1.6;margin:28px 0 0">Email ini dikirim otomatis dari menu Setting Admin. Jika Anda menerima email ini, SMTP Gateway telah berfungsi.</p>
        </td></tr>
    </table>
</td></tr></table>
</body>
</html>
