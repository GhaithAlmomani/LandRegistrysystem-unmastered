<?php
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);
?>

<section>
    <h1 class="heading">Sell Request Details</h1>

    <div class="search-container" style="margin-bottom: 2rem;">
        <form action="sellRequest" method="GET" class="search-form">
            <input type="text" name="tracking_number" placeholder="Enter tracking number" class="box" required 
                   value="<?= htmlspecialchars($_GET['tracking_number'] ?? '') ?>">
            <button type="submit" class="btn">Search</button>
        </form>
    </div>

    <?php if ($error): ?>
        <div class="error-message" style="color: red; text-align: center; margin: 1rem 0;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success-message" style="color: green; text-align: center; margin: 1rem 0;">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($_GET['tracking_number']) && !empty($pendingRequests)): ?>
        <div class="pending-requests">
            <h2>Pending Transfer Requests</h2>
            <div class="box-container">
                <?php foreach ($pendingRequests as $request): ?>
                    <div class="box">
                        <h3>Transfer Request #<?= htmlspecialchars($request['tracking_number']) ?></h3>
                        <p><strong>Property:</strong> <?= htmlspecialchars($request['district_name']) ?>, <?= htmlspecialchars($request['village']) ?></p>
                        <p><strong>Seller:</strong> <?= htmlspecialchars($request['seller_name']) ?></p>
                        <p><strong>Buyer:</strong> <?= htmlspecialchars($request['buyer_name']) ?></p>
                        <p><strong>Created:</strong> <?= htmlspecialchars($request['created_at']) ?></p>
                        <div class="action-buttons" style="display: flex; gap: 1rem; margin-top: 1rem;">
                            <form action="sellRequest" method="POST" style="flex: 1;">
                                <?= \MVC\core\CSRFToken::generateFormField() ?>
                                <input type="hidden" name="tracking_number" value="<?= htmlspecialchars($request['tracking_number']) ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn" style="background-color: #28a745; width: 100%;">Approve</button>
                            </form>
                            <form action="sellRequest" method="POST" style="flex: 1;">
                                <?= \MVC\core\CSRFToken::generateFormField() ?>
                                <input type="hidden" name="tracking_number" value="<?= htmlspecialchars($request['tracking_number']) ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="btn" style="background-color: #dc3545; width: 100%;">Reject</button>
                            </form>
                        </div>
                        <a href="sellRequest?tracking_number=<?= urlencode($request['tracking_number']) ?>" class="inline-btn" style="margin-top: 1rem; display: block; text-align: center;">View Details</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($transferDetails)): ?>
        <div class="details-container">
            <div class="box-container">
                <div class="box">
                    <h3>Transfer Information</h3>
                    <p><strong>Tracking Number:</strong> <?= htmlspecialchars($transferDetails['tracking_number']) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($transferDetails['status']) ?></p>
                    <p><strong>Created At:</strong> <?= htmlspecialchars($transferDetails['created_at']) ?></p>
                </div>

                <div class="box">
                    <h3>Property Information</h3>
                    <p><strong>District:</strong> <?= htmlspecialchars($propertyDetails['district_name']) ?></p>
                    <p><strong>Village:</strong> <?= htmlspecialchars($propertyDetails['village']) ?></p>
                    <p><strong>Block Name:</strong> <?= htmlspecialchars($propertyDetails['block_name']) ?></p>
                    <p><strong>Plot Number:</strong> <?= htmlspecialchars($propertyDetails['plot_number']) ?></p>
                    <p><strong>Block Number:</strong> <?= htmlspecialchars($propertyDetails['block_number']) ?></p>
                    <p><strong>Apartment Number:</strong> <?= htmlspecialchars($propertyDetails['apartment_number']) ?></p>
                </div>

                <div class="box">
                    <h3>Seller Information</h3>
                    <p><strong>Name:</strong> <?= htmlspecialchars($sellerDetails['User_Name']) ?></p>
                    <p><strong>National ID:</strong> <?= htmlspecialchars($sellerDetails['National_ID']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($sellerDetails['Phone']) ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($sellerDetails['Address']) ?></p>
                </div>

                <div class="box">
                    <h3>Buyer Information</h3>
                    <p><strong>Name:</strong> <?= htmlspecialchars($transferDetails['buyer_name']) ?></p>
                    <p><strong>National ID:</strong> <?= htmlspecialchars($transferDetails['buyer_national_id']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($transferDetails['buyer_phone']) ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($transferDetails['buyer_address']) ?></p>
                </div>
            </div>

            <?php if ($transferDetails['status'] === 'pending'): ?>
                <div class="action-buttons" style="text-align: center; margin-top: 2rem;">
                    <form action="sellRequest" method="POST" style="display: inline-block;">
                        <?= \MVC\core\CSRFToken::generateFormField() ?>
                        <input type="hidden" name="tracking_number" value="<?= htmlspecialchars($transferDetails['tracking_number']) ?>">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="btn" style="background-color: #28a745;">Approve</button>
                    </form>
                    <form action="sellRequest" method="POST" style="display: inline-block; margin-left: 1rem;">
                        <?= \MVC\core\CSRFToken::generateFormField() ?>
                        <input type="hidden" name="tracking_number" value="<?= htmlspecialchars($transferDetails['tracking_number']) ?>">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="btn" style="background-color: #dc3545;">Reject</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>

<style>
    .search-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 2rem;
        background-color: var(--white);
        border-radius: 1rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .search-form {
        display: flex;
        gap: 1rem;
    }

    .search-form .box {
        flex: 1;
        padding: 1rem;
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        font-size: 1.4rem;
    }

    .search-form .btn {
        padding: 1rem 2rem;
        background-color: var(--main-color);
        color: white;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .search-form .btn:hover {
        background-color: #003d06;
    }

    .details-container {
        margin-top: 2rem;
    }

    .box-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .box {
        background-color: var(--white);
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .box h3 {
        font-size: 1.8rem;
        color: var(--black);
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--main-color);
    }

    .box p {
        font-size: 1.4rem;
        margin-bottom: 1rem;
        color: var(--light-color);
    }

    .box p strong {
        color: var(--black);
        margin-right: 0.5rem;
    }

    .action-buttons .btn {
        padding: 1rem 3rem;
        font-size: 1.6rem;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .action-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .pending-requests {
        margin-top: 2rem;
    }

    .pending-requests h2 {
        font-size: 2rem;
        color: var(--black);
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .inline-btn {
        display: inline-block;
        padding: 0.8rem 1.5rem;
        background-color: var(--main-color);
        color: white;
        text-decoration: none;
        border-radius: 0.5rem;
        margin-top: 1rem;
        transition: all 0.3s ease;
    }

    .inline-btn:hover {
        background-color: #003d06;
        transform: translateY(-2px);
    }
</style> 