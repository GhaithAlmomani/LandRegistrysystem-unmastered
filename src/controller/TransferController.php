<?php

namespace MVC\controller;

require_once __DIR__ . '/../../config/database.php';

use MVC\middleware\AuthMiddleware;
use MVC\core\CSRFToken;
use MVC\model\User;
use MVC\model\Property;
use MVC\model\PropertyTransfer;
use Exception;

class TransferController extends Controller
{
    private function storageRootAbsolutePath(): string
    {
        $root = realpath(__DIR__ . '/../../storage');
        if ($root === false) {
            $root = __DIR__ . '/../../storage';
        }
        return rtrim($root, "/\\");
    }

    private function buildStorageAbsolutePath(string $relativeFromStorageRoot): string
    {
        $relative = ltrim(str_replace(['\\', "\0"], ['/', ''], $relativeFromStorageRoot), '/');
        return $this->storageRootAbsolutePath() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }

    private function resolveStorageAbsolutePathOrFail(string $relativeFromStorageRoot): string
    {
        $storageRoot = $this->storageRootAbsolutePath();
        $abs = $this->buildStorageAbsolutePath($relativeFromStorageRoot);

        $resolved = realpath($abs);
        if ($resolved === false) {
            throw new Exception("Document file not found");
        }

        $resolvedNorm = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $resolved), DIRECTORY_SEPARATOR);
        $rootNorm = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $storageRoot), DIRECTORY_SEPARATOR);
        if (stripos($resolvedNorm, $rootNorm . DIRECTORY_SEPARATOR) !== 0) {
            throw new Exception("Invalid document path");
        }

        return $resolvedNorm;
    }

    private function postString(string $key, int $maxLen, bool $required = true): array
    {
        $raw = $_POST[$key] ?? null;
        if ($raw === null) {
            return [$required ? null : '', $required ? "$key is required" : null];
        }
        $value = trim((string)$raw);
        if ($required && $value === '') {
            return [null, "$key is required"];
        }
        $len = function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
        if ($value !== '' && $len > $maxLen) {
            return [null, "$key must be at most {$maxLen} characters"];
        }
        return [$value, null];
    }

    private function postInt(string $key, bool $required = true, ?int $min = null, ?int $max = null): array
    {
        $raw = $_POST[$key] ?? null;
        if ($raw === null) {
            return [$required ? null : null, $required ? "$key is required" : null];
        }
        $value = trim((string)$raw);
        if ($required && $value === '') {
            return [null, "$key is required"];
        }
        if ($value === '') {
            return [null, null];
        }
        if (!preg_match('/^-?\d+$/', $value)) {
            return [null, "$key must be an integer"];
        }
        $intVal = (int)$value;
        if ($min !== null && $intVal < $min) {
            return [null, "$key must be >= {$min}"];
        }
        if ($max !== null && $intVal > $max) {
            return [null, "$key must be <= {$max}"];
        }
        return [$intVal, null];
    }

    private function requireValidCsrfOrRedirect(string $to): void
    {
        if (!CSRFToken::validateRequest()) {
            $_SESSION['error'] = 'Invalid or expired form session. Please refresh and try again.';
            header('Location: ' . $to);
            exit;
        }
    }

    public function sellReq(): bool|array|string
    {
        AuthMiddleware::requireUser();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->requireValidCsrfOrRedirect('/sellReq');

                $errors = [];
                [$propertyId, $e] = $this->postInt('property_id', true, 1, null);
                if ($e) $errors['property_id'][] = $e;

                [$buyerName, $e] = $this->postString('buyer_name', 120, true);
                if ($e) $errors['buyer_name'][] = $e;

                [$buyerNationalId, $e] = $this->postString('buyer_national_id', 32, true);
                if ($e) $errors['buyer_national_id'][] = $e;
                if ($buyerNationalId !== null && $buyerNationalId !== '' && !preg_match('/^[0-9]{6,32}$/', $buyerNationalId)) {
                    $errors['buyer_national_id'][] = 'buyer_national_id must be 6-32 digits';
                }

                [$buyerPhone, $e] = $this->postString('buyer_phone', 32, true);
                if ($e) $errors['buyer_phone'][] = $e;
                if ($buyerPhone !== null && $buyerPhone !== '' && !preg_match('/^[0-9+\-\s()]{7,32}$/', $buyerPhone)) {
                    $errors['buyer_phone'][] = 'buyer_phone must be a valid phone number';
                }

                [$buyerAddress, $e] = $this->postString('buyer_address', 255, true);
                if ($e) $errors['buyer_address'][] = $e;

                if (!empty($errors)) {
                    $_SESSION['error'] = 'Please correct the highlighted fields and try again.';
                    $_SESSION['form_errors'] = $errors;
                    $_SESSION['form_old'] = [
                        'property_id' => $_POST['property_id'] ?? null,
                        'buyer_name' => $_POST['buyer_name'] ?? null,
                        'buyer_national_id' => $_POST['buyer_national_id'] ?? null,
                        'buyer_phone' => $_POST['buyer_phone'] ?? null,
                        'buyer_address' => $_POST['buyer_address'] ?? null,
                    ];
                    header('Location: /sellReq?error=1');
                    exit;
                }

                $userData = User::findByUsername($_SESSION['Username']);

                if (!$userData) {
                    throw new Exception("User not found");
                }

                $trackingNumber = 'TRK-' . strtoupper(bin2hex(random_bytes(6)));

                $con = \Database::getConnection();
                $con->beginTransaction();
                PropertyTransfer::create([
                    'property_id' => $propertyId,
                    'seller_id' => (int)$userData['User_ID'],
                    'buyer_name' => $buyerName,
                    'buyer_national_id' => $buyerNationalId,
                    'buyer_phone' => $buyerPhone,
                    'buyer_address' => $buyerAddress,
                    'tracking_number' => $trackingNumber,
                ]);
                Property::updateStatus($propertyId, 'pending_transfer', (int)$userData['User_ID']);
                $con->commit();

                $_SESSION['tracking_number'] = $trackingNumber;
                header('Location: /sellReq?success=1');
                exit;
            } catch (Exception $e) {
                if (isset($con)) {
                    $con->rollBack();
                }
                $_SESSION['error'] = "Error processing your request: " . $e->getMessage();
                header('Location: /sellReq?error=1');
                exit;
            }
        }

        // GET request - render the form with seller + property details (no DB access in view).
        $userDetails = null;
        $propertyDetails = null;
        $error = null;

        if (isset($_GET['property_id'])) {
            try {
                $userDetails = User::findByUsername($_SESSION['Username']);
                if (!$userDetails) {
                    throw new Exception('User not found');
                }
                $propertyId = (int)$_GET['property_id'];
                $propertyDetails = Property::findByIdAndOwner($propertyId, (int)$userDetails['User_ID']);
                if (!$propertyDetails) {
                    throw new Exception('Property not found for this user');
                }
            } catch (Exception $e) {
                $error = "Error loading property details.";
            }
        }

        return $this->render('home.User.sellReq', compact('userDetails', 'propertyDetails', 'error'));
    }

    public function sellRequest(): bool|array|string
    {
        AuthMiddleware::requireEmployee();

        try {
            $con = \Database::getConnection();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $con->beginTransaction();

                $transfer = PropertyTransfer::findByTracking((string)($_POST['tracking_number'] ?? ''));

                if (!$transfer) {
                    throw new Exception("Transfer request not found");
                }

                if ($_POST['action'] === 'approve') {
                    $buyer = User::findByNationalID((string)$transfer['buyer_national_id']);

                    if (!$buyer) {
                        throw new Exception("Buyer not found in the system");
                    }

                    PropertyTransfer::approve((string)$_POST['tracking_number']);
                    Property::updateOwner((int)$transfer['property_id'], (int)$buyer['User_ID']);
                    Property::updateStatus((int)$transfer['property_id'], 'transferred');

                    $result = PropertyTransfer::findTransferDetailsForDocument((string)$_POST['tracking_number']);

                    if ($result) {
                        $transferDetails = [
                            'tracking_number' => $result['tracking_number'],
                            'status' => 'completed',
                            'created_at' => $result['created_at']
                        ];

                        $propertyDetails = [
                            'id' => $result['property_id'],
                            'district_name' => $result['district_name'],
                            'village' => $result['village'],
                            'block_name' => $result['block_name'],
                            'plot_number' => $result['plot_number'],
                            'block_number' => $result['block_number'],
                            'apartment_number' => $result['apartment_number']
                        ];

                        $sellerDetails = [
                            'User_Name' => $result['User_Name'],
                            'National_ID' => $result['User_NationalID'],
                            'Phone' => $result['User_Phone'],
                            'Address' => $result['User_Email']
                        ];

                        $buyerDetails = [
                            'buyer_name' => $result['buyer_name'],
                            'buyer_national_id' => $result['buyer_national_id'],
                            'buyer_phone' => $result['buyer_phone'],
                            'buyer_address' => $result['buyer_address']
                        ];

                        $docGenerator = new \MVC\core\DocumentGenerator();
                        $docGenerator->generateTransferDocument($transferDetails, $propertyDetails, $sellerDetails, $buyerDetails);

                        $storageDir = $this->buildStorageAbsolutePath('documents/transfers');
                        if (!file_exists($storageDir)) {
                            if (!mkdir($storageDir, 0777, true)) {
                                throw new Exception("Failed to create storage directory");
                            }
                        }

                        $documentRelativePath = 'documents/transfers/' . $transferDetails['tracking_number'] . '.pdf';
                        $documentAbsolutePath = $this->buildStorageAbsolutePath($documentRelativePath);
                        $docGenerator->Output($documentAbsolutePath, 'F');

                        if (!file_exists($documentAbsolutePath)) {
                            throw new Exception("Failed to save document file");
                        }
                        if (filesize($documentAbsolutePath) === 0) {
                            throw new Exception("Generated document file is empty");
                        }

                        PropertyTransfer::setDocumentPath($transferDetails['tracking_number'], $documentRelativePath);
                        $check = PropertyTransfer::getDocumentPath($transferDetails['tracking_number']);
                        if (empty($check)) {
                            throw new Exception("Failed to store document path in database");
                        }
                    }

                    $_SESSION['success'] = "Transfer request approved successfully. Property ownership has been transferred.";
                } else {
                    PropertyTransfer::reject((string)$_POST['tracking_number']);
                    Property::updateStatus((int)$transfer['property_id'], 'active');

                    $_SESSION['success'] = "Transfer request rejected";
                }

                $con->commit();
                header('Location: sellRequest');
                exit;
            }

            $pendingRequests = PropertyTransfer::findPending();

            if (isset($_GET['tracking_number']) && !empty($_GET['tracking_number'])) {
                $result = PropertyTransfer::findTransferDetailsForDocument((string)$_GET['tracking_number']);

                if ($result) {
                    $transferDetails = [
                        'tracking_number' => $result['tracking_number'],
                        'status' => $result['status'],
                        'created_at' => $result['created_at'],
                        'buyer_name' => $result['buyer_name'],
                        'buyer_national_id' => $result['buyer_national_id'],
                        'buyer_phone' => $result['buyer_phone'],
                        'buyer_address' => $result['buyer_address']
                    ];

                    $propertyDetails = [
                        'district_name' => $result['district_name'],
                        'village' => $result['village'],
                        'block_name' => $result['block_name'],
                        'plot_number' => $result['plot_number'],
                        'block_number' => $result['block_number'],
                        'apartment_number' => $result['apartment_number']
                    ];

                    $sellerDetails = [
                        'User_Name' => $result['User_Name'],
                        'National_ID' => $result['User_NationalID'],
                        'Phone' => $result['User_Phone'],
                        'Address' => $result['User_Email']
                    ];
                } else {
                    $_SESSION['error'] = "No transfer request found with this tracking number";
                }
            }
        } catch (Exception $e) {
            if (isset($con) && $con->inTransaction()) {
                $con->rollBack();
            }
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }

        return $this->render('home.Employee.sellRequest', [
            'pendingRequests' => $pendingRequests ?? [],
            'transferDetails' => $transferDetails ?? null,
            'propertyDetails' => $propertyDetails ?? null,
            'sellerDetails' => $sellerDetails ?? null
        ]);
    }

    public function downloadDocument(): void
    {
        AuthMiddleware::requireEmployee();

        try {
            $trackingNumber = $_POST['tracking_number'] ?? $_GET['tracking_number'] ?? null;
            if ($trackingNumber === null || $trackingNumber === '') {
                throw new Exception("Tracking number is required");
            }
            $trackingNumber = (string)$trackingNumber;
            $docPath = PropertyTransfer::getDocumentPath($trackingNumber);
            if (empty($docPath)) {
                throw new Exception("No document found for this transfer request");
            }
            $documentAbsolutePath = $this->resolveStorageAbsolutePathOrFail($docPath);

            $fileSize = filesize($documentAbsolutePath);
            if ($fileSize === 0) {
                throw new Exception("Document file is empty");
            }

            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="transfer_document_' . $trackingNumber . '.pdf"');
            header('Content-Length: ' . $fileSize);
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            if (ob_get_level()) {
                ob_end_clean();
            }

            readfile($documentAbsolutePath);
            exit;
        } catch (Exception $e) {
            error_log("Document download error: " . $e->getMessage());

            $_SESSION['error'] = "Error displaying document: " . $e->getMessage();
            header('Location: sellRequest');
            exit;
        }
    }
}

