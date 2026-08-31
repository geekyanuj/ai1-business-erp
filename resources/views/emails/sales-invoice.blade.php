<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tax Invoice</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family: Arial, sans-serif; font-size:14px; color:#333;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:20px;">
    <tr>
        <td align="center">

            <!-- Main Container -->
            <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:6px; overflow:hidden;">

                <!-- Header -->
                <tr>
                    <td style="background-color:#198754; padding:20px; color:#ffffff; vertical-align:middle;">
                        <h2 style="margin:0;">{{ $company->name }}</h2>
                        <p style="margin:5px 0 0; font-size:13px;">
                            Tax Invoice
                        </p>
                    </td>
                    <td style="background-color:#198754; padding:20px; text-align:right;">
                        @if(!empty($logoPath))
                            <img src="{{ $message->embed($logoPath) }}"
                                alt="{{ $company->name }} Logo"
                                style="max-height:80px; display:block;">
                        @endif
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:25px;">

                        <p style="margin-top:0;">
                            Dear <strong>{{ $so->client->name ?? 'Sir/Madam' }}</strong>,
                        </p>

                        <p>
                            Thank you for your payment.  
                            Please find attached your <strong>Tax Invoice</strong> for the completed order.
                        </p>

                        @if(!empty($body))
                            <p style="background:#f8f9fa; padding:12px; border-left:4px solid #198754;">
                                {!! nl2br(e($body)) !!}
                            </p>
                        @endif

                        <!-- Invoice Details -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin:20px 0; border-collapse:collapse;">
                            <tr>
                                <td style="padding:8px; border:1px solid #ddd;"><strong>Invoice Number</strong></td>
                                <td style="padding:8px; border:1px solid #ddd;">{{ $so->invoice_number }}</td>
                            </tr>
                            <tr>
                                <td style="padding:8px; border:1px solid #ddd;"><strong>Invoice Date</strong></td>
                                <td style="padding:8px; border:1px solid #ddd;">{{ $so->invoice_date }}</td>
                            </tr>
                            <tr>
                                <td style="padding:8px; border:1px solid #ddd;"><strong>Grand Total</strong></td>
                                <td style="padding:8px; border:1px solid #ddd; font-size:16px; color:#198754;">
                                    <strong>₹ {{ inr_format($so->grand_total) }}</strong>
                                </td>
                            </tr>
                        </table>

                        <!-- Thank You Note -->
                        <p style="background:#e9f7ef; padding:12px; border-left:4px solid #198754;">
                            <strong>Thank you for your prompt payment.</strong>  
                            We truly appreciate your business and look forward to serving you again.
                        </p>

                        <p style="margin-top:25px;">
                            If you have any questions regarding this invoice, please feel free to contact us.
                        </p>

                        <p style="margin-bottom:0;">
                            Regards,<br>
                            <strong>{{ $company->name }}</strong><br>
                            {{ config('app.name') }}
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background-color:#f1f1f1; padding:15px; text-align:center; font-size:12px; color:#777;">
                        © {{ date('Y') }} {{ $company->name }}. All rights reserved.
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
