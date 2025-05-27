<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

// Ensure only admins can access this page
AuthMiddleware::requireAdmin();

// --- Admin Dashboard Logic ---
// Etherscan API details
$etherscan_api_key = '1A2RYWWYNQDPNGIQ1TNKHV9WWBBI63VKM5';
$contract_address = '0xfac2Cf4A0ECe7e748e5C7047Db1CA596462436AB';

// Fetch transactions from Etherscan
$tx_url = "https://api-sepolia.etherscan.io/api?module=account&action=txlist&address={$contract_address}&startblock=0&endblock=99999999&sort=asc&apikey={$etherscan_api_key}";

$options = [
    "http" => [
        "header" => "User-Agent: Mozilla/5.0\r\n"
    ]
];
$context = stream_context_create($options);
$tx_json = file_get_contents($tx_url, false, $context);

$tx_data = $tx_json ? json_decode($tx_json, true) : null;

// Generate the last 30 days (including today)
$days = [];
$tx_per_day = [];
for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $days[] = $date;
    $tx_per_day[$date] = 0;
}

// Count transactions per day for the last 30 days
if ($tx_data && isset($tx_data['result']) && is_array($tx_data['result'])) {
    foreach ($tx_data['result'] as $tx) {
        $tx_time = isset($tx['timeStamp']) ? (int)$tx['timeStamp'] : 0;
        $date = date('Y-m-d', $tx_time);
        if (isset($tx_per_day[$date])) {
            $tx_per_day[$date]++;
        }
    }
}
$tx_counts = array_values($tx_per_day);

// --- Mock values for overview cards (replace with real queries as needed) ---
$total_users = 123;
$total_assets = 45;
$total_orders = 67;
$total_transactions = array_sum($tx_counts);
?>

<section class="admin-dashboard">
    <h1 class="heading">Admin Dashboard</h1>
    <div class="dashboard-cards">

        <div class="dashboard-card">
            <h3><?= $total_users ?></h3>
            <p>Total Users</p>
        </div>
        <div class="dashboard-card">
            <h3><?= $total_assets ?></h3>
            <p>Total Assets</p>
        </div>
        <div class="dashboard-card">
            <h3><?= $total_orders ?></h3>
            <p>Total Orders</p>
        </div>
        <div class="dashboard-card">
            <h3><?= $total_transactions ?></h3>
            <p>Smart Contract Transactions</p>
        </div>
    </div>
    <div class="dashboard-chart-container">
        <canvas id="txChart" height="100"></canvas>
    </div>
</section>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('txChart').getContext('2d');
const txChart = new Chart(ctx, {
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
</script>

<!-- Recent Transactions Table -->
<section class="dashboard-recent-tx">
    <h2 class="heading" style="font-size:1.7rem;margin-top:2rem;">Recent Transactions</h2>
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
                <?php if ($tx_data && isset($tx_data['result']) && is_array($tx_data['result']) && count($tx_data['result']) > 0): ?>
                    <?php foreach (array_slice(array_reverse($tx_data['result']), 0, 10) as $tx): ?>
                        <tr>
                            <td><?= date('Y-m-d H:i', (int)$tx['timeStamp']) ?></td>
                            <td><?= substr($tx['from'], 0, 8) . '...' ?></td>
                            <td><?= $tx['to'] ? substr($tx['to'], 0, 8) . '...' : 'Contract Creation' ?></td>
                            <td><a href="https://sepolia.etherscan.io/tx/<?= $tx['hash'] ?>" class="inline-btn tx-link" target="_blank">View</a></td>
                            <td><span class="status-pill<?= $tx['isError'] === '0' ? '' : ' status-failed' ?>"><?= $tx['isError'] === '0' ? 'Success' : 'Failed' ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No transactions found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<style>
.admin-dashboard {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}
.dashboard-cards {
    display: flex;
    gap: 2rem;
    margin-bottom: 3rem;
    flex-wrap: wrap;
}
.dashboard-card {
    flex: 1 1 200px;
    background: var(--white);
    border-radius: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    padding: 2rem 1.5rem;
    text-align: center;
    border: var(--border);
}
.dashboard-card h3 {
    font-size: 2.8rem;
    color: var(--main-color);
    margin-bottom: 0.5rem;
}
.dashboard-card p {
    font-size: 1.3rem;
    color: var(--light-color);
    margin: 0;
}
.dashboard-chart-container {
    background: var(--white);
    border-radius: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    padding: 2rem;
    border: var(--border);
}
.dashboard-recent-tx .table-container {
    overflow-x: auto;
    border-radius: .9rem;
    box-shadow: 0 2px 14px rgba(0, 0, 0, 0.13);
    background-color: var(--white);
    padding: 0rem;
    margin-top: 2.5rem;
}
.dashboard-recent-tx table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 1.7rem;
}
.dashboard-recent-tx th, .dashboard-recent-tx td {
    padding: 1.5rem 1.2rem;
    text-align: center;
    border-bottom: 1px solid var(--border);
    font-size: 1.35rem;
}
.dashboard-recent-tx th {
    background-color: var(--light-bg);
    color: var(--black);
    text-transform: uppercase;
    font-size: 1.25rem;
    letter-spacing: 0.7px;
}
.dashboard-recent-tx tr:nth-child(even) {
    background-color: #f7f7f7;
}
.dashboard-recent-tx tr:nth-child(odd) {
    background-color: var(--white);
}
.dashboard-recent-tx tr:hover {
    background-color: var(--main-color);
    color: var(--white);
}
.dashboard-recent-tx td a.tx-link, .dashboard-recent-tx td a.inline-btn {
    display: inline-block;
    padding: 0.7rem 2.2rem;
    background: var(--main-color);
    color: #fff !important;
    border-radius: 0.5rem;
    font-size: 1.25rem;
    text-decoration: none;
    font-weight: 700;
    transition: background 0.18s;
    box-shadow: 0 1px 4px rgba(0,0,0,0.07);
    margin: 0 auto;
}
.dashboard-recent-tx td a.tx-link:hover, .dashboard-recent-tx td a.inline-btn:hover {
    background: var(--black);
}
.dashboard-recent-tx .status-pill {
    font-size: 1.25rem;
    padding: 0.5rem 1.7rem;
}
.dashboard-recent-tx .status-failed {
    background: #e74c3c;
}
.dashboard-recent-tx .heading {
    font-size: 2.3rem !important;
    margin-top: 2.5rem;
    margin-bottom: 1.5rem;
    text-align: left;
}
</style> 