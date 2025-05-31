<?php

// Check if TCPDF exists
$tcpdfPath = __DIR__ . '/vendor/tcpdf/tcpdf.php';
if (!file_exists($tcpdfPath)) {
    die('TCPDF library not found. Please make sure you have copied all TCPDF files to vendor/tcpdf/ directory.');
}

// Include TCPDF
require_once $tcpdfPath;

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Land Registry System');
$pdf->SetAuthor('Land Registry System');
$pdf->SetTitle('Test Document');

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// Add content
$pdf->Cell(0, 10, 'TCPDF is working correctly!', 0, 1, 'C');

// Output the PDF
$pdf->Output('test.pdf', 'I'); 