<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Your Password — {{ config('app.name') }}</title>
<style>
  body { margin: 0; padding: 0; background: #111111; font-family: Inter, Arial, sans-serif; color: #f5f5f5; }
  .wrapper { max-width: 560px; margin: 40px auto; background: #1a1a1a; border-radius: 12px; overflow: hidden; border: 1px solid #2d2d2d; }
  .header { background: #111111; padding: 32px 40px; text-align: center; border-bottom: 1px solid #2d2d2d; }
  .header img { height: 48px; }
  .body { padding: 40px; }
  h1 { font-size: 22px; font-weight: 600; color: #f5f5f5; margin: 0 0 12px; }
  p { font-size: 15px; line-height: 1.6; color: #9ca3af; margin: 0 0 20px; }
  .btn { display: inline-block; background: #f59e0b; color: #111111; font-weight: 700; font-size: 15px; padding: 14px 32px; border-radius: 8px; text-decoration: none; }
  .link-box { background: #111111; border-radius: 8px; padding: 14px 16px; margin-top: 20px; border: 1px solid #2d2d2d; }
  .link-box p { font-size: 12px; color: #6b7280; margin: 0 0 6px; }
  .link-box code { font-size: 12px; color: #9ca3af; word-break: break-all; }
  .footer { padding: 24px 40px; border-top: 1px solid #2d2d2d; }
  .footer p { font-size: 12px; color: #6b7280; margin: 0; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}">
  </div>
  <div class="body">
    <h1>Password Reset Request</h1>
    <p>Hi <strong style="color:#f5f5f5">{{ $user->full_name }}</strong>, we received a request to reset the password for your account. Click the button below to choose a new password.</p>
    <p style="text-align:center; margin: 32px 0;">
      <a href="{{ $resetUrl }}" class="btn">Reset My Password</a>
    </p>
    <p>This link will expire in <strong style="color:#f59e0b">60 minutes</strong>. If you did not request a password reset, you can safely ignore this email — your password will not change.</p>
    <div class="link-box">
      <p>If the button doesn't work, copy and paste this link into your browser:</p>
      <code>{{ $resetUrl }}</code>
    </div>
  </div>
  <div class="footer">
    <p>{{ config('app.name') }} · This is an automated message, please do not reply.</p>
  </div>
</div>
</body>
</html>
