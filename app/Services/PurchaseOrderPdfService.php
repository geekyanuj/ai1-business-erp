<?php
namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\Company;
use App\Pdf\BasePdf;

class PurchaseOrderPdfService
{

    protected AmountService $amountService;

    public function __construct(AmountService $amountService)
    {
        $this->amountService = $amountService;
    }

    public function generate(PurchaseOrder $po): string
    {
        $po->loadMissing(['supplier', 'items']);
        $company = Company::with('defaultBranch')->firstOrFail();
        $amountInWordsIndian = $this->amountService->amountInWordsIndian($po->grand_total);

        $pdf = new BasePdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->company = $company;
        $pdf->documentTitle = 'Purchase Order';
        $pdf->watermarkText = strtoupper($po->status);

        $pdf->SetCreator('Inventory-ERP');
        $pdf->SetAuthor($company->name);
        $pdf->SetTitle('Purchase Order');

        $pdf->SetMargins(10, 30, 10);
        $pdf->SetFooterMargin(10);
        $pdf->SetAutoPageBreak(true, 20);

        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);

        $pdf->AddPage();

        $html = view('purchase_orders.po-print', [
            'po' => $po,
            'company' => $company,
            'amountInWordsIndian' => $amountInWordsIndian,
        ])->render();

        $pdf->writeHTML($html, true, false, true, false, '');

        return $pdf->Output('', 'S');
    }
}
