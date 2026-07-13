<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
</head>
<body style="margin:0;padding:0;background-color:#f4f5f7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f5f7;padding:40px 16px;">
        <tr>
            <td align="center">
                <table width="480" cellpadding="0" cellspacing="0" style="max-width:480px;width:100%;">
                    <tr>
                        <td style="padding-bottom:24px;text-align:center;">
                            <h1 style="margin:0;font-size:20px;font-weight:700;color:#1e293b;">{{ config('app.name') }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#ffffff;border-radius:12px;padding:32px;box-shadow:0 1px 3px rgba(0,0,0,0.08);">
                            <h2 style="margin:0 0 8px;font-size:18px;font-weight:600;color:#1e293b;">Reset Your Password</h2>
                            <p style="margin:0 0 20px;font-size:14px;line-height:1.6;color:#64748b;">We received a request to reset the password for your account. Click the button below to choose a new password.</p>
                            <table cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                                <tr>
                                    <td align="center" style="background-color:#4f46e5;border-radius:8px;padding:12px 24px;">
                                        <a href="{{ route('password.reset', $token) }}" style="color:#ffffff;font-size:14px;font-weight:600;text-decoration:none;display:inline-block;">Reset Password</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:0 0 8px;font-size:13px;line-height:1.5;color:#94a3b8;">Or copy this link into your browser:</p>
                            <p style="margin:0 0 20px;font-size:12px;line-height:1.5;color:#64748b;word-break:break-all;">{{ route('password.reset', $token) }}</p>
                            <hr style="border:none;border-top:1px solid #e2e8f0;margin:20px 0;">
                            <p style="margin:0;font-size:12px;line-height:1.5;color:#94a3b8;">This link will expire in 1 hour. If you did not request a password reset, no further action is required.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:24px;text-align:center;">
                            <p style="margin:0;font-size:12px;color:#94a3b8;">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
