<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quotation</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family: Arial, sans-serif; font-size:14px; color:#333;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:20px;">
    <tr>
        <td align="center">

            <!-- Main Container -->
            <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:6px; overflow:hidden;">

                <!-- Header -->
                <tr>
                    <td style="background-color:#0d6efd; padding:20px; color:#ffffff;">
                        <h2 style="margin:0;">{{ $company->name }}</h2>
                        <p style="margin:5px 0 0; font-size:13px;">
                            Quotation
                        </p>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:25px;">

                        <p style="margin-top:0;">
                            Dear <strong>{{ $so->client->name ?? 'Sir/Madam' }}</strong>,
                        </p>

                        <p>
                            Thank you for your interest in our products/services.  
                            Please find attached the quotation with the details below.
                        </p>

                        @if(!empty($body))
                            <p style="background:#f8f9fa; padding:12px; border-left:4px solid #0d6efd;">
                                {!! nl2br(e($body)) !!}
                            </p>
                        @endif

                        <!-- Quotation Details -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin:20px 0; border-collapse:collapse;">
                            <tr>
                                <td style="padding:8px; border:1px solid #ddd;"><strong>Quotation No</strong></td>
                                <td style="padding:8px; border:1px solid #ddd;">{{ $so->quotation_number }}</td>
                            </tr>
                            <tr>
                                <td style="padding:8px; border:1px solid #ddd;"><strong>Quotation Date</strong></td>
                                <td style="padding:8px; border:1px solid #ddd;">{{ $so->quotation_date }}</td>
                            </tr>
                            <tr>
                                <td style="padding:8px; border:1px solid #ddd;"><strong>Grand Total</strong></td>
                                <td style="padding:8px; border:1px solid #ddd; font-size:16px; color:#0d6efd;">
                                    <strong>₹ {{ inr_format($so->grand_total) }}</strong>
                                </td>
                            </tr>
                        </table>

                        <!-- Payment Details -->
                        <h3 style="margin-bottom:10px;">Payment Details</h3>
                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                            <tr>
                                <td style="padding:6px; border:1px solid #ddd;"><strong>Bank Name</strong></td>
                                <td style="padding:6px; border:1px solid #ddd;">{{ $company->bank_name ?? 'ABC Bank' }}</td>
                            </tr>
                            <tr>
                                <td style="padding:6px; border:1px solid #ddd;"><strong>Account Name</strong></td>
                                <td style="padding:6px; border:1px solid #ddd;">{{ $company->account_name ?? $company->name }}</td>
                            </tr>
                            <tr>
                                <td style="padding:6px; border:1px solid #ddd;"><strong>Account Number</strong></td>
                                <td style="padding:6px; border:1px solid #ddd;">{{ $company->account_number ?? 'XXXXXXXXXX' }}</td>
                            </tr>
                            <tr>
                                <td style="padding:6px; border:1px solid #ddd;"><strong>IFSC Code</strong></td>
                                <td style="padding:6px; border:1px solid #ddd;">{{ $company->ifsc_code ?? 'ABC0123456' }}</td>
                            </tr>
                            <tr>
                                <td style="padding:6px; border:1px solid #ddd;"><strong>UPI ID</strong></td>
                                <td style="padding:6px; border:1px solid #ddd;">{{ $company->upi_id ?? 'company@upi' }}</td>
                            </tr>
                        </table>

                        <!-- Notes -->
                        <p style="margin-top:20px; font-size:13px; color:#555;">
                            <strong>Notes:</strong><br>
                            • Prices are valid as per quotation terms.<br>
                            • Payment to be made as per agreed payment terms.<br>
                            • Please mention quotation number while making payment.
                        </p>

                        <!-- CTA -->
                        <p style="margin-top:25px;">
                            If you have any questions or need clarification, feel free to contact us.  
                            We look forward to working with you.
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
