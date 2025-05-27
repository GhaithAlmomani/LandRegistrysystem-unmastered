<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dsn = 'mysql:host=127.0.0.1;dbname=wise';
    $user = 'root';
    $pass = '994422Gg';
    $option = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);
    
    try {
        $con = new PDO($dsn, $user, $pass, $option);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
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
            $_SESSION['user_id'],
            $_POST['buyer_name'],
            $_POST['buyer_national_id'],
            $_POST['buyer_phone'],
            $_POST['buyer_address'],
            $trackingNumber
        ]);
        
        // Update property status
        $stmt = $con->prepare("UPDATE properties SET status = 'pending_transfer' WHERE id = ?");
        $stmt->execute([$_POST['property_id']]);
        
        $con->commit();
        
        // Store tracking number in session for display
        $_SESSION['tracking_number'] = $trackingNumber;
        
        // Redirect to prevent form resubmission
        header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
        exit;
        
    } catch (PDOException $e) {
        $con->rollBack();
        $error = "Error processing your request. Please try again.";
    }
}

// Get property details if property_id is set
$propertyDetails = null;
if (isset($_GET['property_id'])) {
    try {
        $dsn = 'mysql:host=127.0.0.1;dbname=wise';
        $user = 'root';
        $pass = '994422Gg';
        $option = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);
        
        $con = new PDO($dsn, $user, $pass, $option);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get user details
        $stmt = $con->prepare("SELECT * FROM user WHERE User_Name = ?");
        $stmt->execute([$_SESSION['Username']]);
        $userDetails = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get property details
        $stmt = $con->prepare("SELECT * FROM properties WHERE id = ? AND owner_id = ?");
        $stmt->execute([$_GET['property_id'], $userDetails['User_ID']]);
        $propertyDetails = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error loading property details.";
    }
}
?>

<section>
    <div class="flex-container">

        <style>

            .flex-container {
                display: flex;
                justify-content: space-between;
                gap: 5rem;
                margin-bottom: 5rem;
            }

            .table-container {
                flex: 1;
                background-color: var(--white);
                border-radius: 1rem;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                padding: 2rem;
                transition: transform 0.3s ease;
            }

            .table-container:hover {
                transform: translateY(-5px);
                box-shadow: 0 5px 15px rgba(0, 86, 15, 0.15);
            }

            .table-container table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0 1rem;
            }

            .table-container th {
                text-align: center;
                padding: 1rem;
                font-size: 1.6rem;
                color: var(--black);
                border-bottom: 2px solid var(--main-color);
            }

            .table-container td {
                padding: 1rem;
                font-size: 1.4rem;
            }

            .table-container td:first-child {
                font-weight: 600;
                color: var(--black);
            }

            .table-container .box {
                width: 100%;
                padding: 1.2rem;
                border: 1px solid #ddd;
                border-radius: 0.5rem;
                font-size: 1.4rem;
                transition: all 0.3s ease;
                background-color: #f8f9fa;
            }

            .table-container .box:hover {
                border-color: var(--main-color);
                background-color: #fff;
            }

            .table-container .box:focus {
                border-color: var(--main-color);
                box-shadow: 0 0 0 2px rgba(0, 86, 15, 0.1);
                background-color: #fff;
            }

            .table-container .box::placeholder {
                color: #aaa;
            }

            .submit-container {
                text-align: center;
                margin-top: 2rem;
            }

            .submit-container .btn {
                padding: 1.2rem 4rem;
                font-size: 1.6rem;
                border-radius: 0.5rem;
                background-color: var(--main-color);
                color: #fff;
                border: none;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 2px 5px rgba(0, 86, 15, 0.2);
            }

            .submit-container .btn:hover {
                background-color: #003d06;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 86, 15, 0.2);
            }

            /* Popup Styling */
            .popup {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                justify-content: center;
                align-items: center;
                z-index: 1000;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .popup.active {
                opacity: 1;
            }

            .popup-content {
                background-color: #fff;
                border-radius: 1.2rem;
                padding: 3rem;
                text-align: center;
                max-width: 450px;
                width: 90%;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
                transform: translateY(-20px);
                transition: transform 0.3s ease;
                position: relative;
                border: 2px solid var(--main-color);
            }

            .popup.active .popup-content {
                transform: translateY(0);
            }

            .popup h2 {
                font-size: 2rem;
                margin-bottom: 1.5rem;
                color: var(--black);
            }

            .popup .tracking-container {
                display: flex;
                align-items: center;
                gap: 1rem;
                margin: 2rem 0;
            }

            .popup .tracking-number {
                font-size: 2.2rem;
                color: var(--main-color);
                font-weight: bold;
                padding: 1.5rem;
                background-color: #f8f9fa;
                border-radius: 0.8rem;
                border: 2px dashed var(--main-color);
                letter-spacing: 1px;
                flex: 1;
            }

            .popup .copy-btn {
                background-color: var(--main-color);
                color: white;
                border: none;
                padding: 1.5rem;
                border-radius: 0.8rem;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.6rem;
            }

            .popup .copy-btn:hover {
                background-color: #003d06;
                transform: translateY(-2px);
            }

            .popup .copy-btn.copied {
                background-color: #28a745;
            }

            .popup .save-message {
                font-size: 1.6rem;
                color: #666;
                margin-top: 1rem;
            }

            .close {
                position: absolute;
                top: 1.5rem;
                right: 1.5rem;
                font-size: 2.4rem;
                font-weight: bold;
                color: #666;
                cursor: pointer;
                width: 3rem;
                height: 3rem;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                transition: all 0.3s ease;
            }

            .close:hover {
                background-color: #f8f9fa;
                color: var(--main-color);
                transform: rotate(90deg);
            }

            .popup .icon {
                font-size: 4rem;
                color: var(--main-color);
                margin-bottom: 1.5rem;
                background-image: url('/images/pic-6.jpg');
                background-size: cover;
                width: 100px;
                height: 100px;
                border-radius: 50%;
                margin: 0 auto 1.5rem;
            }
        </style>

        <div class="table-container">
            <h1 class="heading">Seller Information</h1>
            <table>
                <tr>
                    <th>Field</th>
                    <th>Details</th>
                </tr>
                <tr>
                    <td>Full name</td>
                    <td><input type="text" name="seller_name" value="<?= htmlspecialchars($userDetails['User_Name'] ?? '') ?>" class="box" readonly /></td>
                </tr>
                <tr>
                    <td>National ID</td>
                    <td><input type="text" name="seller_national_id" value="<?= htmlspecialchars($userDetails['National_ID'] ?? '') ?>" class="box" readonly /></td>
                </tr>
                <tr>
                    <td>Phone</td>
                    <td><input type="tel" name="seller_phone" value="<?= htmlspecialchars($userDetails['Phone'] ?? '') ?>" class="box" readonly /></td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td><input type="text" name="seller_address" value="<?= htmlspecialchars($userDetails['Address'] ?? '') ?>" class="box" readonly /></td>
                </tr>
            </table>
        </div>

        <form method="POST" action="/sellReq" id="transferForm">
            <?= \MVC\core\CSRFToken::generateFormField() ?>
            <input type="hidden" name="property_id" value="<?= htmlspecialchars($_GET['property_id'] ?? '') ?>">
            
            <div class="table-container">
                <h1 class="heading">Buyer Information</h1>
                <table>
                    <tr>
                        <th>Field</th>
                        <th>Details</th>
                    </tr>
                    <tr>
                        <td>Full name</td>
                        <td><input type="text" name="buyer_name" placeholder="Enter buyer's full name" class="box" required /></td>
                    </tr>
                    <tr>
                        <td>National ID</td>
                        <td><input type="text" name="buyer_national_id" placeholder="Enter buyer's national ID" class="box" required /></td>
                    </tr>
                    <tr>
                        <td>Phone</td>
                        <td><input type="tel" name="buyer_phone" placeholder="Enter buyer's phone number" class="box" required /></td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td><input type="text" name="buyer_address" placeholder="Enter buyer's address" class="box" required /></td>
                    </tr>
                </table>
            </div>
        </form>
    </div>

    <div class="submit-container">
        <button type="submit" form="transferForm" class="btn">Submit</button>
    </div>

    <?php if (isset($error)): ?>
    <div class="error-message" style="color: red; text-align: center; margin-top: 1rem;">
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <!-- Popup Modal -->
    <div id="popup" class="popup <?= isset($_SESSION['tracking_number']) ? 'active' : '' ?>">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <div class="icon">✓</div>
            <h2>Request Submitted Successfully!</h2>
            <div class="tracking-container">
                <p class="tracking-number" id="tracking-number"><?= htmlspecialchars($_SESSION['tracking_number'] ?? '') ?></p>
                <button class="copy-btn" onclick="copyTrackingNumber()">Copy</button>
            </div>
            <p class="save-message">Please save this tracking number for future reference</p>
        </div>
    </div>

    <script>
        function closePopup() {
            const popup = document.getElementById('popup');
            popup.classList.remove('active');
            setTimeout(() => {
                popup.style.display = 'none';
            }, 300);
        }

        function copyTrackingNumber() {
            const trackingNumber = document.getElementById('tracking-number').innerText;
            navigator.clipboard.writeText(trackingNumber).then(() => {
                const copyBtn = document.querySelector('.copy-btn');
                copyBtn.textContent = 'Copied!';
                copyBtn.classList.add('copied');
                
                setTimeout(() => {
                    copyBtn.textContent = 'Copy';
                    copyBtn.classList.remove('copied');
                }, 2000);
            });
        }

        // Show popup if tracking number exists
        <?php if (isset($_SESSION['tracking_number'])): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const popup = document.getElementById('popup');
            popup.style.display = 'flex';
        });
        <?php endif; ?>
    </script>

</section>

