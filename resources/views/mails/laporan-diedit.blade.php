<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Safety Walk and Talk Report Updated | Siemens</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/static/favicon.ico') }}?v={{ time() }}">
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
                                Report Updated</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #ffffff; padding: 30px; font-size: 12px; color: black;">
                            <p style="font-weight: bold; margin: 0 0 10px;">
                                @if($laporan->penanggungJawab)
                                    Hello, {{ $laporan->penanggungJawab->name }}
                                @else
                                    Hello, {{ $laporan->area ? implode(', ', $laporan->area->penanggungJawabs->pluck('name')->toArray()) : '' }}
                                @endif
                            </p>
                            <p style="margin: 0 0 10px;">
                                The report assigned to you has been updated with the following details:
                            </p>
                            <ul style="padding-left: 20px; margin-top: 10px; color: black;">
                                <li><strong>Category:</strong> {{ $laporan->problemCategory->name ?? '-' }}</li>
                                <li><strong>Description:</strong> {{ \Illuminate\Support\Str::limit($laporan->deskripsi_masalah, 150, '...') }}</li>
                                <li><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($laporan->tenggat_waktu)->locale('en')->isoFormat('dddd, D MMMM YYYY') }}</li>
                                <li><strong>Area:</strong> {{ $laporan->area ? $laporan->area->name : '-' }}</li>
                                @if($laporan->penanggungJawab)
                                    <li><strong>Station:</strong> {{ $laporan->penanggungJawab->station }}</li>
                                @endif
                                <li><strong>Status:</strong> {{ $laporan->status }}</li>
                            </ul>

                            @if(count($perubahan) > 0)
                            <p style="margin: 10px 0; font-weight: bold;">Changes made:</p>
                            <table width="100%" cellpadding="8" cellspacing="0" style="border: 1px solid #ddd; border-collapse: collapse; margin-bottom: 20px;">
                                <tr style="background-color: #f5f5f5;">
                                    <th style="border: 1px solid #ddd; text-align: left;">Before</th>
                                    <th style="border: 1px solid #ddd; text-align: left;">After</th>
                                </tr>
                                @foreach($perubahan as $change)
                                <tr>
                                    <td style="border: 1px solid #ddd;">{{ $change['old'] }}</td>
                                    <td style="border: 1px solid #ddd;">{{ $change['new'] }}</td>
                                </tr>
                                @endforeach
                            </table>
                            @endif

                            <p style="margin: 0 0 10px;">
                                Please visit the Safety Walk and Talk system for more details:
                                <a href="{{ route('laporan.show', $encryptedId) }}" style="color: navy; text-decoration: underline;">Open Safety Walk and Talk Application</a>
                            </p>
                            <p style="margin: 0 0 10px;">
                                Thank you for your attention and cooperation.<br />DO NOT REPLY to this email as it is not monitored.
                            </p>
                            <p style="margin: 0;">Best regards,<br /><br /><br />PT Siemens Indonesia</p>
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
