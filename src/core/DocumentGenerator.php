<?php

namespace MVC\core;

// Check if TCPDF exists
$tcpdfPath = __DIR__ . '/../../vendor/tcpdf/tcpdf.php';
if (!file_exists($tcpdfPath)) {
    throw new \Exception('TCPDF library not found. Please download it from https://github.com/tecnickcom/TCPDF/releases and extract to vendor/tcpdf directory.');
}

// Define all required CURL constants if not available
if (!defined('CURLOPT_CONNECTTIMEOUT')) {
    define('CURLOPT_CONNECTTIMEOUT', 78);
}
if (!defined('CURLOPT_TIMEOUT')) {
    define('CURLOPT_TIMEOUT', 13);
}
if (!defined('CURLOPT_RETURNTRANSFER')) {
    define('CURLOPT_RETURNTRANSFER', 19913);
}
if (!defined('CURLOPT_SSL_VERIFYPEER')) {
    define('CURLOPT_SSL_VERIFYPEER', 64);
}
if (!defined('CURLOPT_SSL_VERIFYHOST')) {
    define('CURLOPT_SSL_VERIFYHOST', 81);
}
if (!defined('CURLOPT_MAXREDIRS')) {
    define('CURLOPT_MAXREDIRS', 68);
}
if (!defined('CURLOPT_FOLLOWLOCATION')) {
    define('CURLOPT_FOLLOWLOCATION', 52);
}
if (!defined('CURLOPT_USERAGENT')) {
    define('CURLOPT_USERAGENT', 10018);
}
if (!defined('CURLOPT_HTTPHEADER')) {
    define('CURLOPT_HTTPHEADER', 10023);
}
if (!defined('CURLOPT_ENCODING')) {
    define('CURLOPT_ENCODING', 10102);
}
if (!defined('CURLOPT_PROTOCOLS')) {
    define('CURLOPT_PROTOCOLS', 181);
}
if (!defined('CURLOPT_REDIR_PROTOCOLS')) {
    define('CURLOPT_REDIR_PROTOCOLS', 182);
}
if (!defined('CURLPROTO_HTTP')) {
    define('CURLPROTO_HTTP', 1);
}
if (!defined('CURLPROTO_HTTPS')) {
    define('CURLPROTO_HTTPS', 2);
}
if (!defined('CURLPROTO_FTP')) {
    define('CURLPROTO_FTP', 4);
}
if (!defined('CURLPROTO_FTPS')) {
    define('CURLPROTO_FTPS', 8);
}
if (!defined('CURLOPT_FAILONERROR')) {
    define('CURLOPT_FAILONERROR', 45);
}
if (!defined('CURLOPT_HTTP_VERSION')) {
    define('CURLOPT_HTTP_VERSION', 84);
}
if (!defined('CURL_HTTP_VERSION_1_1')) {
    define('CURL_HTTP_VERSION_1_1', 2);
}
if (!defined('CURLOPT_IPRESOLVE')) {
    define('CURLOPT_IPRESOLVE', 113);
}
if (!defined('CURL_IPRESOLVE_V4')) {
    define('CURL_IPRESOLVE_V4', 1);
}

// Include TCPDF directly
require_once $tcpdfPath;

class DocumentGenerator extends \TCPDF {
    public function __construct() {
        // Call parent constructor first
        parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $this->SetCreator('Land Registry System');
        $this->SetAuthor('Land Registry System');
        $this->SetTitle('Property Transfer Document');
        
        // Set default header data
        $this->SetHeaderData('', 0, 'Property Transfer Document', 'Land Registry System');
        
        // Set margins
        $this->SetMargins(15, 15, 15);
        $this->SetHeaderMargin(5);
        $this->SetFooterMargin(10);
        
        // Set auto page breaks
        $this->SetAutoPageBreak(TRUE, 15);
        
        // Set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        // Set font
        $this->SetFont('helvetica', '', 12);
        
        // Disable remote file access
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $this->setJPEGQuality(100);
    }

    public function generateTransferDocument($transferDetails, $propertyDetails, $sellerDetails, $buyerDetails) {
        // Check if storage directory exists
        $storageDir = __DIR__ . '/../../storage/documents/transfers';
        if (!is_dir($storageDir)) {
            throw new \Exception('Storage directory not found. Please create the directory: storage/documents/transfers');
        }

        // Check if storage directory is writable
        if (!is_writable($storageDir)) {
            throw new \Exception('Storage directory is not writable. Please check permissions for: storage/documents/transfers');
        }

        // Add a page
        $this->AddPage();
        
        // Set font
        $this->SetFont('helvetica', 'B', 16);
        
        // Title
        $this->Cell(0, 10, 'Property Transfer Document', 0, 1, 'C');
        $this->Ln(10);
        
        // Transfer Information
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 10, 'Transfer Information', 0, 1, 'L');
        $this->SetFont('helvetica', '', 12);
        $this->Cell(60, 10, 'Tracking Number:', 0, 0);
        $this->Cell(0, 10, $transferDetails['tracking_number'], 0, 1);
        $this->Cell(60, 10, 'Status:', 0, 0);
        $this->Cell(0, 10, $transferDetails['status'], 0, 1);
        $this->Cell(60, 10, 'Date:', 0, 0);
        $this->Cell(0, 10, $transferDetails['created_at'], 0, 1);
        $this->Ln(10);
        
        // Property Information
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 10, 'Property Information', 0, 1, 'L');
        $this->SetFont('helvetica', '', 12);
        $this->Cell(60, 10, 'District:', 0, 0);
        $this->Cell(0, 10, $propertyDetails['district_name'], 0, 1);
        $this->Cell(60, 10, 'Village:', 0, 0);
        $this->Cell(0, 10, $propertyDetails['village'], 0, 1);
        $this->Cell(60, 10, 'Block Name:', 0, 0);
        $this->Cell(0, 10, $propertyDetails['block_name'], 0, 1);
        $this->Cell(60, 10, 'Plot Number:', 0, 0);
        $this->Cell(0, 10, $propertyDetails['plot_number'], 0, 1);
        $this->Cell(60, 10, 'Block Number:', 0, 0);
        $this->Cell(0, 10, $propertyDetails['block_number'], 0, 1);
        $this->Cell(60, 10, 'Apartment Number:', 0, 0);
        $this->Cell(0, 10, $propertyDetails['apartment_number'], 0, 1);
        $this->Ln(10);
        
        // Seller Information
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 10, 'Seller Information', 0, 1, 'L');
        $this->SetFont('helvetica', '', 12);
        $this->Cell(60, 10, 'Name:', 0, 0);
        $this->Cell(0, 10, $sellerDetails['User_Name'], 0, 1);
        $this->Cell(60, 10, 'National ID:', 0, 0);
        $this->Cell(0, 10, $sellerDetails['National_ID'], 0, 1);
        $this->Cell(60, 10, 'Phone:', 0, 0);
        $this->Cell(0, 10, $sellerDetails['Phone'], 0, 1);
        $this->Cell(60, 10, 'Address:', 0, 0);
        $this->Cell(0, 10, $sellerDetails['Address'], 0, 1);
        $this->Ln(10);
        
        // Buyer Information
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 10, 'Buyer Information', 0, 1, 'L');
        $this->SetFont('helvetica', '', 12);
        $this->Cell(60, 10, 'Name:', 0, 0);
        $this->Cell(0, 10, $buyerDetails['buyer_name'], 0, 1);
        $this->Cell(60, 10, 'National ID:', 0, 0);
        $this->Cell(0, 10, $buyerDetails['buyer_national_id'], 0, 1);
        $this->Cell(60, 10, 'Phone:', 0, 0);
        $this->Cell(0, 10, $buyerDetails['buyer_phone'], 0, 1);
        $this->Cell(60, 10, 'Address:', 0, 0);
        $this->Cell(0, 10, $buyerDetails['buyer_address'], 0, 1);
        $this->Ln(10);
        
        // Signature Section
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 10, 'Signatures', 0, 1, 'L');
        $this->SetFont('helvetica', '', 12);
        $this->Cell(60, 10, 'Seller Signature:', 0, 0);
        $this->Cell(0, 10, '_________________', 0, 1);
        $this->Cell(60, 10, 'Buyer Signature:', 0, 0);
        $this->Cell(0, 10, '_________________', 0, 1);
        $this->Cell(60, 10, 'Official Stamp:', 0, 0);
        $this->Cell(0, 10, '_________________', 0, 1);
        
        // Generate QR Code
        $this->Ln(10);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 10, 'Document Verification QR Code', 0, 1, 'C');
        $this->Ln(5);
        
        // Add QR Code
        $qrData = json_encode([
            'tracking_number' => $transferDetails['tracking_number'],
            'property_id' => $propertyDetails['id'],
            'transfer_date' => $transferDetails['created_at']
        ]);
        
        $this->write2DBarcode($qrData, 'QRCODE,H', 80, $this->GetY(), 50, 50);
    }
} 