<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\SalesQuotation;
use App\Services\SalesQuotationPdfService;
use Illuminate\Mail\Mailable;
class SalesQuotationMail extends Mailable
{
    public function __construct(public $id, public $data)
    {
    }

    public function build(SalesQuotationPdfService $pdfService)
    {
        $so = SalesQuotation::with(['client', 'items'])->findOrFail($this->id);
        $company = Company::firstOrFail();
        $pdfBinary = $pdfService->generate($so);

        // dd('hello');

        return $this->from($this->data['from_email'])
            ->subject($this->data['subject'])
            ->view('emails.sales-quotation')
            ->with([
                'so'   => $so,
                'body' => $this->data['body'],
                'company' => $company,
            ])
            ->attachData(
                $pdfBinary,
                "INV-{$so->quotation_number}.pdf",
                [
                    'mime' => 'application/pdf',
                ]
            );
    }
}
