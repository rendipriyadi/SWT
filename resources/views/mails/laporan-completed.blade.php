<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Safety Walk and Talk Report Completed | Siemens</title>
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
                            <p style="font-size: 20pt; font-weight: bold; letter-spacing: 1pt; color: white; margin: 0;">
                                Report Completed</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #ffffff; padding: 30px; font-size: 12px; color: black;">
                            <p style="font-weight: bold; margin: 0 0 10px;">
                                @php
                                    $allNames = $toNames ?? [];
                                    $greeting = !empty($allNames) ? 'Hello, ' . implode(', ', $allNames) : 'Hello, Safety Team';
                                @endphp
                                {{ $greeting }}
                            </p>
                            <p style="margin: 0 0 10px;">
                                The Safety Walk and Talk report has been completed with the following details:
                            </p>
                            
                            <h3 style="margin: 20px 0 10px; color: #000028;">Report Details:</h3>
                            <ul style="padding-left: 20px; margin-top: 10px; color: black;">
                                <li><strong>Category:</strong> {{ $laporan->problemCategory->name ?? '-' }}</li>
                                <li><strong>Problem Description:</strong> {{ \Illuminate\Support\Str::limit($laporan->deskripsi_masalah, 150, '...') }}</li>
                                <li><strong>Area:</strong> {{ $laporan->area ? $laporan->area->name : '-' }}</li>
                                @if($laporan->penanggungJawab)
                                    <li><strong>Station:</strong> {{ $laporan->penanggungJawab->station }}</li>
                                    <li><strong>PIC:</strong> {{ $laporan->penanggungJawab->name }}</li>
                                @endif
                                <li><strong>Tenggat Waktu:</strong> {{ \Carbon\Carbon::parse($laporan->tenggat_waktu)->locale('en')->isoFormat('dddd, D MMMM YYYY') }}</li>
                                <li><strong>Status:</strong> <span style="color: #28a745; font-weight: bold;">{{ $laporan->status }}</span></li>
                            </ul>

                            @if($laporan->penyelesaian)
                            <h3 style="margin: 20px 0 10px; color: #28a745;">Completion Details:</h3>
                                <li><strong>Completion Date:</strong> {{ \Carbon\Carbon::parse($laporan->penyelesaian->Tanggal)->locale('en')->isoFormat('dddd, D MMMM YYYY') }}</li>
                                <li><strong>Completion Description:</strong> {{ $laporan->penyelesaian->deskripsi_penyelesaian }}</li>
                            </ul>
                            @endif

                            <p style="margin: 0 0 10px;">
                                Please visit the Safety Walk and Talk system to view complete details:
                            </p>
                            <p style="margin: 0 0 10px;">
                                <a href="{{ $fullUrl }}" style="display: inline-block; background-color: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; font-weight: bold;">View Report Details</a>
                            </p>
                            <p style="margin: 20px 0 10px;">
                                Thank you for your attention and cooperation in maintaining workplace safety.<br />
                                DO NOT REPLY to this email as it is not monitored.
                            </p>
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
