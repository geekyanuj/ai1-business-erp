<style>
    body {
        font-family: dejavusans, arial, sans-serif;
        font-size: 9px;
        color: #1e293b;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .text-medium {
        font-size: 8px;
    }

    .bold {
        font-weight: bold;
    }

    .muted {
        color: #64748b;
        font-size: 8.5px;
    }

    .spacer {
        height: 12px;
        line-height: 12px;
    }

    /* ===== SECTION TITLES ===== */
    .section-title {
        font-size: 10px;
        font-weight: bold;
        color: #0b5ed7;
        text-transform: uppercase;
    }

    /* ===== INFO TABLE ===== */
    .info-table td {
        padding: 6px 4px;
        vertical-align: top;
    }

    /* ===== ITEMS TABLE ===== */
    .items-table th {
        background-color: rgba(54, 86, 135, 1);
        color: #ffffff;
        font-size: 9.5px;
        padding: 6px;
    }

    .items-table td {
        padding: 6px;
        border-bottom: 1px solid #e2e8f0;
    }

    /* ===== SUMMARY ===== */
    .summary-box {
        background-color: #f1f5f9;
        padding: 6px;
        border: 1px solid #e2e8f0;
        line-height: 1.4;
    }

    .total-box {
        background-color: #0b5ed7;
        color: #ffffff;
        padding: 10px;
    }
</style>

<body>

    <!-- ================= SUPPLIER + ORDER INFO ================= -->
    <table width="100%" cellpadding="6">
        <tr>
            <td width="120mm" valign="top">
                <div class="section-title">Supplier</div>
                <strong>{{ $po->supplier->name }}</strong><br>
                <table width="90mm" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="word-wrap:break-word; padding:0; white-space:pre-line;">
                            <br><span></span>
                            {!! nl2br(e($po->supplier->address)) !!}
                        </td>
                    </tr>
                    <tr>
                        <td style="word-wrap:break-word; padding:0;">
                            <br><span></span>
                            {{ $po->supplier->email }} | {{ $po->supplier->phone }}
                        </td>
                    </tr>
                    <tr>
                        <td style="word-wrap:break-word; padding:0;">
                            <br><span></span>
                            GSTIN: {{ $po->supplier->gst_number }}
                        </td>
                    </tr>
                </table>

            </td>

            <td width="65mm" valign="top">
                <div class="section-title">Order Details</div>
                <table width="100%" cellpadding="3">
                    <tr>
                        <td width="25mm">PO No</td>
                        <td width="40mm" align="right">
                            <strong>#{{ $po->po_number }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td width="25mm">Order Date</td>
                        <td width="40mm" align="right">{{ $po->ordered_date }}</td>
                    </tr>
                    @if($po->delivery_date)
                        <tr>
                            <td width="25mm">Delivery Date</td>
                            <td width="40mm" align="right">{{ $po->delivery_date }}</td>
                        </tr>
                    @endif
                    @if($po->quote_ref)
                        <tr>
                            <td width="25mm">Quote Reference</td>
                            <td width="40mm" align="right">{{ $po->quote_ref }}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="bold">Deliver To</td>
        </tr>
        <tr>
            <td>{!! nl2br(e($po->deliveryAddress ? $po->deliveryAddress->full_address : 'N/A')) !!}</td>
        </tr>
    </table>


    <div class="spacer"></div>

    <!-- ================= ITEMS ================= -->
    <table class="items-table" cellpadding="4" cellspacing="0">
        <thead>
            <tr>
                <th style="width:8mm" class="text-center">#</th>
                <th style="width:58mm">Item & Description</th>
                <th style="width:16mm" class="text-center">HSN</th>
                <th style="width:12mm" class="text-right">Qty</th>
                <th style="width:20mm" class="text-right">Rate</th>
                <th style="width:22mm" class="text-right">Taxable</th>
                <th style="width:16mm" class="text-right">Tax<br>(%)</th>
                <th style="width:18mm" class="text-right">Tax Amt</th>
                <th style="width:22mm" class="text-right">Total</th>
            </tr>
        </thead>

        <tbody>
            @foreach($po->items as $i => $item)
                <tr class="text-medium">
                    <td class="text-center muted" style="width:8mm">
                        {{ $i + 1 }}
                    </td>

                    <td style="width:58mm">
                        <span class="bold">{{ $item->product_name }}</span><br>
                        @if($item->product_description)
                            <small>{{ $item->product_description }}</small><br>
                        @endif
                        @if($item->uom)
                            <span class="muted" style="font-size:6px;">UOM: {{ $item->uom }}</span>
                        @endif
                    </td>

                    <td class="text-center" style="width:16mm">
                        {{ $item->hsn_code }}
                    </td>

                    <td class="text-right" style="width:12mm">
                        {{ $item->quantity }}
                    </td>

                    <td class="text-right" style="width:20mm">
                        ₹{{ inr_format($item->unit_price) }}
                    </td>

                    <td class="text-right" style="width:22mm">
                        ₹{{ inr_format($item->quantity * $item->unit_price) }}
                    </td>

                    <td class="text-right" style="width:16mm">
                        {{ number_format($item->tax_rate, 2) }}%
                    </td>

                    <td class="text-right" style="width:18mm">
                        ₹{{ inr_format($item->tax_amount) }}
                    </td>

                    <td class="text-right bold" style="width:22mm">
                        ₹{{ inr_format($item->total_with_tax) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>



    <div class="spacer"></div>

    <!-- ================= NOTES + TOTAL ================= -->
    <table cellpadding="0" cellspacing="0">
        <tr>
            <td width="55%" style="padding-right:10px;">

                <table width="100%">
                    <tr>
                        <td class="bold">Amount in Words</td>
                    </tr>
                    <tr>
                        <td width="100%" style="font-style:italic; word-wrap:break-word;">
                            {{ $amountInWordsIndian }}
                        </td>
                    </tr>
                </table>


                <div class="spacer"></div>

            </td>

            <td width="10%"></td>
            <td width="35%" class="" style="font-size:10px;">
                <table>
                    <tr>
                        <td>Subtotal</td>
                        <td class="text-right">₹{{ inr_format($po->subtotal) }}</td>
                    </tr>
                    @if($po->tax_type === 'cgst_sgst')
                        <tr>
                            <td>CGST ({{ $po->cgst_rate }}%)</td>
                            <td class="text-right">₹{{ inr_format($po->cgst_amount) }}</td>
                        </tr>
                        <tr>
                            <td>SGST ({{ $po->sgst_rate }}%)</td>
                            <td class="text-right">₹{{ inr_format($po->sgst_amount) }}</td>
                        </tr>
                    @else
                        <tr>
                            <td>IGST {{ $po->igst_rate }}</td>
                            <td class="text-right">₹{{ inr_format($po->igst_amount) }}</td>
                        </tr>
                    @endif

                    <tr>
                        <td colspan="2" style="border-top:1px solid #bfdbfe;"></td>
                    </tr>

                    <tr style="font-size:10px; font-weight:bold;">
                        <td>Grand Total</td>
                        <td class="text-right">₹{{ inr_format($po->grand_total) }}</td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="bold">Notes</td>
                    </tr>
                    <tr>

                        <td>{!! nl2br(e($po->notes)) !!}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="spacer"></div>
    <div class="spacer"></div>

    <!-- ================= TERMS + SIGN ================= -->
    <table width="100%" cellpadding="4" cellspacing="0">
        <tr>
            <!-- Terms & Conditions -->
            <td width="110mm" valign="top">
                <span style="font-size:0;"></span><br><span></span>

                <strong>Terms &amp; Conditions</strong><br><span style="font-size:0;"></span>
                {!! nl2br(e($po->tnc)) !!}
            </td>


            <!-- Authorized Signatory -->
            <td width="70mm" valign="bottom" align="center" style="text-align:center;">
                <div class="spacer"></div>
                <div class="spacer"></div>

                @if(($po->status === 'approved' || $po->status === 'received') && $company->authorised_signature)
                    <img src="{{ storage_path('app/public/' . $company->authorised_signature) }}" width="80"><br>
                @else
                    <span style="color:#999;">Signed after approval</span><br>
                @endif

                <hr style="width:80%; margin:4px auto;">
                <strong>Authorized Signatory</strong><br>
                <span style="color:#999;">For {{ $company->name }}</span>
            </td>
        </tr>
    </table>


</body>