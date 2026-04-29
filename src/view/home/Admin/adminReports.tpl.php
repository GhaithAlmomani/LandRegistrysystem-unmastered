<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

AuthMiddleware::requireAdmin();

$from = $from ?? '';
$to = $to ?? '';
$status = $status ?? '';
?>

<section class="admin-reports-page">
    <h1 class="heading">Admin Reports</h1>

    <div class="card admin-reports-card">
        <h3 class="title">Transfers Report (CSV)</h3>
        <p class="tutor">Download transfer requests with property + seller/buyer details. CSV opens directly in Excel.</p>

        <form class="admin-reports-form" method="GET" action="adminReports">
            <div class="admin-reports-grid">
                <div>
                    <label class="admin-reports-label" for="from">From</label>
                    <input class="box" type="date" id="from" name="from" value="<?= htmlspecialchars((string)$from) ?>">
                </div>

                <div>
                    <label class="admin-reports-label" for="to">To</label>
                    <input class="box" type="date" id="to" name="to" value="<?= htmlspecialchars((string)$to) ?>">
                </div>

                <div>
                    <label class="admin-reports-label" for="status">Status</label>
                    <select class="box" id="status" name="status">
                        <option value="" <?= $status === '' ? 'selected' : '' ?>>All</option>
                        <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
                    </select>
                </div>
            </div>

            <div class="admin-reports-actions">
                <button type="submit" class="inline-btn">
                    <i class="fa-solid fa-filter" aria-hidden="true"></i>
                    Apply Filters
                </button>

                <a class="inline-btn" href="adminReports/download?type=transfers&amp;from=<?= urlencode((string)$from) ?>&amp;to=<?= urlencode((string)$to) ?>&amp;status=<?= urlencode((string)$status) ?>">
                    <i class="fa-solid fa-file-arrow-down" aria-hidden="true"></i>
                    Download CSV
                </a>
            </div>
        </form>

        <div class="admin-reports-note">
            <div class="admin-portal-alert is-ok">
                <div class="admin-portal-alert__main">
                    <div class="admin-portal-alert__label">Tip</div>
                    <div class="admin-portal-alert__value">Excel</div>
                </div>
                <div class="admin-portal-alert__hint">If Arabic text looks wrong in Excel, use “Data → From Text/CSV” and choose UTF‑8.</div>
            </div>
        </div>
    </div>
</section>

