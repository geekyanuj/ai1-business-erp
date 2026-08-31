<?php


namespace App\Pdf;
use TCPDF;
use Illuminate\Support\Facades\Storage;

class BasePdf extends TCPDF
{
    public $company;
    public $documentTitle = '';
    public $watermarkText = '';

    public function Header()
    {
        // Background bar
        $this->SetFillColor(240, 248, 255);
        $this->Rect(0, 0, 210, 30, 'F');

        // Logo
        if ($this->company->logo) {
            $path = storage_path('app/public/' . $this->company->logo);
            $this->Image($path, 10, 2, 28);
        }

        $this->SetTextColor(54, 86, 135);

        // Company Name
        $this->SetXY(40, 4);
        $this->SetFont('helvetica', 'B', 18);
        $this->Cell(0, 6, $this->company->name, 0, 1, 'L');

        // Company Address
        $this->SetX(42);
        $this->SetFont('helvetica', '', 8);
        $address = trim(implode("\n", array_filter([
            $this->company->defaultBranch->address_line1,
            $this->company->defaultBranch->address_line2 . ', ' . $this->company->defaultBranch->city . ', ' . $this->company->defaultBranch->state . ' - ' . $this->company->defaultBranch->pincode,
        ])));
        $this->MultiCell(0, 5, $address, 0, 'L');

        // Company Email & GSTIN
        $this->SetX(42);
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(0, 2, 'Email: ' . $this->company->defaultBranch->email, 0, 1, 'L');
        $this->SetX(42);
        $this->Cell(0, 2, 'GSTIN: ' . $this->company->defaultBranch->gst_number, 0, 1, 'L');

        // Document Title
        if ($this->documentTitle) {
            $this->SetXY(140, 10);
            $this->SetFont('helvetica', 'B', 14);
            $this->SetTextColor(0, 102, 204);
            $this->Cell(60, 8, strtoupper($this->documentTitle), 0, 1, 'R');
            $this->SetTextColor(0, 0, 0);
        }

        // ===== WATERMARK =====
        if (!empty($this->watermarkText)) {

            // Set subtle opacity
            $this->SetAlpha(0.15);
            $this->SetTextColor(180, 180, 180);

            $text = strtoupper($this->watermarkText);

            // Page dimensions
            $pageWidth = $this->getPageWidth();
            $pageHeight = $this->getPageHeight();

            // Page center
            $cx = $pageWidth / 2;
            $cy = $pageHeight / 2;

            // Safe font size
            $fontSize = 60;
            $this->SetFont('helvetica', 'B', $fontSize);

            // Text width
            $textWidth = $this->GetStringWidth($text);

            $this->StartTransform();

            // Rotate around center (diagonal watermark)
            $this->Rotate(35, $cx, $cy);

            // Draw text centered
            $this->Text(
                $cx - ($textWidth / 2),
                $cy - ($fontSize / 3), // slightly adjusted vertical
                $text
            );

            $this->StopTransform();

            // Reset
            $this->SetAlpha(1);
            $this->SetTextColor(0, 0, 0);
        }



        // Separator line
        $this->Line(10, 32, 200, 32);
    }


    public function Footer()
    {
        // Footer height
        $footerHeight = 12;

        // Position footer
        $this->SetY(-$footerHeight);


        // Top border line
        $this->SetDrawColor(200, 200, 200);
        $this->Line(10, $this->GetY(), $this->getPageWidth() - 10, $this->GetY());

        $this->Ln(4);
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(60, 60, 60);

        // Left margin start
        $leftX = 10;
        $this->SetX($leftX);

        // "Powered by" text (left aligned)
        $this->Cell(16, 5, 'Powered by', 0, 0, 'L');

        // LinkedIn URL
        $linkedinUrl = 'https://www.linkedin.com/in/geekyanuj/';

        // Logo settings
        $logoWidth = 10; // mm
        $logoY = $this->GetY();

        // Clickable logo (left aligned)
        $this->Image(
            public_path('images/minimal-logo.png'),
            $this->GetX(),
            $logoY,
            $logoWidth,
            0,
            '',
            $linkedinUrl
        );

        // Page number (right aligned)
        $this->SetX(-60);
        $this->Cell(
            50,
            6,
            'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(),
            0,
            0,
            'R'
        );
    }



}
