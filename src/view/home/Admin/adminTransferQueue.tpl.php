<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';

use MVC\middleware\AuthMiddleware;

AuthMiddleware::requireAdmin();

$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>

<section class="admin-page">
    <h1 class="heading">Transfer Queue</h1>

    <?php if ($error): ?>
        <div class="admin-page-status is-error"><?= htmlspecialchars((string)$error) ?></div>
    <?php endif; ?>

    <div class="card admin-page-card">
        <h3 class="title">Search by Tracking Number</h3>
        <p class="tutor">Admins can review details. Approval/rejection is performed by employees only.</p>

        <form class="admin-page-form" method="GET" action="adminTransferQueue">
            <label class="admin-page-label" for="tracking_number">Tracking Number</label>
            <div class="admin-search-row">
                <input class="box" id="tracking_number" name="tracking_number" value="<?= htmlspecialchars((string)($tracking ?? '')) ?>" placeholder="e.g. TRK-..." required>
                <button type="submit" class="inline-btn">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    View
                </button>
            </div>
        </form>
    </div>

    <div class="admin-portal-layout" style="grid-template-columns: 1fr 360px;">
        <div class="admin-portal-main">
            <div class="card admin-page-card">
                <h3 class="title">Pending Requests (Latest)</h3>
                <p class="tutor">Click a tracking number to open details.</p>

                <div class="table-container">
                    <table>
                        <thead>
                        <tr>
                            <th>Tracking</th>
                            <th>Property</th>
                            <th>Seller</th>
                            <th>Buyer</th>
                            <th>Created</th>
                            <th>Open</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($pending_transfers_latest) && is_array($pending_transfers_latest)): ?>
                            <?php foreach ($pending_transfers_latest as $t): ?>
                                <tr>
                                    <td><span class="contract-address"><?= htmlspecialchars((string)($t['tracking_number'] ?? '')) ?></span></td>
                                    <td><?= htmlspecialchars((string)($t['district_name'] ?? '')) ?>, <?= htmlspecialchars((string)($t['village'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($t['seller_name'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($t['buyer_name'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($t['created_at'] ?? '')) ?></td>
                                    <td>
                                        <a class="tx-link" href="adminTransferQueue?tracking_number=<?= urlencode((string)($t['tracking_number'] ?? '')) ?>">Details</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6">No pending requests found.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if (!empty($transferDetails) && !empty($propertyDetails)): ?>
                <div class="card admin-page-card">
                    <h3 class="title">Transfer Details</h3>
                    <p class="tutor">Tracking: <span class="contract-address"><?= htmlspecialchars((string)$transferDetails['tracking_number']) ?></span></p>

                    <div class="box-container">
                        <div class="box">
                            <h3 class="title">Transfer</h3>
                            <p><strong>Status:</strong> <?= htmlspecialchars((string)($transferDetails['status'] ?? '')) ?></p>
                            <p><strong>Created:</strong> <?= htmlspecialchars((string)($transferDetails['created_at'] ?? '')) ?></p>
                            <p><strong>Updated:</strong> <?= htmlspecialchars((string)($transferDetails['updated_at'] ?? '')) ?></p>
                        </div>

                        <div class="box">
                            <h3 class="title">Property</h3>
                            <p><strong>District:</strong> <?= htmlspecialchars((string)($propertyDetails['district_name'] ?? '')) ?></p>
                            <p><strong>Village:</strong> <?= htmlspecialchars((string)($propertyDetails['village'] ?? '')) ?></p>
                            <p><strong>Block:</strong> <?= htmlspecialchars((string)($propertyDetails['block_name'] ?? '')) ?> / <?= htmlspecialchars((string)($propertyDetails['block_number'] ?? '')) ?></p>
                            <p><strong>Plot:</strong> <?= htmlspecialchars((string)($propertyDetails['plot_number'] ?? '')) ?></p>
                            <p><strong>Type:</strong> <?= htmlspecialchars(ucfirst((string)($propertyDetails['type'] ?? 'land'))) ?></p>
                            <p><strong>Area:</strong> <?= htmlspecialchars((string)(!empty($propertyDetails['area']) ? $propertyDetails['area'] . ' m²' : '—')) ?></p>
                        </div>

                        <div class="box">
                            <h3 class="title">Seller</h3>
                            <p><strong>Name:</strong> <?= htmlspecialchars((string)($sellerDetails['name'] ?? '')) ?></p>
                            <p><strong>National ID:</strong> <?= htmlspecialchars((string)($sellerDetails['national_id'] ?? '')) ?></p>
                            <p><strong>Phone:</strong> <?= htmlspecialchars((string)($sellerDetails['phone'] ?? '')) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars((string)($sellerDetails['email'] ?? '')) ?></p>
                        </div>

                        <div class="box">
                            <h3 class="title">Buyer</h3>
                            <p><strong>Name:</strong> <?= htmlspecialchars((string)($buyerDetails['name'] ?? '')) ?></p>
                            <p><strong>National ID:</strong> <?= htmlspecialchars((string)($buyerDetails['national_id'] ?? '')) ?></p>
                            <p><strong>Phone:</strong> <?= htmlspecialchars((string)($buyerDetails['phone'] ?? '')) ?></p>
                            <p><strong>Address:</strong> <?= htmlspecialchars((string)($buyerDetails['address'] ?? '')) ?></p>
                            <?php if (!empty($buyerDetails['email'])): ?>
                                <p><strong>Email:</strong> <?= htmlspecialchars((string)$buyerDetails['email']) ?></p>
                            <?php endif; ?>
                            <p><strong>Registered:</strong> <?= !empty($buyerDetails['is_registered']) ? 'Yes' : 'No' ?></p>
                        </div>
                    </div>

                    <div class="admin-page-status is-warning">
                        Approval and rejection are handled by employees only via the employee review page.
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <aside class="admin-portal-sidebar">
            <div class="card admin-page-card">
                <h3 class="title">Employee Review Page</h3>
                <p class="tutor">Use this only when handing off to the employee reviewer.</p>
                <div class="admin-page-actions">
                    <a class="inline-btn" href="sellRequest<?= !empty($transferDetails['tracking_number']) ? ('?tracking_number=' . urlencode((string)$transferDetails['tracking_number'])) : '' ?>">
                        <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                        Open Employee Review
                    </a>
                </div>
            </div>
        </aside>
    </div>
</section>

