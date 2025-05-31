<?php

namespace MVC\controller;

use MVC\middleware\AuthMiddleware;
use PDO;
use Exception;

class HomeController extends Controller
{
    public function index(): bool|array|string
    {
        return $this->render('home.index');
    }
    public function about(): bool|array|string
    {
        return $this->render('home.about');
    }
    public function team(): bool|array|string
    {
        return $this->render('home.team');
    }

    public function home(): bool|array|string
    {
        if (isset($_SESSION['Username'])) {
            if($_SESSION['role'] == 3){
                return $this->render('home.Admin.adminPortal');
            }
            else{
                if($_SESSION['role'] == 2) {
                    return $this->render('home.Employee.employeePortal');
                }
                else{
                    return $this->render('home.User.home');
                }
            }
        } else {
            header('Location: /login');
        }
    }

    public function login(): bool|array|string
    {
        return $this->render('home.login');
    }

    public function contact(): bool|array|string
    {
        return $this->render('home.contact');
    }
    public function connect(): bool|array|string
    {
        return $this->render('home.connect');
    }
    public function employee(): bool|array|string
    {
        AuthMiddleware::requireEmployee();
        return $this->render('home.Employee.employee');
    }
    public function ownedassets(): bool|array|string
    {
        AuthMiddleware::requireUser();

        try {
            $dsn = 'mysql:host=127.0.0.1;dbname=wise';
            $user = 'root';
            $pass = '994422Gg';
            $option = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);

            $con = new PDO($dsn, $user, $pass, $option);
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Get user ID from username
            $stmt = $con->prepare("SELECT User_ID, User_Name FROM user WHERE User_Name = ?");
            $stmt->execute([$_SESSION['Username']]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$userData) {
                throw new Exception("User not found");
            }

            $userId = $userData['User_ID'];

            // Get properties owned by the user
            $stmt = $con->prepare("
                SELECT 
                    p.id,
                    u.User_Name as owner_name,
                    p.district_name,
                    p.village,
                    p.block_name,
                    p.plot_number,
                    p.block_number,
                    p.apartment_number,
                    p.status,
                    p.created_at
                FROM properties p
                JOIN user u ON p.owner_id = u.User_ID
                WHERE p.owner_id = ?
                ORDER BY p.created_at DESC
            ");
            $stmt->execute([$userId]);
            $ownedProperties = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculate statistics
            $totalAssets = count($ownedProperties);
            $landCount = 0;
            $apartmentCount = 0;

            foreach ($ownedProperties as $property) {
                // Determine property type: Land if apartment_number is null or '-', otherwise Apartment
                $propertyType = (empty($property['apartment_number']) || $property['apartment_number'] === '-') ? 'land' : 'apartment';
                
                if ($propertyType === 'land') {
                    $landCount++;
                } else {
                    $apartmentCount++;
                }
            }

            // Get orders count for the user (as seller or buyer)
            $stmt = $con->prepare("SELECT COUNT(*) FROM property_transfers pt JOIN user u ON u.User_NationalID = pt.buyer_national_id WHERE pt.seller_id = ? OR pt.buyer_national_id = ?");
            
            // Get user's national ID for the buyer filter
            $stmtUserNationalID = $con->prepare("SELECT User_NationalID FROM user WHERE User_ID = ?");
            $stmtUserNationalID->execute([$userId]);
            $userNationalID = $stmtUserNationalID->fetchColumn();

            $stmt->execute([$userId, $userNationalID]);
            $ordersCount = $stmt->fetchColumn();

            return $this->render('home.User.ownedassets', [
                'ownedProperties' => $ownedProperties,
                'totalAssets' => $totalAssets,
                'ordersCount' => $ordersCount,
                'landCount' => $landCount,
                'apartmentCount' => $apartmentCount
            ]);

        } catch (Exception $e) {
            // Handle error appropriately
            return $this->render('home.User.ownedassets', ['error' => $e->getMessage(), 'ownedProperties' => [], 'totalAssets' => 0, 'ordersCount' => 0, 'landCount' => 0, 'apartmentCount' => 0]);
        }
    }
    public function profile(): bool|array|string
    {
        return $this->render('home.profile');
    }
    public function register(): bool|array|string
    {
        return $this->render('home.register');
    }
    public function interact(): bool|array|string
    {
        return $this->render('home.interact');
    }

    public function learnMore(): bool|array|string
    {
        return $this->render('home.learn-more');
    }

    public function adminAddress(): bool|array|string
    {
        AuthMiddleware::requireAdmin();
        return $this->render('home.Admin.adminAddress');
    }

    public function updateProfile(): bool|array|string
    {
        return $this->render('home.update-profile');
    }

    public function watchVideo(): bool|array|string
    {
        return $this->render('home.watch-video');
    }
    public function watchVideo2(): bool|array|string
    {
        return $this->render('home.watch-video2');
    }

    public function propertyRegistration(): bool|array|string
    {
        AuthMiddleware::requireEmployee();
        return $this->render('home.Employee.propertyRegistration');
    }

    public function propertyTransfer(): bool|array|string
    {
        AuthMiddleware::requireEmployee();
        return $this->render('home.Employee.propertyTransfer');
    }
    public function setEmpAuth(): bool|array|string
    {
        AuthMiddleware::requireAdmin();
        return $this->render('home.Admin.setEmpAuth');
    }
    public function qrScan(): bool|array|string
    {
        if (isset($_SESSION['Username'])) {
            if($_SESSION['role'] == 2) {
                return $this->render('home.Employee.qrScan');
            } else if($_SESSION['role'] == 3) {
                return $this->render('home.Admin.qrScan');
            } else {
                header('Location: /home');
            }
        } else {
            header('Location: /login');
        }
    }

    public function testEmp(): bool|array|string
    {
        return $this->render('home.testEmp');
    }

    public function checkEmpAuth(): bool|array|string
    {
        AuthMiddleware::requireAdmin();
        return $this->render('home.Admin.checkEmpAuth');
    }

    public function allProperties(): bool|array|string
    {
        AuthMiddleware::requireEmployee();
        return $this->render('home.Employee.allProperties');
    }

    public function PropertyById(): bool|array|string
    {
        AuthMiddleware::requireEmployee();
        return $this->render('home.Employee.PropertyById');
    }

    public function PropertyCount(): bool|array|string
    {
        AuthMiddleware::requireEmployee();
        return $this->render('home.Employee.PropertyCount');
    }

    public function PropertyInfo(): bool|array|string
    {
        AuthMiddleware::requireEmployee();
        return $this->render('home.Employee.PropertyInfo');
    }

    public function sell(): bool|array|string
    {
        AuthMiddleware::requireUser();
        return $this->render('home.User.sell');
    }

    public function dashboard(): bool|array|string
    {
        if (isset($_SESSION['Username'])) {
            if($_SESSION['role'] == 2) {
                return $this->render('home.Employee.dashboard');
            } else if($_SESSION['role'] == 3) {
                return $this->render('home.Admin.dashboard');
            } else {
                header('Location: /home');
            }
        } else {
            header('Location: /login');
        }
    }

    public function sellReq(): bool|array|string
    {
        AuthMiddleware::requireUser();
        
        // Handle POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $dsn = 'mysql:host=127.0.0.1;dbname=wise';
                $user = 'root';
                $pass = '994422Gg';
                $option = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);
                
                $con = new PDO($dsn, $user, $pass, $option);
                $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Get user ID from username
                $stmt = $con->prepare("SELECT User_ID FROM user WHERE User_Name = ?");
                $stmt->execute([$_SESSION['Username']]);
                $userData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$userData) {
                    throw new Exception("User not found");
                }
                
                // Generate tracking number
                $trackingNumber = 'TRK-' . strtoupper(bin2hex(random_bytes(6)));
                
                // Start transaction
                $con->beginTransaction();
                
                // Insert into property_transfers table
                $stmt = $con->prepare("INSERT INTO property_transfers (
                    property_id,
                    seller_id,
                    buyer_name,
                    buyer_national_id,
                    buyer_phone,
                    buyer_address,
                    tracking_number,
                    status,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
                
                $stmt->execute([
                    $_POST['property_id'],
                    $userData['User_ID'],
                    $_POST['buyer_name'],
                    $_POST['buyer_national_id'],
                    $_POST['buyer_phone'],
                    $_POST['buyer_address'],
                    $trackingNumber
                ]);
                
                // Update property status
                $stmt = $con->prepare("UPDATE properties SET status = 'pending_transfer' WHERE id = ? AND owner_id = ?");
                $stmt->execute([$_POST['property_id'], $userData['User_ID']]);
                
                $con->commit();
                
                // Store tracking number in session for display
                $_SESSION['tracking_number'] = $trackingNumber;
                
                // Redirect to prevent form resubmission
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
        
        // Handle GET request - render the form
        return $this->render('home.User.sellReq');
    }

    public function recentTransaction(): bool|array|string
    {
        AuthMiddleware::requireUser();
        return $this->render('home.User.recentTransaction');
    }

    public function orders(): bool|array|string
    {
        AuthMiddleware::requireUser();
        
        try {
            $dsn = 'mysql:host=127.0.0.1;dbname=wise';
            $user = 'root';
            $pass = '994422Gg';
            $option = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);
            
            $con = new PDO($dsn, $user, $pass, $option);
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Get user ID from username
            $stmt = $con->prepare("SELECT User_ID, User_NationalID FROM user WHERE User_Name = ?");
            $stmt->execute([$_SESSION['Username']]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$userData) {
                throw new Exception("User not found");
            }
            
            // Get all orders for the user (both as seller and buyer)
            $stmt = $con->prepare("
                SELECT 
                    pt.*,
                    CONCAT(p.district_name, ', ', p.village) as property_name,
                    CONCAT(p.block_name, ' Block ', p.block_number, ', Plot ', p.plot_number, 
                           CASE WHEN p.apartment_number IS NOT NULL THEN CONCAT(', Apt ', p.apartment_number) ELSE '' END) as property_location,
                    CASE 
                        WHEN pt.seller_id = ? THEN 'seller'
                        WHEN u.User_NationalID = pt.buyer_national_id THEN 'buyer'
                        ELSE 'unknown'
                    END as user_role
                FROM property_transfers pt
                JOIN properties p ON pt.property_id = p.id
                LEFT JOIN user u ON u.User_NationalID = pt.buyer_national_id
                WHERE pt.seller_id = ? OR pt.buyer_national_id = ?
                ORDER BY pt.created_at DESC
            ");
            $stmt->execute([$userData['User_ID'], $userData['User_ID'], $userData['User_NationalID']]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $this->render('home.User.Orders', ['orders' => $orders]);
            
        } catch (Exception $e) {
            // Handle error appropriately
            return $this->render('home.User.Orders', ['error' => $e->getMessage()]);
        }
    }

    public function employeePortal(): bool|array|string
    {
        AuthMiddleware::requireEmployee();
        return $this->render('home.Employee.employeePortal');
    }

    public function adminPortal(): bool|array|string
    {
        AuthMiddleware::requireAdmin();
        return $this->render('home.Admin.adminPortal');
    }

    public function sellRequest(): bool|array|string
    {
        AuthMiddleware::requireEmployee();
        
        try {
            $dsn = 'mysql:host=127.0.0.1;dbname=wise';
            $user = 'root';
            $pass = '994422Gg';
            $option = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);
            
            $con = new PDO($dsn, $user, $pass, $option);
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Handle POST request for approve/reject actions
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $con->beginTransaction();
                
                // Get the transfer details
                $stmt = $con->prepare("SELECT * FROM property_transfers WHERE tracking_number = ?");
                $stmt->execute([$_POST['tracking_number']]);
                $transfer = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$transfer) {
                    throw new Exception("Transfer request not found");
                }
                
                if ($_POST['action'] === 'approve') {
                    // First, get the buyer's user ID from their national ID
                    $stmt = $con->prepare("SELECT User_ID FROM user WHERE User_NationalID = ?");
                    $stmt->execute([$transfer['buyer_national_id']]);
                    $buyer = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$buyer) {
                        throw new Exception("Buyer not found in the system");
                    }
                    
                    // Update transfer status
                    $stmt = $con->prepare("UPDATE property_transfers SET status = 'completed' WHERE tracking_number = ?");
                    $stmt->execute([$_POST['tracking_number']]);
                    
                    // Update property status and owner
                    $stmt = $con->prepare("UPDATE properties SET status = 'transferred', owner_id = ? WHERE id = ?");
                    $stmt->execute([$buyer['User_ID'], $transfer['property_id']]);
                    
                    // Generate transfer document
                    $stmt = $con->prepare("
                        SELECT pt.*, p.*, u.User_Name, u.User_NationalID, u.User_Phone, u.User_Email
                        FROM property_transfers pt
                        JOIN properties p ON pt.property_id = p.id
                        JOIN user u ON pt.seller_id = u.User_ID
                        WHERE pt.tracking_number = ?
                    ");
                    $stmt->execute([$_POST['tracking_number']]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
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
                        
                        // Generate PDF document
                        $docGenerator = new \MVC\core\DocumentGenerator();
                        $docGenerator->generateTransferDocument($transferDetails, $propertyDetails, $sellerDetails, $buyerDetails);
                        
                        // Create storage directory if it doesn't exist
                        $storageDir = __DIR__ . '/../../storage/documents/transfers';
                        if (!file_exists($storageDir)) {
                            if (!mkdir($storageDir, 0777, true)) {
                                throw new Exception("Failed to create storage directory");
                            }
                        }
                        
                        // Save the document
                        $documentPath = $storageDir . '/' . $transferDetails['tracking_number'] . '.pdf';
                        $docGenerator->Output($documentPath, 'F');
                        
                        // Verify the file was created
                        if (!file_exists($documentPath)) {
                            throw new Exception("Failed to save document file");
                        }
                        
                        // Verify file size
                        if (filesize($documentPath) === 0) {
                            throw new Exception("Generated document file is empty");
                        }
                        
                        // Store document path in database
                        $stmt = $con->prepare("UPDATE property_transfers SET document_path = ? WHERE tracking_number = ?");
                        $stmt->execute([$documentPath, $transferDetails['tracking_number']]);
                        
                        // Verify the database update
                        $stmt = $con->prepare("SELECT document_path FROM property_transfers WHERE tracking_number = ?");
                        $stmt->execute([$transferDetails['tracking_number']]);
                        $checkResult = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if (empty($checkResult['document_path'])) {
                            throw new Exception("Failed to store document path in database");
                        }
                    }
                    
                    $_SESSION['success'] = "Transfer request approved successfully. Property ownership has been transferred.";
                } else {
                    // Update transfer status
                    $stmt = $con->prepare("UPDATE property_transfers SET status = 'rejected' WHERE tracking_number = ?");
                    $stmt->execute([$_POST['tracking_number']]);
                    
                    // Reset property status
                    $stmt = $con->prepare("UPDATE properties SET status = 'active' WHERE id = ?");
                    $stmt->execute([$transfer['property_id']]);
                    
                    $_SESSION['success'] = "Transfer request rejected";
                }
                
                $con->commit();
                header('Location: sellRequest');
                exit;
            }
            
            // Get all pending transfer requests
            $stmt = $con->prepare("
                SELECT pt.*, p.district_name, p.village, p.block_name, p.plot_number, 
                       p.block_number, p.apartment_number, u.User_Name as seller_name
                FROM property_transfers pt
                JOIN properties p ON pt.property_id = p.id
                JOIN user u ON pt.seller_id = u.User_ID
                WHERE pt.status = 'pending'
                ORDER BY pt.created_at DESC
            ");
            $stmt->execute();
            $pendingRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Handle GET request to view specific transfer details
            if (isset($_GET['tracking_number']) && !empty($_GET['tracking_number'])) {
                // Get transfer details with property and seller information
                $stmt = $con->prepare("
                    SELECT pt.*, p.*, u.User_Name, u.User_NationalID, u.User_Phone, u.User_Email
                    FROM property_transfers pt
                    JOIN properties p ON pt.property_id = p.id
                    JOIN user u ON pt.seller_id = u.User_ID
                    WHERE pt.tracking_number = ?
                ");
                $stmt->execute([$_GET['tracking_number']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
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
            if (!isset($_GET['tracking_number'])) {
                throw new Exception("Tracking number is required");
            }
            
            $dsn = 'mysql:host=127.0.0.1;dbname=wise';
            $user = 'root';
            $pass = '994422Gg';
            $option = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);
            
            $con = new PDO($dsn, $user, $pass, $option);
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Get document path
            $stmt = $con->prepare("SELECT document_path FROM property_transfers WHERE tracking_number = ? AND status = 'completed'");
            $stmt->execute([$_GET['tracking_number']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                throw new Exception("No document found for this transfer request");
            }
            
            if (empty($result['document_path'])) {
                throw new Exception("Document path is empty in the database");
            }
            
            if (!file_exists($result['document_path'])) {
                throw new Exception("Document file not found at path: " . $result['document_path']);
            }
            
            // Get file size
            $fileSize = filesize($result['document_path']);
            if ($fileSize === 0) {
                throw new Exception("Document file is empty");
            }
            
            // Set headers for PDF display
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="transfer_document_' . $_GET['tracking_number'] . '.pdf"');
            header('Content-Length: ' . $fileSize);
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            
            // Clear any previous output
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            // Output the PDF file
            readfile($result['document_path']);
            exit;
            
        } catch (Exception $e) {
            // Log the error for debugging
            error_log("Document download error: " . $e->getMessage());
            
            $_SESSION['error'] = "Error displaying document: " . $e->getMessage();
            header('Location: sellRequest');
            exit;
        }
    }
}
