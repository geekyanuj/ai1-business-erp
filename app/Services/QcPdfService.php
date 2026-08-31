<?php

namespace App\Services;

use setasign\Fpdi\Tcpdf\Fpdi;
use Illuminate\Support\Facades\Storage;

class QcPdfService
{
    public function process($inputPdfPath, array $data, $outputPath)
    {
        $pdf = new Fpdi();

        // 🔒 Prevent TCPDF from creating new pages automatically
        $pdf->SetAutoPageBreak(false);

        $pageCount = $pdf->setSourceFile($inputPdfPath);

        for ($i = 1; $i <= $pageCount; $i++) {
            $tpl = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($tpl);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tpl);

            // ✅ Footer ONLY on last page
            if ($i === $pageCount) {
                $this->drawFooter($pdf, $data);
            }
        }

        Storage::put($outputPath, $pdf->Output('', 'S'));
    }


    private function drawFooter($pdf, $data)
    {
        $pdf->SetFont('Helvetica', '', 8);

        // Page metrics
        $pageHeight = $pdf->getPageHeight();
        $pageWidth = $pdf->getPageWidth();

        // Footer box size
        $boxWidth = 70;
        $boxHeight = 40;

        // Position the box 10mm from the left and 10mm from the bottom
        $x = 20;
        $y = $pageHeight - $boxHeight - 20;

        $pdf->SetXY($x, $y);

        /* ===============================
         | ROW 1: TEST REPORT (Header)
         ===============================*/
        $pdf->SetX($x);
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->Cell($boxWidth, 8, 'TEST REPORT', 1, 1, 'C');

        /* ===============================
         | ROW 2: PART NO
         ===============================*/
        $pdf->SetX($x);
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->Cell($boxWidth, 8, 'PART NO: ' . $data['part'], 1, 1, 'C');

        /* ===============================
         | ROW 3: SERIAL NUMBER
         ===============================*/
        $pdf->SetX($x);
        $pdf->Cell($boxWidth, 8, 'S/N: ' . $data['serial'], 1, 1, 'C');

        /* ===============================
         | ROW 4: DATE | QC PASS
         ===============================*/
        $pdf->SetX($x);
        $leftWidth = 35;
        $rightWidth = $boxWidth - $leftWidth;

        // DATE cell
        $pdf->Cell($leftWidth, 8, 'DATE: ' . $data['date'], 1, 0, 'C');

        // QC cell (2 lines)
        $xQc = $pdf->GetX();
        $yQc = $pdf->GetY();


        $pdf->Cell($rightWidth, 8, 'QC PASS', 1, 1, 'C');

        // Restore cursor
        $pdf->SetXY($xQc + $rightWidth, $yQc);
    }


}
