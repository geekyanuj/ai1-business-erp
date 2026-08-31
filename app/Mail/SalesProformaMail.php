<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\SalesProforma;
use App\Services\SalesProformaPdfService;
use Illuminate\Mail\Mailable;
class SalesProformaMail extends Mailable
{
    public function __construct(public $id, public $data)
    {
    }

    public function build(SalesProformaPdfService $pdfService)
    {
        $so = SalesProforma::with(['client', 'items'])->findOrFail($this->id);
        $company = Company::firstOrFail();
        $pdfBinary = $pdfService->generate($so);

        // dd('hello');

        return $this->from($this->data['from_email'])
            ->subject($this->data['subject'])
            ->view('emails.sales-proforma')
            ->with([
                'so'   => $so,
                'body' => $this->data['body'],
                'company' => $company,
            ])
            ->attachData(
                $pdfBinary,
                "{$so->proforma_number}.pdf",
                [
                    'mime' => 'application/pdf',
                ]
            );
    }
}
