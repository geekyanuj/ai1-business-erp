<?php
namespace App\Services;

use App\Models\SalesQuotation;
use App\Models\Company;
use App\Pdf\BasePdf;

class SalesQuotationPdfService
{

    protected AmountService $amountService;

    public function __construct(AmountService $amountService)
    {
        $this->amountService = $amountService;
    }

    public function generate(SalesQuotation $so): string
    {
        $so->loadMissing(['client', 'items']);
        $company = Company::with('defaultBranch')->firstOrFail();
        $amountInWordsIndian = $this->amountService->amountInWordsIndian($so->grand_total);

        $pdf = new BasePdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->company = $company;
        $pdf->documentTitle = 'Quotation';
        $pdf->watermarkText = strtoupper($so->status);

        $pdf->SetCreator('Inventory-ERP');
        $pdf->SetAuthor($company->name);
        $pdf->SetTitle('Quotation');

        $pdf->SetMargins(10, 30, 10);
        $pdf->SetFooterMargin(10);
        $pdf->SetAutoPageBreak(true, 20);

        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);

        $pdf->AddPage();

        $html = view('sales-order.quotation.print', [
            'so' => $so,
            'company' => $company,
            'amountInWordsIndian' => $amountInWordsIndian,
        ])->render();

        $pdf->writeHTML($html, true, false, true, false, '');

        return $pdf->Output('', 'S');
    }
}
