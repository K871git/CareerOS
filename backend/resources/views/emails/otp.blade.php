<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your CareerOS OTP</title>
    <style>
        body { margin: 0; padding: 0; background: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .wrapper { max-width: 480px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #4f46e5, #7c3aed); padding: 32px 40px; text-align: center; }
        .header-brand { color: #ffffff; font-size: 22px; font-weight: 800; letter-spacing: -0.5px; }
        .body { padding: 40px; }
        .title { font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 8px; }
        .subtitle { font-size: 14px; color: #6b7280; margin: 0 0 32px; line-height: 1.5; }
        .otp-box { background: #f5f3ff; border: 2px dashed #a5b4fc; border-radius: 10px; padding: 24px; text-align: center; margin-bottom: 28px; }
        .otp-code { font-size: 42px; font-weight: 800; letter-spacing: 0.25em; color: #4f46e5; font-family: 'Courier New', monospace; }
        .otp-label { font-size: 12px; color: #9ca3af; margin-top: 8px; text-transform: uppercase; letter-spacing: 0.08em; }
        .expiry { font-size: 13px; color: #ef4444; font-weight: 500; text-align: center; margin-bottom: 24px; }
        .note { font-size: 12px; color: #9ca3af; line-height: 1.6; border-top: 1px solid #f3f4f6; padding-top: 20px; }
        .footer { background: #f9fafb; padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="header-brand">CareerOS</div>
        </div>
        <div class="body">
            <p class="title">Your one-time login code</p>
            <p class="subtitle">Use this code to sign in to your CareerOS account. Do not share it with anyone.</p>

            <div class="otp-box">
                <div class="otp-code">{{ $code }}</div>
                <div class="otp-label">One-time password</div>
            </div>

            <p class="expiry">This code expires in 5 minutes.</p>

            <p class="note">
                If you did not request this code, you can safely ignore this email.
                Someone may have typed your email address by mistake.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} CareerOS. All rights reserved.
        </div>
    </div>
</body>
</html>
