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
                $this->requireValidCsrfOrRedirect('sellReq');

                $errors = [];

                [$propertyId, $e] = $this->postInt('property_id', true, 1, null);
                if ($e) {
                    $errors['property_id'][] = $e;
                }

                // Seller full name + national ID are display-only fields in UI.
                // We do not trust posted values for these two and force them from DB after loading user record.
                $sellerFullName = trim((string)($_POST['seller_full_name'] ?? ''));
                $sellerNationalId = trim((string)($_POST['seller_national_id'] ?? ''));

                [$sellerEmail, $e] = $this->postString('seller_email', 255, true);
                if ($e) {
                    $errors['seller_email'][] = $e;
                }
                if ($sellerEmail !== null && $sellerEmail !== '' && !filter_var($sellerEmail, FILTER_VALIDATE_EMAIL)) {
                    $errors['seller_email'][] = 'Enter a valid email address';
                }

                [$sellerPhone, $e] = $this->postString('seller_phone', 32, true);
                if ($e) {
                    $errors['seller_phone'][] = $e;
                }

                [$buyerName, $e] = $this->postString('buyer_name', 120, true);
                if ($e) {
                    $errors['buyer_name'][] = $e;
                }

                [$buyerNationalId, $e] = $this->postString('buyer_national_id', 32, true);
                if ($e) {
                    $errors['buyer_national_id'][] = $e;
                }
                if ($buyerNationalId !== null && $buyerNationalId !== '') {
                    $bd = User::normalizeNationalIdDigits((string)$buyerNationalId);
                    if (strlen($bd) < 6 || strlen($bd) > 32) {
                        $errors['buyer_national_id'][] = 'National ID must contain 6–32 digits';
                    }
                }

                [$buyerEmail, $e] = $this->postString('buyer_email', 255, true);
                if ($e) {
                    $errors['buyer_email'][] = $e;
                }
                if ($buyerEmail !== null && $buyerEmail !== '' && !filter_var($buyerEmail, FILTER_VALIDATE_EMAIL)) {
                    $errors['buyer_email'][] = 'Enter a valid email address';
                }

                [$buyerPhone, $e] = $this->postString('buyer_phone', 32, true);
                if ($e) {
                    $errors['buyer_phone'][] = $e;
                }

                [$buyerAddress, $e] = $this->postString('buyer_address', 255, true);
                if ($e) {
                    $errors['buyer_address'][] = $e;
                }

                $userData = User::findByUsername($_SESSION['Username']);
                if (!$userData) {
                    $_SESSION['error'] = 'Your session account could not be loaded.';
                    header('Location: sellReq?error=1' . ($propertyId ? '&property_id=' . (int)$propertyId : ''));
                    exit;
                }

                // Enforce immutable seller identity fields from DB (cannot be edited by client).
                $sellerFullName = User::registeredFullName($userData);
                $sellerNationalId = (string)($userData['User_NationalID'] ?? '');
                if ($sellerFullName === '') {
                    $errors['seller_full_name'][] = 'Seller name is missing in your account record.';
                }
                $sellerNidDigits = User::normalizeNationalIdDigits($sellerNationalId);
                if ($sellerNidDigits === '' || strlen($sellerNidDigits) < 6 || strlen($sellerNidDigits) > 32) {
                    $errors['seller_national_id'][] = 'Seller National ID is missing or invalid in your account record.';
                }

                $sellerErr = User::validateDeclaredIdentityMatchesUser(
                    $userData,
                    (string)$sellerFullName,
                    (string)$sellerNationalId,
                    (string)$sellerEmail,
                    (string)$sellerPhone
                );
                if ($sellerErr !== null) {
                    $errors['seller_identity'][] = $sellerErr;
                }

                $propertyOk = $propertyId !== null && Property::findByIdAndOwner((int)$propertyId, (int)$userData['User_ID']);
                if (!$propertyOk) {
                    $errors['property_id'][] = 'You can only file a transfer for a property you own.';
                } else {
                    $propStatus = strtolower((string)($propertyOk['status'] ?? ''));
                    if ($propStatus === Property::STATUS_PENDING_TRANSFER) {
                        $errors['property_id'][] = 'This property already has a pending transfer request.';
                    }
                    $pending = PropertyTransfer::findPendingByProperty((int)$propertyId);
                    if ($pending) {
                        $errors['property_id'][] = 'A transfer request is already pending for this property.';
                    }
                }

                $buyerRow = User::findByNationalID((string)$buyerNationalId);
                if (!$buyerRow) {
                    $errors['buyer_national_id'][] = 'No registered citizen account matches this national ID.';
                } else {
                    $buyerErr = User::validateDeclaredIdentityMatchesUser(
                        $buyerRow,
                        (string)$buyerName,
                        (string)$buyerNationalId,
                        (string)$buyerEmail,
                        (string)$buyerPhone
                    );
                    if ($buyerErr !== null) {
                        $errors['buyer_identity'][] = $buyerErr;
                    }
                    if ((int)$buyerRow['User_ID'] === (int)$userData['User_ID']) {
                        $errors['buyer_identity'][] = 'Buyer cannot be the same person as the seller.';
                    }
                }

                if (!empty($errors)) {
                    $_SESSION['error'] = 'Declaration does not match civil registry records, or the request is invalid. Please correct the details.';
                    $_SESSION['form_errors'] = $errors;
                    $_SESSION['form_old'] = [
                        'property_id' => $_POST['property_id'] ?? null,
                        'seller_full_name' => $_POST['seller_full_name'] ?? null,
                        'seller_national_id' => $_POST['seller_national_id'] ?? null,
                        'seller_email' => $_POST['seller_email'] ?? null,
                        'seller_phone' => $_POST['seller_phone'] ?? null,
                        'buyer_name' => $_POST['buyer_name'] ?? null,
                        'buyer_national_id' => $_POST['buyer_national_id'] ?? null,
                        'buyer_email' => $_POST['buyer_email'] ?? null,
                        'buyer_phone' => $_POST['buyer_phone'] ?? null,
                        'buyer_address' => $_POST['buyer_address'] ?? null,
                    ];
                    header('Location: sellReq?error=1' . ($propertyId ? '&property_id=' . (int)$propertyId : ''));
                    exit;
                }

                $trackingNumber = 'TRK-' . strtoupper(bin2hex(random_bytes(6)));

                $con = \Database::getConnection();
                $con->beginTransaction();

                // Re-check inside the transaction to avoid duplicate pending requests.
                $propRow = Property::findByIdAndOwner((int)$propertyId, (int)$userData['User_ID']);
                if (!$propRow) {
                    throw new Exception('Property not found for this user.');
                }
                if (strtolower((string)($propRow['status'] ?? '')) === Property::STATUS_PENDING_TRANSFER) {
                    throw new Exception('This property already has a pending transfer request.');
                }
                $pending = PropertyTransfer::findPendingByProperty((int)$propertyId);
                if ($pending) {
                    $tn = (string)($pending['tracking_number'] ?? '');
                    throw new Exception($tn !== '' ? ("A request is already pending for this property. Tracking: {$tn}") : 'A request is already pending for this property.');
                }

                PropertyTransfer::create([
                    'property_id' => $propertyId,
                    'seller_id' => (int)$userData['User_ID'],
                    'buyer_name' => $buyerName,
                    'buyer_national_id' => User::normalizeNationalIdDigits((string)$buyerNationalId),
                    'buyer_phone' => $buyerPhone,
                    'buyer_email' => strtolower(trim((string)$buyerEmail)),
                    'buyer_address' => $buyerAddress,
                    'tracking_number' => $trackingNumber,
                ]);
                Property::updateStatus($propertyId, 'pending_transfer', (int)$userData['User_ID']);
                $con->commit();

                // Generate a filing slip document (request stage) for citizen to present at DLS office.
                $result = PropertyTransfer::findTransferDetailsForDocument($trackingNumber);
                if ($result) {
                    $docGenerator = new \MVC\core\DocumentGenerator();
                    $docGenerator->generateTransferRequestSlip(
                        [
                            'tracking_number' => $trackingNumber,
                            'created_at' => (string)($result['created_at'] ?? date('Y-m-d H:i:s'))
                        ],
                        [
                            'district_name' => (string)($result['district_name'] ?? ''),
                            'village' => (string)($result['village'] ?? ''),
                            'block_name' => (string)($result['block_name'] ?? ''),
                            'block_number' => (string)($result['block_number'] ?? ''),
                            'plot_number' => (string)($result['plot_number'] ?? ''),
                            'type' => (string)($result['type'] ?? 'land'),
                            'area' => $result['area'] ?? null
                        ],
                        [
                            'name' => (string)($result['User_Name'] ?? ''),
                            'national_id' => (string)($result['User_NationalID'] ?? ''),
                            'email' => (string)($result['User_Email'] ?? ''),
                            'phone' => (string)($result['User_Phone'] ?? '')
                        ],
                        [
                            'buyer_name' => (string)($result['buyer_name'] ?? ''),
                            'buyer_national_id' => (string)($result['buyer_national_id'] ?? ''),
                            'buyer_email' => (string)($result['buyer_email'] ?? ''),
                            'buyer_phone' => (string)($result['buyer_phone'] ?? ''),
                            'buyer_address' => (string)($result['buyer_address'] ?? '')
                        ]
                    );

                    $reqDir = $this->buildStorageAbsolutePath('documents/requests');
                    if (!file_exists($reqDir)) {
                        if (!mkdir($reqDir, 0777, true)) {
                            throw new Exception("Failed to create request documents directory");
                        }
                    }
                    $reqDocAbs = $this->buildStorageAbsolutePath('documents/requests/' . $trackingNumber . '.pdf');
                    $docGenerator->Output($reqDocAbs, 'F');
                }

                $_SESSION['tracking_number'] = $trackingNumber;
                header('Location: sellReq?success=1' . ($propertyId ? '&property_id=' . (int)$propertyId : ''));
                exit;
            } catch (Exception $e) {
                if (isset($con)) {
                    $con->rollBack();
                }
                $_SESSION['error'] = 'Error processing your request: ' . $e->getMessage();
                $pid = (int)($_POST['property_id'] ?? 0);
                header('Location: sellReq?error=1' . ($pid ? '&property_id=' . $pid : ''));
                exit;
            }
        }

        // GET request - render the form with seller + property details (no DB access in view).
        $formErrors = $_SESSION['form_errors'] ?? [];
        $formOld = $_SESSION['form_old'] ?? [];
        unset($_SESSION['form_errors'], $_SESSION['form_old']);

        $errorFlash = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        $userDetails = null;
        $propertyDetails = null;
        $error = null;
        $pendingForSeller = null;

        $propertyIdGet = isset($_GET['property_id']) ? (int)$_GET['property_id'] : (int)($formOld['property_id'] ?? 0);

        if ($propertyIdGet > 0) {
            try {
                $userDetails = User::findByUsername($_SESSION['Username']);
                if (!$userDetails) {
                    throw new Exception('User not found');
                }
                $propertyDetails = Property::findByIdAndOwner($propertyIdGet, (int)$userDetails['User_ID']);
                if (!$propertyDetails) {
                    throw new Exception('Property not found for this user');
                }
                        $pendingForSeller = PropertyTransfer::findPendingByPropertyAndSeller($propertyIdGet, (int)$userDetails['User_ID']);
            } catch (Exception $e) {
                $error = 'Unable to load this property. Choose a parcel from “Sell” first.';
            }
        }

        $trackingFlash = null;
        if (!empty($_GET['success']) && isset($_SESSION['tracking_number'])) {
            $trackingFlash = (string)$_SESSION['tracking_number'];
            unset($_SESSION['tracking_number']);
        }

        return $this->render('home.User.sellReq', compact(
            'userDetails',
            'propertyDetails',
            'error',
            'formErrors',
            'formOld',
            'errorFlash',
            'trackingFlash',
            'pendingForSeller'
        ));
    }

    public function sellRequest(): bool|array|string
    {
        AuthMiddleware::requireStaff();

        try {
            $con = \Database::getConnection();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Only employees can approve/reject transfer requests.
                AuthMiddleware::requireEmployee();
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
                            'type' => $result['type'] ?? 'land',
                            'area' => $result['area'] ?? null
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
                            'buyer_email' => $result['buyer_email'] ?? '',
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
                        'type' => $result['type'] ?? 'land',
                        'area' => $result['area'] ?? null
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

    public function sellReqReceipt(): void
    {
        AuthMiddleware::requireLogin();

        try {
            $trackingNumber = (string)($_GET['tracking_number'] ?? '');
            if ($trackingNumber === '') {
                throw new Exception('Tracking number is required');
            }

            $userData = User::findByUsername((string)($_SESSION['Username'] ?? ''));
            if (!$userData) {
                throw new Exception('User account not found');
            }

            $isStaff = isset($_SESSION['role']) && (
                (int)$_SESSION['role'] === AuthMiddleware::ROLE_EMPLOYEE ||
                (int)$_SESSION['role'] === AuthMiddleware::ROLE_ADMIN
            );

            if (!$isStaff) {
                $ownedReq = PropertyTransfer::findByTrackingAndSeller($trackingNumber, (int)$userData['User_ID']);
                if (!$ownedReq) {
                    throw new Exception('You are not allowed to access this receipt');
                }
            }

            $relative = 'documents/requests/' . $trackingNumber . '.pdf';
            $abs = $this->resolveStorageAbsolutePathOrFail($relative);
            $size = filesize($abs);
            if ($size === 0) {
                throw new Exception('Receipt file is empty');
            }

            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="transfer_request_' . $trackingNumber . '.pdf"');
            header('Content-Length: ' . $size);
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            if (ob_get_level()) {
                ob_end_clean();
            }
            readfile($abs);
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error displaying request receipt: ' . $e->getMessage();
            header('Location: sellReq');
            exit;
        }
    }

    public function sellReqTrackingPopup(): bool|array|string
    {
        AuthMiddleware::requireLogin();

        $trackingNumber = (string)($_GET['tracking_number'] ?? '');
        if ($trackingNumber === '') {
            $_SESSION['error'] = 'Tracking number is required.';
            header('Location: sellReq');
            exit;
        }

        $userData = User::findByUsername((string)($_SESSION['Username'] ?? ''));
        if (!$userData) {
            $_SESSION['error'] = 'User account not found.';
            header('Location: sellReq');
            exit;
        }

        $isStaff = isset($_SESSION['role']) && (
            (int)$_SESSION['role'] === AuthMiddleware::ROLE_EMPLOYEE ||
            (int)$_SESSION['role'] === AuthMiddleware::ROLE_ADMIN
        );
        if (!$isStaff) {
            $ownedReq = PropertyTransfer::findByTrackingAndSeller($trackingNumber, (int)$userData['User_ID']);
            if (!$ownedReq) {
                $_SESSION['error'] = 'You are not allowed to view this tracking receipt.';
                header('Location: sellReq');
                exit;
            }
        }

        return $this->render('home.User.sellReqTrackingPopup', [
            'trackingNumber' => $trackingNumber
        ]);
    }

    public function myRequests(): bool|array|string
    {
        AuthMiddleware::requireUser();

        $userData = User::findByUsername((string)($_SESSION['Username'] ?? ''));
        if (!$userData) {
            $_SESSION['error'] = 'User account not found.';
            header('Location: home');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->requireValidCsrfOrRedirect('myRequests');
                $trackingNumber = trim((string)($_POST['tracking_number'] ?? ''));
                if ($trackingNumber === '') {
                    throw new Exception('Tracking number is required.');
                }

                $con = \Database::getConnection();
                $con->beginTransaction();

                $transfer = PropertyTransfer::findByTrackingAndSeller($trackingNumber, (int)$userData['User_ID']);
                if (!$transfer) {
                    throw new Exception('Request not found for your account.');
                }

                $ok = PropertyTransfer::cancelBySellerWithin24Hours($trackingNumber, (int)$userData['User_ID']);
                if (!$ok) {
                    throw new Exception('Cancellation is only available for pending requests filed within 24 hours.');
                }

                Property::updateStatus((int)$transfer['property_id'], 'active', (int)$userData['User_ID']);
                $con->commit();

                $_SESSION['success'] = 'Request cancelled successfully.';
            } catch (Exception $e) {
                if (isset($con) && $con->inTransaction()) {
                    $con->rollBack();
                }
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: myRequests');
            exit;
        }

        $requests = PropertyTransfer::findForSeller((int)$userData['User_ID'], 150);
        $successFlash = $_SESSION['success'] ?? null;
        $errorFlash = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        return $this->render('home.User.myRequests', [
            'requests' => $requests,
            'successFlash' => $successFlash,
            'errorFlash' => $errorFlash
        ]);
    }
}

