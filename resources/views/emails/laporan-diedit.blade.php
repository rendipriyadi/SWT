<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perubahan Laporan Safety Walk and Talk | Siemens</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}?v={{ time() }}">
</head>

<body style="margin: 0; padding: 0; background-color: #ffffff; font-family: Arial, sans-serif; line-height: 1.6;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" bgcolor="#f0f0f0">
        <tr>
            <td align="center">
                <table width="850" cellpadding="0" cellspacing="0" border="0" align="center">
                    <tr>
                        <td style="background-color: #000028; padding: 32px; color: white;">
                            <h1 style="font-size: 20px; color: white; font-weight: bold; margin: 0;">SIEMENS</h1>
                            <p style="font-size: 8pt; color: white; margin: 28px 0;">Safety Walk and Talk</p>
                            <p
                                style="font-size: 20pt; font-weight: bold; letter-spacing: 1pt; color: white; margin: 0;">
                                Laporan Telah Diperbarui</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #ffffff; padding: 30px; font-size: 12px; color: black;">
                            <p style="font-weight: bold; margin: 0 0 10px;">
                                @if($laporan->penanggungJawab)
                                    Halo {{ $laporan->penanggungJawab->name }}
                                @else
                                    Halo {{ $laporan->area ? implode(', ', $laporan->area->penanggungJawabs->pluck('name')->toArray()) : '' }}
                                @endif
                            </p>
                            <p style="margin: 0 0 10px;">
                                Laporan yang ditugaskan kepada Anda telah diperbarui dengan detail sebagai berikut:
                            </p>
                            <ul style="padding-left: 20px; margin-top: 10px; color: black;">
                                <li><strong>Kategori:</strong> {{ $laporan->problemCategory->name ?? '-' }}</li>
                                <li><strong>Deskripsi:</strong> {{ \Illuminate\Support\Str::limit($laporan->deskripsi_masalah, 150, '...') }}</li>
                                <li><strong>Tenggat Waktu:</strong> {{ \Carbon\Carbon::parse($laporan->tenggat_waktu)->locale('en')->isoFormat('dddd, D MMMM YYYY') }}</li>
                                <li><strong>Area:</strong> {{ $laporan->area ? $laporan->area->name : '-' }}</li>
                                @if($laporan->penanggungJawab)
                                    <li><strong>Station:</strong> {{ $laporan->penanggungJawab->station }}</li>
                                @endif
                                <li><strong>Status:</strong> {{ $laporan->status }}</li>
                            </ul>

                            @if (count($perubahan) > 0)
                            <p style="margin: 10px 0; font-weight: bold;">Perubahan yang dilakukan:</p>
                            <table width="100%" cellpadding="8" cellspacing="0" style="border: 1px solid #ddd; border-collapse: collapse; margin-bottom: 20px;">
                                <tr style="background-color: #f5f5f5;">
                                    <th style="border: 1px solid #ddd; text-align: left;">Bidang</th>
                                    <th style="border: 1px solid #ddd; text-align: left;">Sebelumnya</th>
                                    <th style="border: 1px solid #ddd; text-align: left;">Sekarang</th>
                                </tr>
                                @foreach($perubahan as $field => $values)
                                <tr>
                                    <td style="border: 1px solid #ddd;">{{ $field }}</td>
                                    <td style="border: 1px solid #ddd;">{{ $values['old'] }}</td>
                                    <td style="border: 1px solid #ddd;">{{ $values['new'] }}</td>
                                </tr>
                                @endforeach
                            </table>
                            @endif

                            <p style="margin: 0 0 10px;">
                                Silakan kunjungi sistem Safety Walk and Talk untuk melihat lebih detail:
                                <a href="{{ url('/dashboard') }}" style="color: navy; text-decoration: underline;">Buka Aplikasi Safety Walk and Talk</a>
                            </p>
                            <p style="margin: 0 0 10px;">
                                Terima kasih atas perhatian dan kerjasamanya.<br />JANGAN MEMBALAS email ini karena tidak dikelola.
                            </p>
                            <p style="margin: 0;">Salam,<br /><br /><br />PT Siemens Indonesia</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #000028; color: white; padding: 10px 30px; font-size: 12px;">
                            &copy; Siemens Indonesia {{ date('Y') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>