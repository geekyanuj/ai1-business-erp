<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\SalesInvoice;
use App\Services\SalesInvoicePdfService;
use Illuminate\Mail\Mailable;
use Storage;
use Symfony\Component\Mime\Email;
class SalesInvoiceMail extends Mailable
{
    public function __construct(public $id, public $data)
    {
    }

    public function build(SalesInvoicePdfService $pdfService)
    {
        $so = SalesInvoice::with(['client', 'items'])->findOrFail($this->id);
        $company = Company::firstOrFail();
        $pdfBinary = $pdfService->generate($so);

            
        $logoPath = null;
        if ($company->logo && Storage::disk('public')->exists($company->logo)) {
            $logoPath = Storage::disk('public')->path($company->logo);
        }


        return $this->from($this->data['from_email'])
            ->subject($this->data['subject'])
            ->view('emails.sales-invoice')
            ->with([
                'so' => $so,
                'body' => $this->data['body'],
                'company' => $company,
                'logoPath' => $logoPath,

            ])
            ->attachData(
                $pdfBinary,
                "INVOICE-{$so->invoice_number}.pdf",
                [
                    'mime' => 'application/pdf',
                ]
            );
    }
}
