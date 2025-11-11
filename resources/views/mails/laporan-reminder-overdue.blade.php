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
                                    Hello Team {{ $areaName }},
                                @endif
                            </p>
                            <p style="margin: 0 0 10px;">
                                @if($pic)
                                    This is an urgent reminder that you have <strong>{{ count($reportsData) }} report(s)</strong> that are overdue 
                                    and require immediate attention.
                                @else
                                    This is an urgent reminder that there are <strong>{{ count($reportsData) }} report(s)</strong> in your area that are overdue 
                                    and require immediate attention.
                                @endif
                            </p>
                            <p style="margin: 0 0 15px;">
                                Please complete the following report(s) as soon as possible:
                            </p>

                            @foreach($reportsData as $index => $report)
                            <div style="background-color: #f5f5f5; padding: 15px; margin-bottom: 15px; border: 1px solid #ddd;">
                                <p style="margin: 0 0 8px; font-weight: bold;">
                                    Report #{{ $index + 1 }} - Overdue {{ $report['days_overdue'] }} day(s)
                                </p>
                                <ul style="padding-left: 20px; margin: 0; color: black;">
                                    <li><strong>Category:</strong> {{ $report['category'] }}</li>
                                    <li><strong>Description:</strong> {{ \Illuminate\Support\Str::limit($report['description'] ?? '', 150, '...') }}</li>
                                    <li><strong>Original Deadline:</strong> {{ $report['deadline_formatted'] }}</li>
                                    <li><strong>Days Overdue:</strong> {{ $report['days_overdue'] }} day(s)</li>
                                    <li><strong>Area:</strong> {{ $report['area'] }}</li>
                                    <li><strong>PIC:</strong> {{ $report['pic'] }}</li>
                                    <li><strong>Status:</strong> {{ $report['status'] }}</li>
                                </ul>
                                <p style="margin: 0 0 10px;">
                                    Please visit the Safety Walk and Talk system to view more details and complete this report:
                                </p>
                                <p style="margin: 0 0 10px;">
                                    <a href="{{ $report['url'] }}" 
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
