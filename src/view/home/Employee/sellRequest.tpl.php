<?php
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);

$isEmployee = isset($_SESSION['role']) && (int)$_SESSION['role'] === \MVC\middleware\AuthMiddleware::ROLE_EMPLOYEE;
$trackingQuery = $_GET['tracking_number'] ?? '';
?>

<section class="admin-page sell-requests-page">
    <h1 class="heading">Transfer Review Queue</h1>

    <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success-message"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="card admin-page-card">
        <h3 class="title">Search by Tracking Number</h3>
        <p class="tutor">Open a specific request by tracking number to review parties, property details, and documents.</p>

        <form action="sellRequest" method="GET" class="admin-page-form">
            <label class="admin-page-label" for="tracking_number">Tracking Number</label>
            <div class="admin-search-row">
                <input id="tracking_number" type="text" name="tracking_number" class="box" required
                       placeholder="Enter tracking number…"
                       value="<?= htmlspecialchars((string)$trackingQuery) ?>">
                <button type="submit" class="inline-btn">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    View
                </button>
            </div>
        </form>
    </div>

    <?php if (empty($trackingQuery) && !empty($pendingRequests)): ?>
        <div class="card admin-page-card">
            <div class="admin-records-cardhead">
                <h3 class="title">Pending Requests</h3>
                <span class="status-pill"><?= number_format(count($pendingRequests)) ?> item(s)</span>
            </div>

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
                    <?php foreach ($pendingRequests as $r): ?>
                        <tr>
                            <td><span class="contract-address"><?= htmlspecialchars((string)$r['tracking_number']) ?></span></td>
                            <td><?= htmlspecialchars((string)$r['district_name']) ?>, <?= htmlspecialchars((string)$r['village']) ?></td>
                            <td><?= htmlspecialchars((string)$r['seller_name']) ?></td>
                            <td><?= htmlspecialchars((string)$r['buyer_name']) ?></td>
                            <td><?= htmlspecialchars((string)$r['created_at']) ?></td>
                            <td><a class="tx-link" href="sellRequest?tracking_number=<?= urlencode((string)$r['tracking_number']) ?>">Details</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif (empty($trackingQuery)): ?>
        <div class="card admin-page-card">
            <p class="tutor" style="margin:0;">No pending requests found.</p>
        </div>
    <?php endif; ?>

    <?php if (isset($transferDetails) && isset($propertyDetails) && isset($sellerDetails)): ?>
        <div class="card admin-page-card">
            <div class="admin-records-cardhead">
                <h3 class="title">Request Details</h3>
                <span class="status-pill<?= ($transferDetails['status'] ?? '') === 'pending' ? ' status-pending' : (($transferDetails['status'] ?? '') === 'rejected' ? ' status-failed' : '') ?>">
                    <?= htmlspecialchars((string)($transferDetails['status'] ?? '')) ?>
                </span>
            </div>

            <div class="admin-page-status">
                Tracking:
                <span class="contract-address"><?= htmlspecialchars((string)$transferDetails['tracking_number']) ?></span>
                <span style="margin-left:10px;color:var(--text-tertiary);">
                    Created: <?= htmlspecialchars((string)($transferDetails['created_at'] ?? '')) ?>
                </span>
            </div>

            <div class="box-container sell-requests-grid">
                <div class="box">
                    <h3 class="title">Property</h3>
                    <p><strong>District:</strong> <?= htmlspecialchars((string)$propertyDetails['district_name']) ?></p>
                    <p><strong>Village:</strong> <?= htmlspecialchars((string)$propertyDetails['village']) ?></p>
                    <p><strong>Block:</strong> <?= htmlspecialchars((string)$propertyDetails['block_name']) ?> / <?= htmlspecialchars((string)$propertyDetails['block_number']) ?></p>
                    <p><strong>Plot:</strong> <?= htmlspecialchars((string)$propertyDetails['plot_number']) ?></p>
                    <p><strong>Type:</strong> <?= htmlspecialchars(ucfirst((string)($propertyDetails['type'] ?? 'land'))) ?></p>
                    <p><strong>Area:</strong> <?= htmlspecialchars((string)(!empty($propertyDetails['area']) ? $propertyDetails['area'] . ' m²' : '—')) ?></p>
                </div>

                <div class="box">
                    <h3 class="title">Seller</h3>
                    <p><strong>Name:</strong> <?= htmlspecialchars((string)$sellerDetails['User_Name']) ?></p>
                    <p><strong>National ID:</strong> <?= htmlspecialchars((string)$sellerDetails['National_ID']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars((string)$sellerDetails['Phone']) ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars((string)$sellerDetails['Address']) ?></p>
                </div>

                <div class="box">
                    <h3 class="title">Buyer</h3>
                    <p><strong>Name:</strong> <?= htmlspecialchars((string)$transferDetails['buyer_name']) ?></p>
                    <p><strong>National ID:</strong> <?= htmlspecialchars((string)$transferDetails['buyer_national_id']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars((string)$transferDetails['buyer_phone']) ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars((string)$transferDetails['buyer_address']) ?></p>
                </div>

                <div class="box">
                    <h3 class="title">Documents</h3>
                    <?php if (($transferDetails['status'] ?? '') === 'completed'): ?>
                        <div class="admin-page-actions" style="margin-top:0;">
                            <a href="downloadDocument?tracking_number=<?= urlencode((string)$transferDetails['tracking_number']) ?>"
                               class="inline-btn" target="_blank" rel="noopener">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                View PDF
                            </a>
                            <button onclick="printDocument('<?= urlencode((string)$transferDetails['tracking_number']) ?>')"
                                    class="inline-btn" type="button">
                                <i class="fa-solid fa-print" aria-hidden="true"></i>
                                Print
                            </button>
                        </div>
                    <?php else: ?>
                        <p class="tutor" style="margin:0;">Document becomes available after completion.</p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (($transferDetails['status'] ?? '') === 'pending'): ?>
                <?php if ($isEmployee): ?>
                    <div class="admin-page-actions sell-requests-actions">
                        <form action="sellRequest" method="POST">
                            <?= \MVC\core\CSRFToken::generateFormField() ?>
                            <input type="hidden" name="tracking_number" value="<?= htmlspecialchars((string)$transferDetails['tracking_number']) ?>">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="inline-btn">
                                <i class="fa-solid fa-check" aria-hidden="true"></i>
                                Approve
                            </button>
                        </form>

                        <form action="sellRequest" method="POST">
                            <?= \MVC\core\CSRFToken::generateFormField() ?>
                            <input type="hidden" name="tracking_number" value="<?= htmlspecialchars((string)$transferDetails['tracking_number']) ?>">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="inline-btn">
                                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                                Reject
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="admin-page-status is-warning">Only employees can approve/reject requests.</div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>

<script>
function printDocument(trackingNumber) {
    // Open the document in a new window
    var printWindow = window.open('downloadDocument?tracking_number=' + trackingNumber, '_blank');
    
    // Wait for the PDF to load
    printWindow.onload = function() {
        // Print the document
        printWindow.print();
    };
}
</script>
