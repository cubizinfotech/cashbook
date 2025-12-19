<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .credentials {
            background: #f3f4f6;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .credentials-item {
            margin: 10px 0;
        }
        .credentials-label {
            font-weight: bold;
            color: #374151;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #0ea5e9;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to {{ $member->business->name }}!</h1>
    </div>
   @if ($isNewUser)
    <div class="content">
        <p>Hello {{ $member->name }},</p>

        <p>Your account has been created successfully. You can now access the Cash Book management system.</p>

        <div class="credentials">
            <h3 style="margin-top: 0; color: #111827;">Your Login Credentials:</h3>
            <div class="credentials-item">
                <span class="credentials-label">Email:</span> {{ $user->email }}
            </div>
            <div class="credentials-item">
                <span class="credentials-label">Password:</span> {{ $password }}
            </div>
        </div>

        <p><strong>Important:</strong> Please change your password after your first login for security purposes.</p>

        <div style="text-align: center;">
            <a href="{{ $loginUrl }}" class="button">Login to Your Account</a>
        </div>

        <p style="margin-top: 30px;">If you have any questions, please contact your administrator.</p>

        <p>Best regards,<br>{{ $member->business->name }} Team</p>
    </div>
 @else
        {{-- EXISTING USER MESSAGE --}}
        <p>You already have an account in our system.</p>

        <p>You have now been granted access to a new business:</p>
        <h3>{{ $member->business->name }}</h3>

        <p>You can log in using your existing email and password.</p>
    @endif
    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} {{ $member->business->name }}. All rights reserved.</p>
    </div>
</body>
</html>


