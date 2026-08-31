<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Purchase Order</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family: Arial, sans-serif; font-size:14px; color:#333;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:20px;">
    <tr>
        <td align="center">

            <!-- Main Container -->
            <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:6px; overflow:hidden;">

                <!-- Header -->
                <tr>
                    <td style="background-color:#6f42c1; padding:20px; color:#ffffff;">
                        <h2 style="margin:0;">{{ $company->name }}</h2>
                        <p style="margin:5px 0 0; font-size:13px;">
                            Purchase Order
                        </p>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:25px;">

                        <p style="margin-top:0;">
                            Dear <strong>{{ $po->supplier->name ?? 'Supplier' }}</strong>,
                        </p>

                        <p>
                            We are pleased to place the following <strong>Purchase Order</strong>.  
                            Please find the PO attached for your reference.
                        </p>

                        @if(!empty($body))
                            <p style="background:#f8f9fa; padding:12px; border-left:4px solid #6f42c1;">
                                {!! nl2br(e($body)) !!}
                            </p>
                        @endif

                        <!-- PO Details -->
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin:20px 0; border-collapse:collapse;">
                            <tr>
                                <td style="padding:8px; border:1px solid #ddd;"><strong>PO Number</strong></td>
                                <td style="padding:8px; border:1px solid #ddd;">{{ $po->po_number }}</td>
                            </tr>
                            <tr>
                                <td style="padding:8px; border:1px solid #ddd;"><strong>Order Date</strong></td>
                                <td style="padding:8px; border:1px solid #ddd;">{{ $po->ordered_date }}</td>
                            </tr>
                            <tr>
                                <td style="padding:8px; border:1px solid #ddd;"><strong>Grand Total</strong></td>
                                <td style="padding:8px; border:1px solid #ddd; font-size:16px; color:#6f42c1;">
                                    <strong>₹ {{ inr_format($po->grand_total) }}</strong>
                                </td>
                            </tr>
                        </table>

                        <!-- Delivery & Payment Terms -->
                        <h3 style="margin-bottom:10px;">Delivery & Payment Terms</h3>
                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                            <tr>
                                <td style="padding:6px; border:1px solid #ddd;"><strong>Delivery Address</strong></td>
                                <td style="padding:6px; border:1px solid #ddd;">
                                    {{ $company->address ?? 'As mentioned in PO document' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:6px; border:1px solid #ddd;"><strong>Expected Delivery Date</strong></td>
                                <td style="padding:6px; border:1px solid #ddd;">
                                    {{ $po->delivery_date ?? 'As per agreement' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:6px; border:1px solid #ddd;"><strong>Payment Terms</strong></td>
                                <td style="padding:6px; border:1px solid #ddd;">
                                    {{ $po->payment_terms ?? 'As per agreement' }}
                                </td>
                            </tr>
                        </table>

                        <!-- Notes -->
                        <p style="margin-top:20px; font-size:13px; color:#555;">
                            <strong>Important Instructions:</strong><br>
                            • Please mention the PO number on all invoices and delivery challans.<br>
                            • Any deviation from this PO must be approved in writing.<br>
                            • Goods are subject to inspection upon receipt.
                        </p>

                        <p style="margin-top:25px;">
                            Kindly acknowledge and confirm acceptance of this purchase order at the earliest.
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
