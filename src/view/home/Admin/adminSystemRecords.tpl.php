<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';

use MVC\middleware\AuthMiddleware;

AuthMiddleware::requireAdmin();
?>

<section class="admin-page admin-system-records">
    <h1 class="heading">Search System Records</h1>

    <div class="card admin-page-card admin-records-hero">
        <div class="admin-records-hero__top">
            <div>
                <h3 class="title">Unified Search</h3>
                <p class="tutor">Search across properties and transfer requests by district, village, block, plot, tracking number, owner/seller/buyer info.</p>
            </div>
            <div class="admin-records-hero__stats">
                <div class="admin-records-stat">
                    <div class="admin-records-stat__label">Properties</div>
                    <div class="admin-records-stat__value"><?= number_format(count($properties ?? [])) ?></div>
                </div>
                <div class="admin-records-stat">
                    <div class="admin-records-stat__label">Transfers</div>
                    <div class="admin-records-stat__value"><?= number_format(count($transfers ?? [])) ?></div>
                </div>
            </div>
        </div>

        <form method="GET" action="adminSystemRecords" class="admin-page-form admin-records-form">
            <label class="admin-page-label" for="q">Search</label>
            <div class="admin-search-row">
                <input id="q" type="text" class="box" name="q" value="<?= htmlspecialchars((string)($q ?? '')) ?>" placeholder="District, village, block, plot, tracking number, national ID…">
                <button type="submit" class="inline-btn">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    Search
                </button>
            </div>

            <div class="admin-records-actions">
                <a class="tx-link" href="adminSystemRecords">Clear</a>
                <?php if (!empty($q)): ?>
                    <span class="admin-records-query">Searching: <span class="contract-address"><?= htmlspecialchars((string)$q) ?></span></span>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="card admin-page-card admin-records-results">
        <div class="admin-records-grid">
            <div class="admin-records-panel admin-records-properties">
                <div class="admin-records-cardhead">
                    <h3 class="title">Properties</h3>
                    <span class="status-pill"><?= number_format(count($properties ?? [])) ?> result(s)</span>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>District / Village</th>
                            <th>Block / Plot</th>
                            <th>Owner</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($properties)): ?>
                            <tr><td colspan="5">No property records found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($properties as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars((string)($p['id'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($p['district_name'] ?? '')) ?>, <?= htmlspecialchars((string)($p['village'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($p['block_name'] ?? '')) ?> / <?= htmlspecialchars((string)($p['plot_number'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($p['owner_name'] ?? '')) ?></td>
                                    <td><span class="status-pill"><?= htmlspecialchars((string)($p['status'] ?? '')) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="admin-records-panel admin-records-transfers">
                <div class="admin-records-cardhead">
                    <h3 class="title">Transfer Requests</h3>
                    <span class="status-pill"><?= number_format(count($transfers ?? [])) ?> result(s)</span>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                        <tr>
                            <th>Tracking</th>
                            <th>Property</th>
                            <th>Seller</th>
                            <th>Buyer</th>
                            <th>Status</th>
                            <th>Open</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($transfers)): ?>
                            <tr><td colspan="6">No transfer records found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($transfers as $t): ?>
                                <tr>
                                    <td><span class="contract-address"><?= htmlspecialchars((string)($t['tracking_number'] ?? '')) ?></span></td>
                                    <td><?= htmlspecialchars((string)($t['district_name'] ?? '')) ?>, <?= htmlspecialchars((string)($t['village'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($t['seller_name'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($t['buyer_name'] ?? '')) ?></td>
                                    <td><span class="status-pill<?= ($t['status'] ?? '') === 'rejected' ? ' status-failed' : (($t['status'] ?? '') === 'pending' ? ' status-pending' : '') ?>"><?= htmlspecialchars((string)($t['status'] ?? '')) ?></span></td>
                                    <td>
                                        <a class="tx-link" href="adminTransferQueue?tracking_number=<?= urlencode((string)($t['tracking_number'] ?? '')) ?>">Details</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

