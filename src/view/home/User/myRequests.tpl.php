<?php
$requests = $requests ?? [];
$successFlash = $successFlash ?? null;
$errorFlash = $errorFlash ?? null;
?>

<section class="admin-page sell-req-page my-requests-page">
    <header class="registry-lookup-hero card">
        <div class="registry-lookup-hero__badge admin-portal-badge">
            <i class="fa-solid fa-folder-open" aria-hidden="true"></i>
            <span>My orders & requests</span>
        </div>
        <h1 class="heading registry-lookup-hero__title">Orders / requests and statuses</h1>
        <p class="registry-lookup-hero__lead">
            Review all requests you filed, track their status, and cancel pending submissions within 24 hours.
        </p>
    </header>

    <?php if (!empty($successFlash)): ?>
        <div class="success-message"><?= htmlspecialchars((string)$successFlash) ?></div>
    <?php endif; ?>
    <?php if (!empty($errorFlash)): ?>
        <div class="error-message"><?= htmlspecialchars((string)$errorFlash) ?></div>
    <?php endif; ?>

    <div class="card admin-page-card my-requests-card">
        <div class="admin-records-cardhead">
            <h2 class="title" style="margin:0;">Requests</h2>
            <span class="status-pill"><?= number_format(count($requests)) ?> total</span>
        </div>

        <?php if (empty($requests)): ?>
            <p class="tutor" style="margin-top:0.8rem;">No requests filed yet.</p>
        <?php else: ?>
            <div class="table-container my-requests-table">
                <table>
                    <thead>
                    <tr>
                        <th>Tracking</th>
                        <th>Property</th>
                        <th>Buyer</th>
                        <th>National ID</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Filed At</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($requests as $r): ?>
                        <?php
                        $createdAt = (string)($r['created_at'] ?? '');
                        $createdTs = strtotime($createdAt);
                        $within24h = $createdTs !== false && $createdTs >= (time() - 86400);
                        $isPending = (string)($r['status'] ?? '') === 'pending';
                        $canCancel = $isPending && $within24h;
                        $isCancelledBySeller = (int)($r['cancelled_by_seller'] ?? 0) === 1;
                        $statusText = $isCancelledBySeller ? 'cancelled by seller' : (string)($r['status'] ?? '');
                        ?>
                        <tr>
                            <td><span class="contract-address"><?= htmlspecialchars((string)($r['tracking_number'] ?? '')) ?></span></td>
                            <td>
                                <?= htmlspecialchars((string)($r['district_name'] ?? '')) ?>, <?= htmlspecialchars((string)($r['village'] ?? '')) ?>
                                <br><span class="tutor">Block <?= htmlspecialchars((string)($r['block_number'] ?? '')) ?> / Plot <?= htmlspecialchars((string)($r['plot_number'] ?? '')) ?></span>
                            </td>
                            <td><?= htmlspecialchars((string)($r['buyer_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string)($r['buyer_national_id'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string)($r['buyer_phone'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string)($r['buyer_email'] ?? '')) ?></td>
                            <td>
                                <span class="status-pill my-requests-status<?= $canCancel ? ' status-pending' : '' ?>">
                                    <?= htmlspecialchars($statusText) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($createdAt) ?></td>
                            <td>
                                <?php if ($canCancel): ?>
                                    <form method="POST" action="myRequests" onsubmit="return confirm('Cancel this request? This action cannot be undone.');">
                                        <?= \MVC\core\CSRFToken::generateFormField() ?>
                                        <input type="hidden" name="tracking_number" value="<?= htmlspecialchars((string)$r['tracking_number']) ?>">
                                        <button type="submit" class="inline-btn my-requests-cancel-btn">Cancel request</button>
                                    </form>
                                <?php else: ?>
                                    <?php
                                    $lockReason = 'Already processed';
                                    if ((string)($r['status'] ?? '') === 'pending' && !$within24h) {
                                        $lockReason = 'Expired (24h passed)';
                                    }
                                    if ($isCancelledBySeller) {
                                        $lockReason = 'Already cancelled';
                                    }
                                    ?>
                                    <button type="button" class="inline-btn my-requests-cancel-btn is-disabled" disabled aria-disabled="true" title="<?= htmlspecialchars($lockReason) ?>">
                                        Cancel request
                                    </button>
                                    <div class="tutor my-requests-lock-reason"><?= htmlspecialchars($lockReason) ?></div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="admin-page-help" style="margin-top:0.75rem;">
                Cancellation is allowed only within 24 hours from filing and only while status is pending.
            </p>
        <?php endif; ?>
    </div>
</section>

