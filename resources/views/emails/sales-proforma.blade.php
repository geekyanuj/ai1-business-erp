<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Proforma Invoice</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family: Arial, sans-serif; font-size:14px; color:#333;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:20px;">
    <tr>
        <td align="center">

            <!-- Main Container -->
            <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:6px; overflow:hidden;">

                <!-- Header -->
                <tr>
                    <td style="background-color:#198754; padding:20px; color:#ffffff;">
                        <h2 style="margin:0;">{{ $company->name }}</h2>
                        <p style="margin:5px 0 0; font-size:13px;">
                            <strong>Proforma Invoice (PI)</strong>
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
                            Please find below the <strong>Proforma Invoice</strong> for your reference.
                            This PI is issued prior to delivery and does not demand payment unless agreed.
                        </p>

                        @if(!empty($body))
                            <p style="background:#f8f9fa; padding:12px; border-left:4px solid #198754;">
                                {!! nl2br(e($body)) !!}
                            </p>
                        @endif

                        <!-- PI Details -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin:20px 0; border-collapse:collapse;">
                            <tr>
                                <td style="padding:8px; border:1px solid #ddd;"><strong>PI Number</strong></td>
                                <td style="padding:8px; border:1px solid #ddd;">{{ $so->proforma_number }}</td>
                            </tr>
                            <tr>
                                <td style="padding:8px; border:1px solid #ddd;"><strong>PI Date</strong></td>
                                <td style="padding:8px; border:1px solid #ddd;">{{ $so->proforma_date }}</td>
                            </tr>
                            <tr>
                                <td style="padding:8px; border:1px solid #ddd;"><strong>Validity</strong></td>
                                <td style="padding:8px; border:1px solid #ddd;">
                                    {{ $so->validity_days ?? '15' }} Days
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:8px; border:1px solid #ddd;"><strong>Grand Total</strong></td>
                                <td style="padding:8px; border:1px solid #ddd; font-size:16px; color:#198754;">
                                    <strong>₹ {{ inr_format($so->grand_total) }}</strong>
                                </td>
                            </tr>
                        </table>

                        <!-- Billing & Shipping -->
                        <h3 style="margin-bottom:10px;">Billing & Shipping Details</h3>
                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                            <tr>
                                <td style="padding:6px; border:1px solid #ddd;"><strong>Billing Address</strong></td>
                                <td style="padding:6px; border:1px solid #ddd;">
                                    {{ $so->billing_address ?? 'As per records' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:6px; border:1px solid #ddd;"><strong>Shipping Address</strong></td>
                                <td style="padding:6px; border:1px solid #ddd;">
                                    {{ $so->shipping_address ?? 'Same as billing address' }}
                                </td>
                            </tr>
                        </table>

                        <!-- Tax Details -->
                        <h3 style="margin:20px 0 10px;">Tax Details</h3>
                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                            <tr>
                                <td style="padding:6px; border:1px solid #ddd;"><strong>GST Number</strong></td>
                                <td style="padding:6px; border:1px solid #ddd;">
                                    {{ $company->gst_number ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:6px; border:1px solid #ddd;"><strong>Tax Type</strong></td>
                                <td style="padding:6px; border:1px solid #ddd;">
                                    {{ $so->tax_type ?? 'GST' }}
                                </td>
                            </tr>
                        </table>

                        <!-- Payment Details -->
                        <h3 style="margin:20px 0 10px;">Payment Details</h3>
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

                        <!-- Terms -->
                        <p style="margin-top:20px; font-size:13px; color:#555;">
                            <strong>Terms & Conditions:</strong><br>
                            • This Proforma Invoice is not a tax invoice.<br>
                            • Prices are valid only for the mentioned validity period.<br>
                            • Goods will be dispatched after confirmation/payment as agreed.<br>
                            • Taxes applicable as per government norms at the time of final invoice.
                        </p>

                        <p style="margin-top:25px;">
                            For any clarification regarding this Proforma Invoice, please contact us.
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
