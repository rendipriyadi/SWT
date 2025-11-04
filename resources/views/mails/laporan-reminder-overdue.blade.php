<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Urgent: Overdue Reports</title>
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
                                Urgent: Overdue Reports</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #ffffff; padding: 30px; font-size: 12px; color: black;">
                            <p style="font-weight: bold; margin: 0 0 10px;">
                                @if($pic)
                                    Hello, {{ $pic->name }}
                                @else
                                    Hello Team {{ $reports->first()->area->name ?? 'Area' }},
                                @endif
                            </p>
                            <p style="margin: 0 0 10px;">
                                @if($pic)
                                    This is an urgent reminder that you have <strong>{{ $reports->count() }} report(s)</strong> that are overdue 
                                    and require immediate attention.
                                @else
                                    This is an urgent reminder that there are <strong>{{ $reports->count() }} report(s)</strong> in your area that are overdue 
                                    and require immediate attention.
                                @endif
                            </p>
                            <p style="margin: 0 0 15px;">
                                Please complete the following report(s) as soon as possible:
                            </p>

                            @foreach($reports as $index => $laporan)
                            @php
                                $deadline = $laporan->tenggat_waktu ? \Carbon\Carbon::parse($laporan->tenggat_waktu)->startOfDay() : null;
                                $today = \Carbon\Carbon::now()->startOfDay();
                                $daysOverdue = $deadline ? (int) $deadline->diffInDays($today) : 0;
                            @endphp
                            <div style="background-color: #f5f5f5; padding: 15px; margin-bottom: 15px; border: 1px solid #ddd;">
                                <p style="margin: 0 0 8px; font-weight: bold;">
                                    Report #{{ $index + 1 }} - Overdue {{ $daysOverdue }} day(s)
                                </p>
                                <ul style="padding-left: 20px; margin: 0; color: black;">
                                    <li><strong>Category:</strong> {{ $laporan->problemCategory->name ?? '-' }}</li>
                                    <li><strong>Description:</strong> {{ \Illuminate\Support\Str::limit($laporan->deskripsi_masalah ?? '', 150, '...') }}</li>
                                    <li><strong>Original Deadline:</strong> {{ $deadline ? $deadline->locale('en')->isoFormat('dddd, D MMMM YYYY') : '-' }}</li>
                                    <li><strong>Days Overdue:</strong> {{ $daysOverdue }} day(s)</li>
                                    <li><strong>Area:</strong> {{ $laporan->area->name ?? '-' }}</li>
                                    <li><strong>PIC:</strong> 
                                        @if($laporan->penanggungJawab)
                                            {{ $laporan->penanggungJawab->name }}
                                        @elseif($laporan->area && $laporan->area->penanggungJawabs->isNotEmpty())
                                            {{ $laporan->area->penanggungJawabs->pluck('name')->join(', ') }}
                                        @else
                                            -
                                        @endif
                                    </li>
                                    <li><strong>Status:</strong> {{ $laporan->status ?? '-' }}</li>
                                </ul>
                                <p style="margin: 10px 0 0;">
                                    @php
                                        $encryptedId = (isset($encryptedIds) && is_array($encryptedIds) && isset($encryptedIds[$laporan->id])) 
                                            ? $encryptedIds[$laporan->id] 
                                            : encrypt($laporan->id);
                                    @endphp
                                    <a href="{{ route('laporan.show', $encryptedId) }}" 
                                       style="color: navy; text-decoration: underline;">
                                        View Report Details
                                    </a>
                                </p>
                            </div>
                            @endforeach

                            <p style="margin: 15px 0 10px;">
                                These reports have exceeded their deadlines. Please complete them immediately to ensure workplace safety compliance.
                            </p>

                            <p style="margin: 15px 0 10px;">
                                <a href="{{ route('laporan.index') }}" style="color: navy; text-decoration: underline;">Open Safety Walk and Talk Application</a>
                            </p>

                            <p style="margin: 15px 0 10px;">
                                Thank you for your immediate attention to this matter.<br />
                                DO NOT REPLY to this email as it is not monitored.
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
