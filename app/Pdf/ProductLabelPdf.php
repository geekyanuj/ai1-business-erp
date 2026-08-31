<?php

namespace App\Pdf;

use App\Models\Label;
use TCPDF;

class ProductLabelPdf extends TCPDF
{
    public function render(Label $label): self
    {
        $this->SetMargins(0, 0, 0);
        $this->SetAutoPageBreak(false);
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        $this->SetTitle("Labels - Lot {$label->lot_no}");
        $this->AddPage();

        return $this;
    }
}
