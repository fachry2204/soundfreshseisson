<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Status pendaftaran diperbarui</title>
</head>
<body style="margin:0;background:#080808;font-family:Arial,Helvetica,sans-serif;color:#f7f7f5">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#080808">
    <tr><td align="center" style="padding:28px 14px">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;border:1px solid #34302c;border-radius:24px;overflow:hidden;background:#151515">
            <tr><td style="height:6px;background:linear-gradient(90deg,#ff5f14,#ff982f)"></td></tr>
            <tr><td style="padding:36px 34px 24px;text-align:center;background:radial-gradient(circle at top,#392012,#151515 62%)">
                <div style="display:inline-block;padding:11px 18px;border:1px solid #ff762080;border-radius:999px;background:#ff762018;color:#ff8a3d;font-size:13px;font-weight:bold;letter-spacing:1px">{{ $statusLabel }}</div>
                <p style="margin:24px 0 8px;color:#ff7620;font-size:12px;font-weight:bold;letter-spacing:3px;text-transform:uppercase">Original Sessions 2026</p>
                <h1 style="margin:0;color:#fff;font-size:32px;line-height:1.2">Status lagumu diperbarui 🎵</h1>
                <p style="margin:16px auto 0;max-width:530px;color:#b9b6b1;font-size:16px;line-height:1.7">Halo <strong style="color:#fff">{{ $applicant->full_name }}</strong>, ada perkembangan terbaru untuk lagu yang kamu daftarkan.</p>
            </td></tr>
            <tr><td style="padding:0 34px 18px">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px dashed #ff762080;border-radius:16px;background:#201811">
                    <tr><td style="padding:20px;text-align:center">
                        <div style="color:#9e9992;font-size:11px;font-weight:bold;letter-spacing:2px;text-transform:uppercase">Nomor pendaftaran</div>
                        <div style="margin-top:7px;color:#ff8a3d;font-family:Courier New,monospace;font-size:25px;font-weight:bold">{{ $submission->registration_number }}</div>
                    </td></tr>
                </table>
            </td></tr>
            <tr><td style="padding:0 34px 16px">
                <div style="padding:18px;border-radius:14px;background:#ffffff0b;color:#d5d2cd;font-size:15px;line-height:1.65">{{ $statusCopy }}</div>
            </td></tr>
            <tr><td style="padding:0 34px 12px"><h2 style="margin:0;color:#fff;font-size:20px">Detail pembaruan</h2></td></tr>
            <tr><td style="padding:0 34px 28px">
                @php
                    $rows = [
                        'Judul lagu' => $song?->title,
                        'Nama artis' => $song?->artist_name,
                        'Genre' => $song?->genre,
                        'Status terbaru' => $statusLabel,
                        'Alasan / catatan status' => $reason,
                        'Waktu pembaruan' => now()->timezone('Asia/Jakarta')->format('d/m/Y, H:i').' WIB',
                    ];
                @endphp
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0 8px">
                    @foreach($rows as $label => $value)
                    <tr>
                        <td style="width:34%;padding:12px 14px;border-radius:10px 0 0 10px;background:#1d1d1d;color:#8f8c87;font-size:12px;font-weight:bold;vertical-align:top">{{ $label }}</td>
                        <td style="padding:12px 14px;border-radius:0 10px 10px 0;background:#1d1d1d;color:#f5f4f1;font-size:14px;line-height:1.55;word-break:break-word">{{ $value ?: '-' }}</td>
                    </tr>
                    @endforeach
                </table>
            </td></tr>
            <tr><td style="padding:0 34px 32px;text-align:center">
                <a href="{{ $trackingUrl }}" style="display:inline-block;padding:15px 28px;border-radius:999px;background:#ff6a00;color:#111;text-decoration:none;font-size:15px;font-weight:bold">Lacak Pendaftaran →</a>
            </td></tr>
            <tr><td style="padding:22px 34px;border-top:1px solid #2c2c2c;text-align:center;color:#716e69;font-size:12px;line-height:1.6">Email ini dikirim otomatis saat tim mengubah status pendaftaran.<br>© 2026 SoundFresh.id × D'MASIV</td></tr>
        </table>
    </td></tr>
</table>
</body>
</html>
