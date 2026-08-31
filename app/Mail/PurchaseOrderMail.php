<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderPdfService;
use Illuminate\Mail\Mailable;
class PurchaseOrderMail extends Mailable
{
    public function __construct(public $id, public $data)
    {
    }

    public function build(PurchaseOrderPdfService $pdfService)
    {
        $po = PurchaseOrder::with(['supplier', 'items'])->findOrFail($this->id);
        $company = Company::firstOrFail();
        $pdfBinary = $pdfService->generate($po);

        // dd('hello');

        return $this->from($this->data['from_email'])
            ->subject($this->data['subject'])
            ->view('emails.purchase-order')
            ->with([
                'po'   => $po,
                'body' => $this->data['body'],
                'company' => $company,
            ])
            ->attachData(
                $pdfBinary,
                "PO-{$po->po_number}.pdf",
                [
                    'mime' => 'application/pdf',
                ]
            );
    }
}
