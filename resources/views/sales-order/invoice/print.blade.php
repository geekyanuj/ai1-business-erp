<style>
    body {
        font-family: dejavusans, arial, sans-serif;
        font-size: 11px;
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
        font-size: 10px;
    }

    .bold {
        font-weight: bold;
    }

    .muted {
        color: #64748b;
        font-size: 10px;
    }

    .spacer {
        height: 12px;
        line-height: 12px;
    }

    /* ===== SECTION TITLES ===== */
    .section-title {
        font-size: 12px;
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
        font-size: 11px;
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

    <!-- ================= CLIENT + ORDER INFO ================= -->
    <table width="100%" cellpadding="6">
        <tr>
            <td width="120mm" valign="top">
                <div class="section-title">Client</div>
                <strong>{{ $so->client->name }}</strong><br>
                <table width="90mm" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="word-wrap:break-word; padding:0; white-space:pre-line;">
                            <br><span></span>
                            {!! nl2br(e($so->client->shipping_address)) !!}
                        </td>
                    </tr>
                    <tr>
                        <td style="word-wrap:break-word; padding:0;">
                            <br><span></span>
                            {{ $so->client->email }} | {{ $so->client->phone }}
                        </td>
                    </tr>
                    <tr>
                        <td style="word-wrap:break-word; padding:0;">
                            <br><span></span>
                            GSTIN: {{ $so->client->gst_number }}
                        </td>
                    </tr>
                </table>

            </td>

            <td width="65mm" valign="top">
                <div class="section-title">Order Details</div>
                <table width="100%" cellpadding="3">
                    <tr>
                        <td width="20mm">Invoice No</td>
                        <td width="45mm" align="right">
                            <strong>#{{ $so->invoice_number }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td width="25mm">Order Date</td>
                        <td width="40mm" align="right">{{ $so->invoice_date }}</td>
                    </tr>
                    <tr>
                        <td width="25mm">Prepared by</td>
                        <td width="40mm" align="right">{{ $so->creator->name }}</td>
                    </tr>
                    @if($so->delivery_date)
                        <tr>
                            <td width="20mm">Expected Delivery Date</td>
                            <td width="45mm" align="right">{{ $so->delivery_date }}</td>
                        </tr>
                    @endif
                    @if($so->client_po_ref)
                        <tr>
                            <td width="20mm">Invoice Reference</td>
                            <td width="45mm" align="right">{{ $so->client_po_ref }}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>
      <!-- ================= BILLING + SHIPPING ADDRESS ================= -->
    <table width="100%" cellpadding="6">
        <tr>
            <td width="120mm" valign="top">
                <strong>Shipping Address </strong><br>
                <table width="90mm" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="word-wrap:break-word; padding:0; white-space:pre-line;">
                            <br><span></span>
                            {!! nl2br(e($so->client->contact_person)) !!}
                            <br><span></span>
                            {!! nl2br(e($so->shippingAddress ? $so->shippingAddress->full_address : $so->client->shipping_address)) !!}
                        </td>
                    </tr>
                    
                </table>

            </td>
            <td width="120mm" valign="top">
                <strong>Billing Address </strong><br>
                <table width="90mm" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="word-wrap:break-word; padding:0; white-space:pre-line;">
                            
                            <br><span></span>
                            {!! nl2br(e($so->billingAddress ? $so->billingAddress->full_address : $so->client->billing_address)) !!}
                        </td>
                    </tr>
                    
                </table>

            </td>

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
                <th style="width:16mm" class="text-center">Tax<br>(%)</th>
                <th style="width:18mm" class="text-right">Tax Amt</th>
                <th style="width:22mm" class="text-right">Total</th>
            </tr>
        </thead>

        <tbody>
            @foreach($so->items as $i => $item)
                <tr class="text-medium">
                    <td class="text-center muted" style="width:8mm">
                        {{ $i + 1 }}
                    </td>

                    <td style="width:58mm">
                        <span class="bold">{{ $item->product->our_part_no }}</span><br>
                        @if($item->product->description)
                            <small>{{ $item->product->description }}</small><br>
                        @endif
                        @if($item->product->uom)
                            <span class="muted" style="font-size:8px;">UOM: {{ $item->product->uom }}</span>
                        @endif
                    </td>

                    <td class="text-center" style="width:16mm">
                        {{ $item->product->hsn }}
                    </td>

                    <td class="text-right" style="width:12mm">
                        {{ $item->quantity }}
                    </td>

                    <td class="text-right" style="width:20mm">
                        ₹{{ inr_format($item->unit_price) }}
                    </td>

                    <td class="text-right" style="width:22mm">
                        ₹{{ inr_format($item->taxable_amount) }}
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
            <td width="35%" class="" style="font-size:12px;">
                <table>
                    <tr>
                        <td>Subtotal</td>
                        <td class="text-right">₹{{ inr_format($so->subtotal) }}</td>
                    </tr>
                    @php
                        $cgstRatio = (float) \App\Models\Setting::get('cgst_division_percentage', 50);
                        $sgstRatio = (float) \App\Models\Setting::get('sgst_division_percentage', 50);
                        $totalRatio = $cgstRatio + $sgstRatio ?: 1;
                        
                        $taxRates = $so->items->pluck('tax_rate')->unique();
                        $commonTaxRate = count($taxRates) === 1 ? $taxRates->first() : null;
                    @endphp

                    @if($so->tax_type === 'cgst_sgst')
                        <tr>
                            <td>CGST @if($commonTaxRate) ({{ number_format($commonTaxRate * $cgstRatio / $totalRatio, 2) + 0 }}%) @endif</td>
                            <td class="text-right">₹{{ inr_format($so->cgst_amount) }}</td>
                        </tr>
                        <tr>
                            <td>SGST @if($commonTaxRate) ({{ number_format($commonTaxRate * $sgstRatio / $totalRatio, 2) + 0 }}%) @endif</td>
                            <td class="text-right">₹{{ inr_format($so->sgst_amount) }}</td>
                        </tr>
                    @else
                        <tr>
                            <td>IGST @if($commonTaxRate) ({{ number_format($commonTaxRate, 2) + 0 }}%) @endif</td>
                            <td class="text-right">₹{{ inr_format($so->igst_amount) }}</td>
                        </tr>
                    @endif

                    <tr>
                        <td colspan="2" style="border-top:1px solid #bfdbfe;"></td>
                    </tr>

                    <tr style="font-size:10px; font-weight:bold;">
                        <td>Grand Total</td>
                        <td class="text-right">₹{{ inr_format($so->grand_total,) }}</td>
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
                    @if ($so->notes)
                        <td>{!! nl2br(e($so->notes)) !!}</td>
                    @else
                        <td style="font-size:8px;">• All goods supplied as per specifications.<span><br></span>
                        • Products carry manufacturer warranty; defects must be reported within 15 days.<span><br></span>
                        • Taxes included as per law.</td><span><br></span>
                    @endif

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
                @if ($so->tnc)
                    <span style="font-size:0;"></span><br><span></span>
                    <strong>Terms &amp; Conditions</strong><br><span style="font-size:0;"></span>
                    {!! nl2br(e($so->tnc)) !!}
                @else
                    <span style="font-size:0;"></span><br><span></span>
                    <strong>Terms &amp; Conditions</strong><span></span><span></span>
                    <div style="font-size:8px;"><span></span>
                        • Ownership transferred; company not liable for misuse or indirect damages.<br><span></span>
                        • Returns accepted only for defective items within 15 days.<br><span></span>
                        • Disputes subject to [Your City/State] jurisdiction.<br><span></span>
                    </div>
                @endif
            </td>

            
            <!-- Authorized Signatory -->
            <td width="70mm" valign="bottom" align="center" style="text-align:center;">
            <div class="spacer"></div>
            <div class="spacer"></div>

                @if(($so->status === 'paid') && $company->authorised_signature)
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