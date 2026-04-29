<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

// Ensure only admins can access this page
AuthMiddleware::requireAdmin();
?>

<section class="admin-dashboard">
    <h1 class="heading">Analytics Overview</h1>

    <div class="card admin-page-card">
        <h3 class="title">System Totals</h3>
        <p class="tutor">High-level volume indicators for the registry platform.</p>

        <div class="dashboard-cards">
            <div class="dashboard-card">
                <h3><?= (int)($total_users ?? 0) ?></h3>
                <p>Total Users</p>
            </div>
            <div class="dashboard-card">
                <h3><?= (int)($total_assets ?? 0) ?></h3>
                <p>Total Assets</p>
            </div>
            <div class="dashboard-card">
                <h3><?= (int)($total_orders ?? 0) ?></h3>
                <p>Total Transfer Requests</p>
            </div>
            <div class="dashboard-card">
                <h3><?= (int)($total_transactions ?? 0) ?></h3>
                <p>Smart Contract Transactions (30d)</p>
            </div>
        </div>

        <div class="dashboard-chart-container">
            <canvas id="txChart" height="100"></canvas>
        </div>
    </div>
</section>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
const el = document.getElementById('txChart');
if (!el) return;
const ctx = el.getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($days) ?>,
        datasets: [{
            label: 'Transactions per Day',
            data: <?= json_encode($tx_counts) ?>,
            backgroundColor: 'rgba(0, 86, 15, 0.7)',
            borderColor: 'rgba(0, 86, 15, 1)',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            title: {
                display: true,
                text: 'Smart Contract Transactions (Last 30 Days)'
            }
        },
        scales: {
            x: {
                ticks: { 
                    color: '#2c3e50', 
                    font: { size: 12 }, 
                    autoSkip: false, // Force all dates to show
                    maxRotation: 45,
                    minRotation: 45
                },
                grid: { display: false }
            },
            y: {
                beginAtZero: true,
                max: 100,
                ticks: { color: '#2c3e50', font: { size: 12 } },
                grid: { color: '#eee' }
            }
        }
    }
});
(})();
</script>

<!-- Recent Transactions Table -->
<section class="dashboard-recent-tx">
    <h2 class="heading">Recent Smart Contract Transactions</h2>
    <div class="card admin-page-card">
        <div class="table-container">
            <table>
                <thead>
                <tr>
                    <th>Date</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Tx Hash</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($tx_data) && isset($tx_data['result']) && is_array($tx_data['result']) && count($tx_data['result']) > 0): ?>
                    <?php foreach (array_slice(array_reverse($tx_data['result']), 0, 10) as $tx): ?>
                        <tr>
                            <td><?= date('Y-m-d H:i', (int)($tx['timeStamp'] ?? 0)) ?></td>
                            <td><?= isset($tx['from']) ? (substr($tx['from'], 0, 8) . '...') : '' ?></td>
                            <td><?= !empty($tx['to']) ? (substr((string)$tx['to'], 0, 8) . '...') : 'Contract Creation' ?></td>
                            <td>
                                <?php if (!empty($tx['hash'])): ?>
                                    <a href="https://sepolia.etherscan.io/tx/<?= htmlspecialchars((string)$tx['hash']) ?>" class="inline-btn tx-link" target="_blank" rel="noopener">View</a>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td><span class="status-pill<?= (($tx['isError'] ?? '0') === '0') ? '' : ' status-failed' ?>"><?= (($tx['isError'] ?? '0') === '0') ? 'Success' : 'Failed' ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No transactions found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>