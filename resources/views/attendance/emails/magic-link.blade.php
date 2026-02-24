<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-In Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: #28a745;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .button:hover {
            background: #218838;
        }
        .alert {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏫 Attendance Check-In</h1>
        </div>

        <div class="content">
            <h2>Hello {{ $staffName }},</h2>

            <p>You have requested to check in for attendance. Click the button below to complete your check-in:</p>

            <div style="text-align: center;">
                <a href="{{ $magicLinkUrl }}" class="button">
                    ✅ Complete Check-In
                </a>
            </div>

            <div class="alert">
                <strong>⚠️⏰ Important:</strong> This link will expire in <strong>15 minutes</strong> (at {{ $expiresAt }}).
            </div>

            <div class="details">
                <table style="width: 100%;">
                    <tr>
                        <td style="color: #6c757d;">Date:</td>
                        <td><strong>{{ $checkInDate }}</strong></td>
                    </tr>
                    <tr>
                        <td style="color: #6c757d;">Time Requested:</td>
                        <td><strong>{{ $requestedAt }}</strong></td>
                    </tr>
                    <tr>
                        <td style="color: #6c757d;">Location:</td>
                        <td><strong>{{ $locationName }}</strong></td>
                    </tr>
                </table>
            </div>

            <p style="color: #6c757d; font-size: 14px;">
                <strong>Note:</strong> You must be within {{ $geofenceRadius }} meters of the school premises to successfully check in.
                Please ensure location services are enabled on your device.
            </p>

            <hr style="border: none; border-top: 1px solid #dee2e6; margin: 30px 0;">

            <p style="font-size: 13px; color: #6c757d;">
                If you did not request this check-in, please ignore this email or contact your administrator.
            </p>

            <p style="font-size: 13px; color: #6c757d;">
                If the button doesn't work, copy and paste this link:<br>
                <a href="{{ $magicLinkUrl }}" style="color: #28a745; word-break: break-all;">
                    {{ $magicLinkUrl }}
                </a>
            </p>
        </div>

        <div class="footer">
            <p>This is an automated message from {{ config('app.name') }}.</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
