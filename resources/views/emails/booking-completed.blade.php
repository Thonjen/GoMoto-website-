<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Completed</title>

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
                                Booking Completed
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
                                $returnTime = $booking->return_time ? \Carbon\Carbon::parse($booking->return_time)->format('M d, Y g:i A') : now()->format('M d, Y g:i A');
                                $initialAmount = (float) ($booking->total_amount ?? 0);
                                $over = isset($overchargeAmount) ? (float) $overchargeAmount : 0.0;
                                $grandTotal = $initialAmount + $over;
                            @endphp

                            <p style="margin:0 0 16px; line-height:1.6; color:#555;">
                                Your {{ $vehicleName }} rental has been successfully completed.
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin:16px 0 24px;">
                                <tr>
                                    <td style="padding:12px 0; border-bottom:1px solid #e5e7eb;">
                                        <strong style="color:#535862;">Return Time:</strong>
                                        <span style="color:#444;">{{ $returnTime }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0; border-bottom:1px solid #e5e7eb;">
                                        <strong style="color:#535862;">Initial Amount:</strong>
                                        <span style="color:#444;">₱{{ number_format($initialAmount, 2) }}</span>
                                    </td>
                                </tr>
                                @if($over > 0)
                                <tr>
                                    <td style="padding:12px 0; border-bottom:1px solid #e5e7eb;">
                                        <strong style="color:#535862;">Overcharge Applied:</strong>
                                        <span style="color:#444;">₱{{ number_format($over, 2) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;">
                                        <strong style="color:#535862;">Total:</strong>
                                        <span style="color:#444;">₱{{ number_format($grandTotal, 2) }}</span>
                                    </td>
                                </tr>
                                @endif
                            </table>

                            @if($over > 0)
                                <div style="background:#fff7ed; border-left:4px solid #f59e0b; padding:16px; margin:16px 0 24px; border-radius:4px;">
                                    <p style="margin:0; color:#7c2d12;">
                                        Please settle the overcharge payment directly with the owner. Thank you for your understanding.
                                    </p>
                                </div>
                            @else
                                <p style="margin:0 0 16px; line-height:1.6; color:#555;">
                                    No additional charges were applied. Thank you for returning on time!
                                </p>
                            @endif

                            <p style="text-align:center; margin:32px 0;">
                                <a href="{{ rtrim(config('app.url'), '/') }}/bookings/{{ $booking->id }}" class="button"
                                    style="background:#535862; color:#ffffff; text-decoration:none; padding:14px 28px; border-radius:8px; display:inline-block; font-weight:600; letter-spacing:0.3px;">
                                    View Booking Details
                                </a>
                            </p>

                            <p style="margin:32px 0 0; color:#666;">We appreciate you choosing GoMoto. See you again soon!<br><strong>GoMoto Team</strong></p>

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
