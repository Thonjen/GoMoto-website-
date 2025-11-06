<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Your Email</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:#f6f6f6; margin:0; padding:20px;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff; border-radius:8px; overflow:hidden;">
                    <tr>
                        <td style="padding:24px; text-align:center; background:#2b2f36;">
                            @if(!empty($logo))
                                <img src="{{ $logo }}" alt="GoMoto" style="max-width:240px; height:auto; display:block; margin:0 auto 8px;" />
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px; color:#333333;">
                            <h1 style="font-size:20px; margin:0 0 12px;">Hello!</h1>

                            <p style="margin:0 0 16px; line-height:1.5; color:#555555;">
                                Please click the button below to verify your email address.
                            </p>

                            <p style="text-align:center; margin:24px 0;">
                                <a href="{{ $verificationUrl }}" style="background:#1f6feb; color:#ffffff; text-decoration:none; padding:12px 20px; border-radius:6px; display:inline-block; font-weight:600;">
                                    Verify Email Address
                                </a>
                            </p>

                            <p style="margin:0 0 12px; color:#666666;">If you did not create an account, no further action is required.</p>

                            <p style="margin:24px 0 0; color:#666666;">Regards,<br>GOMOTO</p>

                            <hr style="border:none; border-top:1px solid #eeeeee; margin:24px 0;" />

                            <p style="font-size:12px; color:#999999;">
                                If you're having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser:
                                <br>
                                <a href="{{ $verificationUrl }}" style="color:#1f6feb; word-break:break-all;">{{ $verificationUrl }}</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>