<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lagu berhasil disubmit</title>
</head>
<body style="margin:0;background:#080808;font-family:Arial,Helvetica,sans-serif;color:#f7f7f5">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#080808">
    <tr><td align="center" style="padding:28px 14px">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;border:1px solid #34302c;border-radius:24px;overflow:hidden;background:#151515">
            <tr><td style="height:6px;background:linear-gradient(90deg,#ff5f14,#ff982f)"></td></tr>
            <tr><td style="padding:34px 34px 20px;text-align:center;background:radial-gradient(circle at top,#392012,#151515 62%)">
                <div style="display:inline-block;width:66px;height:66px;line-height:66px;border-radius:50%;background:#22a950;color:#fff;font-size:34px;font-weight:bold;box-shadow:0 0 0 10px rgba(34,169,80,.12)">✓</div>
                <p style="margin:26px 0 8px;color:#ff7620;font-size:12px;font-weight:bold;letter-spacing:3px;text-transform:uppercase">Original Sessions 2026</p>
                <h1 style="margin:0;color:#fff;font-size:34px;line-height:1.15">Lagu kamu berhasil disubmit! 🎧</h1>
                <p style="margin:16px auto 0;max-width:520px;color:#b9b6b1;font-size:16px;line-height:1.7">Halo <strong style="color:#fff">{{ $applicant->full_name }}</strong>, terima kasih sudah mempercayakan karyamu kepada Original Sessions. Data berikut sudah kami terima dan akan masuk proses verifikasi serta kurasi.</p>
            </td></tr>
            <tr><td style="padding:0 34px 26px">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px dashed #ff762080;border-radius:16px;background:#201811">
                    <tr><td style="padding:20px;text-align:center">
                        <div style="color:#9e9992;font-size:11px;font-weight:bold;letter-spacing:2px;text-transform:uppercase">Nomor pendaftaran</div>
                        <div style="margin-top:7px;color:#ff8a3d;font-family:Courier New,monospace;font-size:25px;font-weight:bold">{{ $submission->registration_number }}</div>
                        <div style="margin-top:7px;color:#807d78;font-size:12px">Simpan nomor ini untuk melacak status pendaftaran.</div>
                    </td></tr>
                </table>
            </td></tr>
            <tr><td style="padding:0 34px 12px"><h2 style="margin:0;color:#fff;font-size:20px">Detail yang kamu submit</h2></td></tr>
            <tr><td style="padding:0 34px 28px">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0 8px">
                    @php
                        $writers = collect($song?->songwriters ?? [])->map(function ($writer) {
                            $roles = ['composer' => 'Composer', 'author' => 'Author', 'composer_author' => 'Composer & Author'];
                            return ($writer['name'] ?? '-').' — '.($roles[$writer['role'] ?? ''] ?? ($writer['role'] ?? '-'));
                        })->join(', ');
                        $rows = [
                            'Nama peserta' => $applicant->full_name,
                            'Email' => $applicant->email,
                            'WhatsApp' => $applicant->whatsapp,
                            'Asal' => collect([$applicant->village, $applicant->district, $applicant->city, $applicant->province, $applicant->postal_code])->filter()->join(', '),
                            'Nama artis' => $song?->artist_name,
                            'Judul lagu' => $song?->title,
                            'Genre & bahasa' => collect([$song?->genre, $song?->language])->filter()->join(' · '),
                            'Tahun penciptaan' => $song?->creation_year,
                            'Songwriter' => $writers,
                            'Sosial media artis' => $song?->artist_social_url,
                            'Spotify artis' => $song?->artist_spotify_url ?: 'Tidak dicantumkan',
                            'Link video' => $links->get('video')?->url ?: 'Tidak dicantumkan',
                            'File terunggah' => $files->map(fn ($file) => $file->type === 'ktp' ? 'Dokumen identitas (tersimpan aman)' : $file->original_name)->join(', '),
                            'Waktu submit' => optional($submission->submitted_at)->timezone('Asia/Jakarta')->format('d M Y, H:i').' WIB',
                        ];
                    @endphp
                    @foreach($rows as $label => $value)
                    <tr>
                        <td style="width:34%;padding:12px 14px;border-radius:10px 0 0 10px;background:#1d1d1d;color:#8f8c87;font-size:12px;font-weight:bold;vertical-align:top">{{ $label }}</td>
                        <td style="padding:12px 14px;border-radius:0 10px 10px 0;background:#1d1d1d;color:#f5f4f1;font-size:14px;line-height:1.5;word-break:break-word">{{ $value ?: '-' }}</td>
                    </tr>
                    @endforeach
                </table>
            </td></tr>
            <tr><td style="padding:0 34px 30px;text-align:center">
                <div style="padding:18px;border-radius:14px;background:#ffffff0b;color:#aaa7a2;font-size:14px;line-height:1.65">Tim kami akan mendengarkan setiap karya dengan serius. Jika ada perkembangan status, kamu akan menerima email lanjutan dari sistem.</div>
                <a href="{{ $trackingUrl }}" style="display:inline-block;margin-top:22px;padding:15px 28px;border-radius:999px;background:#ff6a00;color:#111;text-decoration:none;font-size:15px;font-weight:bold">Lacak Pendaftaran →</a>
            </td></tr>
            <tr><td style="padding:22px 34px;border-top:1px solid #2c2c2c;text-align:center;color:#716e69;font-size:12px;line-height:1.6">Email ini dikirim otomatis oleh sistem Original Sessions.<br>© 2026 SoundFresh.id × D'MASIV</td></tr>
        </table>
    </td></tr>
</table>
</body>
</html>
