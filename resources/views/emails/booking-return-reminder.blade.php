<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Return Reminder</title>

    <style>
        /* Dark mode styling for clients that support prefers-color-scheme */
        @media (prefers-color-scheme: dark) {
            body {
                background: #1f2024 !important;
                color: #e5e7eb !important;
            }

            table[role="presentation"] table {
                background: #2b2d31 !important;
                color: #e5e7eb !important;
                background-image: none !important;
            }

            h1,
            h2,
            p,
            a,
            strong {
                color: #e5e7eb !important;
            }

            a.button {
                background: #535862 !important;
                color: #ffffff !important;
            }

            td.footer {
                background: #232428 !important;
                color: #a0a0a0 !important;
            }
        }
    </style>
</head>

<body style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif; background:#f3f4f6; margin:0; padding:24px;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" role="presentation"
                    style="
                           background:#ffffff url('https://i.ibb.co/DP0cSWHt/Generated-Image-November-07-2025-11-16-AM.png') center/cover no-repeat;
                           background-blend-mode: lighten;
                           opacity: 0.98;
                           border-radius:10px;
                           overflow:hidden;
                           box-shadow:0 4px 12px rgba(0,0,0,0.05);
                       ">

                    <!-- Header -->
                    <tr>
                        <td style="padding:32px 24px; text-align:center; background:#535862;">
                            <img
                                src="{{ asset('storage/assets/GoMotoCover.png') }}"
                                alt="GoMoto"
                                style="max-width:180px; height:auto; display:block; margin:0 auto 10px;" />
                            <h1 style="color:#ffffff; font-size:22px; font-weight:600; margin:8px 0 0;">
                                Return Reminder
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:32px; color:#333333;">
                            <h2 style="font-size:18px; margin:0 0 12px; color:#535862;">Hi {{ $booking->user->first_name ?? 'there' }}!</h2>

                            @php
                                $vehicleMake = optional(optional($booking->vehicle)->make)->name;
                                $vehicleModel = optional(optional($booking->vehicle)->model)->name;
                                $vehicleName = trim(($vehicleMake ? $vehicleMake : '') . ' ' . ($vehicleModel ? $vehicleModel : '')) ?: 'your vehicle';
                                $expectedReturn = $booking->getCalculatedEndDatetimeAttribute();
                                $returnTime = $expectedReturn ? $expectedReturn->format('M d, Y g:i A') : 'your scheduled time';
                            @endphp

                            <p style="margin:0 0 16px; line-height:1.6; color:#555;">
                                This is a friendly reminder that your {{ $vehicleName }} rental is due in about 10 minutes.
                            </p>

                            <div style="background:#f8fafc; border-left:4px solid #535862; padding:16px; margin:24px 0; border-radius:4px;">
                                <p style="margin:0; color:#374151;">
                                    <strong>Return Time:</strong> {{ $returnTime }}
                                </p>
                            </div>

                            <p style="margin:0 0 16px; line-height:1.6; color:#555;">
                                Please return the vehicle on time to avoid overcharge fees. If you need more time, you can request an extension through the app.
                            </p>

                            <p style="text-align:center; margin:32px 0;">
                                <a href="{{ rtrim(config('app.url'), '/') }}/bookings/{{ $booking->id }}" class="button"
                                    style="background:#535862; color:#ffffff; text-decoration:none; padding:14px 28px; border-radius:8px; display:inline-block; font-weight:600; letter-spacing:0.3px;">
                                    Manage Booking
                                </a>
                            </p>

                            <p style="margin:32px 0 0; color:#666;">Thank you,<br><strong>GoMoto Team</strong></p>

                            <hr style="border:none; border-top:1px solid #e5e7eb; margin:32px 0;" />

                            <p style="font-size:13px; color:#888; line-height:1.5;">
                                This is an automated message. Please do not reply to this email.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="footer" style="padding:16px; text-align:center; background:#f9fafb; color:#999; font-size:12px;">
                            © {{ date('Y') }} GoMoto. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
