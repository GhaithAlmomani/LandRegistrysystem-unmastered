<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

// Ensure only admins can access this page
AuthMiddleware::requireAdmin();
?>

<section class="admin-portal home-grid">
    <div class="admin-portal-layout">
        <div class="admin-portal-main">
            <header class="admin-portal-header card">
                <div class="admin-portal-header__badge admin-portal-badge">
                    <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                    <span>Admin Console</span>
                </div>

                <div class="admin-portal-header__top">
                    <div>
                        <h1 class="heading admin-portal-title">Department of Land & Survey — Jordan</h1>
                        <p class="admin-portal-subtitle">
                            Operational oversight for land registration, title management, surveys, and secure staff access.
                        </p>
                    </div>
                    <div class="admin-portal-header__meta">
                        <span class="admin-portal-meta-pill">
                            <i class="fa-solid fa-calendar-days" aria-hidden="true"></i>
                            Last 30 days
                        </span>
                        <span class="admin-portal-meta-pill">
                            <i class="fa-solid fa-clock" aria-hidden="true"></i>
                            <?= date('Y-m-d H:i') ?>
                        </span>
                    </div>
                </div>

                <div class="admin-portal-kpis">
                    <div class="admin-portal-kpi">
                        <div class="admin-portal-kpi__top">
                            <div class="admin-portal-kpi__icon"><i class="fa-solid fa-hourglass-half" aria-hidden="true"></i></div>
                            <div class="admin-portal-kpi__label">Pending Transfer Reviews</div>
                        </div>
                        <div class="admin-portal-kpi__value"><?= number_format((int)($pending_transfer_reviews ?? 0)) ?></div>
                        <div class="admin-portal-kpi__hint">Requests awaiting administrative decision</div>
                    </div>
                    <div class="admin-portal-kpi">
                        <div class="admin-portal-kpi__top">
                            <div class="admin-portal-kpi__icon"><i class="fa-solid fa-file-circle-check" aria-hidden="true"></i></div>
                            <div class="admin-portal-kpi__label">Completed Transfers (30d)</div>
                        </div>
                        <div class="admin-portal-kpi__value"><?= number_format((int)($completed_transfers_30d ?? 0)) ?></div>
                        <div class="admin-portal-kpi__hint">Finalized ownership changes</div>
                    </div>
                    <div class="admin-portal-kpi">
                        <div class="admin-portal-kpi__top">
                            <div class="admin-portal-kpi__icon"><i class="fa-solid fa-circle-xmark" aria-hidden="true"></i></div>
                            <div class="admin-portal-kpi__label">Rejected Requests (30d)</div>
                        </div>
                        <div class="admin-portal-kpi__value"><?= number_format((int)($rejected_transfers_30d ?? 0)) ?></div>
                        <div class="admin-portal-kpi__hint">Declined requests requiring follow-up</div>
                    </div>
                    <div class="admin-portal-kpi">
                        <div class="admin-portal-kpi__top">
                            <div class="admin-portal-kpi__icon"><i class="fa-solid fa-arrows-rotate" aria-hidden="true"></i></div>
                            <div class="admin-portal-kpi__label">Properties In Transfer</div>
                        </div>
                        <div class="admin-portal-kpi__value"><?= number_format((int)($properties_pending_transfer ?? 0)) ?></div>
                        <div class="admin-portal-kpi__hint">Marked as pending transfer in registry</div>
                    </div>
                    <div class="admin-portal-kpi">
                        <div class="admin-portal-kpi__top">
                            <div class="admin-portal-kpi__icon"><i class="fa-brands fa-ethereum" aria-hidden="true"></i></div>
                            <div class="admin-portal-kpi__label">Blockchain Transactions (30d)</div>
                        </div>
                        <div class="admin-portal-kpi__value">
                            <?= ($blockchain_tx_30d ?? null) === null ? 'N/A' : number_format((int)$blockchain_tx_30d) ?>
                        </div>
                        <div class="admin-portal-kpi__hint">Etherscan contract tx count (cached ~5 min)</div>
                    </div>
                </div>
            </header>

            <section class="card admin-portal-panel">
                <div class="admin-portal-panel__header">
                    <h3 class="admin-portal-panel__title">
                        <i class="fa-solid fa-clipboard-list" aria-hidden="true"></i>
                        Pending Transfers (Latest)
                    </h3>
                    <a class="inline-btn admin-portal-panel__cta" href="adminTransferQueue">
                        <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                        Open Queue
                    </a>
                </div>

                <?php if (!empty($pending_transfers_latest) && is_array($pending_transfers_latest)): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                            <tr>
                                <th>Tracking</th>
                                <th>Property</th>
                                <th>Seller</th>
                                <th>Buyer</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($pending_transfers_latest as $t): ?>
                                <tr>
                                    <td><span class="contract-address"><?= htmlspecialchars((string)($t['tracking_number'] ?? '')) ?></span></td>
                                    <td>
                                        <?= htmlspecialchars((string)($t['district_name'] ?? '')) ?>,
                                        <?= htmlspecialchars((string)($t['village'] ?? '')) ?>
                                    </td>
                                    <td><?= htmlspecialchars((string)($t['seller_name'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($t['buyer_name'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($t['created_at'] ?? '')) ?></td>
                                    <td>
                                        <a class="tx-link" href="adminTransferQueue?tracking_number=<?= urlencode((string)($t['tracking_number'] ?? '')) ?>">
                                            Review
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="admin-portal-empty">No pending transfers right now.</p>
                <?php endif; ?>
            </section>

            <details class="card admin-portal-panel admin-portal-analytics-details" open>
                <summary class="admin-portal-summary">
                    <div class="admin-portal-summary__left">
                        <i class="fa-solid fa-chart-column" aria-hidden="true"></i>
                        <span>Analytics Overview</span>
                    </div>
                    <span class="admin-portal-summary__hint">Totals + chart + recent transactions</span>
                </summary>

                <div class="admin-portal-details-body">
                    <section class="admin-dashboard admin-portal-analytics">
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
                    </section>

                    <section class="dashboard-recent-tx admin-portal-recent-tx">
                        <h2 class="heading">Recent Smart Contract Transactions</h2>
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
                                            <td>
                                                <span class="status-pill<?= (($tx['isError'] ?? '0') === '0') ? '' : ' status-failed' ?>">
                                                    <?= (($tx['isError'] ?? '0') === '0') ? 'Success' : 'Failed' ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5">No transactions found (or Etherscan not configured).</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </details>

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
                            labels: <?= json_encode($days ?? []) ?>,
                            datasets: [{
                                label: 'Transactions per Day',
                                data: <?= json_encode($tx_counts ?? []) ?>,
                                backgroundColor: 'rgba(0, 86, 15, 0.7)',
                                borderColor: 'rgba(0, 86, 15, 1)',
                                borderWidth: 1,
                                borderRadius: 6,
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {display: false},
                                title: {display: true, text: 'Smart Contract Transactions (Last 30 Days)'}
                            },
                            scales: {
                                x: {
                                    ticks: {color: '#2c3e50', font: {size: 12}, autoSkip: false, maxRotation: 45, minRotation: 45},
                                    grid: {display: false}
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {color: '#2c3e50', font: {size: 12}},
                                    grid: {color: '#eee'}
                                }
                            }
                        }
                    });
                })();
            </script>

            <section class="box-container">

                <div class="box admin-portal-box">
                    <h3 class="title"><i class="fa-solid fa-id-badge" aria-hidden="true"></i> Staff Authorization</h3>
                    <p class="tutor">Grant or revoke employee permissions to protect registry operations and enforce internal controls.</p>
                    <div class="flex">
                        <a href="setEmpAuth"><i class="fa-solid fa-user-check" aria-hidden="true"></i> Set Employee Authorization</a>
                        <a href="checkEmpAuth"><i class="fa-solid fa-user-shield" aria-hidden="true"></i> Check Authorized Employees</a>
                        <a href="qrScan"><i class="fa-solid fa-qrcode" aria-hidden="true"></i> Scan QR to Capture Address</a>
                    </div>
                </div>

                <div class="box admin-portal-box">
                    <h3 class="title"><i class="fa-solid fa-file-signature" aria-hidden="true"></i> Titles & Transfers</h3>
                    <p class="tutor">Review registry activity and monitor the integrity of ownership records and transfer processes.</p>
                    <div class="flex">
                        <a href="dashboard"><i class="fa-solid fa-chart-column" aria-hidden="true"></i> Activity & Transaction Analytics</a>
                        <a href="connect"><i class="fa-brands fa-ethereum" aria-hidden="true"></i> Smart Contract Access</a>
                        <a href="adminAddress"><i class="fa-solid fa-fingerprint" aria-hidden="true"></i> Verify Admin Address</a>
                    </div>
                </div>

                <div class="box admin-portal-box">
                    <h3 class="title"><i class="fa-solid fa-map-location-dot" aria-hidden="true"></i> Land Records</h3>
                    <p class="tutor">Support parcel-level verification and maintain administrative readiness for citizen services.</p>
                    <div class="flex">
                        <a href="adminSystemRecords"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> Search System Records</a>
                        <a href="qrScan"><i class="fa-solid fa-qrcode" aria-hidden="true"></i> Field QR Scan (Address Capture)</a>
                        <a href="about"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Platform Overview</a>
                    </div>
                </div>

                <div class="box admin-portal-box">
                    <h3 class="title"><i class="fa-solid fa-lock" aria-hidden="true"></i> Governance & Security</h3>
                    <p class="tutor">Confirm administrative address settings and keep an auditable security posture for on-chain functions.</p>
                    <div class="flex">
                        <a href="adminAddress"><i class="fa-solid fa-fingerprint" aria-hidden="true"></i> Verify Admin Address</a>
                        <a href="connect"><i class="fa-brands fa-ethereum" aria-hidden="true"></i> Wallet Connection</a>
                        <a href="contact"><i class="fa-solid fa-headset" aria-hidden="true"></i> Support / Contact</a>
                    </div>
                </div>

            </section>
        </div>

        <aside class="admin-portal-sidebar">
            <div class="card admin-portal-callout">
                <h3 class="admin-portal-callout__title">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                    Quick Actions
                </h3>
                <div class="admin-portal-callout__links">
                    <a class="inline-btn" href="adminTransferQueue"><i class="fa-solid fa-list-check" aria-hidden="true"></i> Transfer Queue</a>
                    <a class="inline-btn" href="adminUserSearch"><i class="fa-solid fa-users-gear" aria-hidden="true"></i> Employee / User Search</a>
                    <a class="inline-btn" href="adminReports"><i class="fa-solid fa-file-arrow-down" aria-hidden="true"></i> Reports (CSV)</a>
                    <a class="inline-btn" href="connect"><i class="fa-brands fa-ethereum" aria-hidden="true"></i> Connect Wallet</a>
                </div>
                <p class="admin-portal-callout__note">
                    Keep core operations accessible — queue, search, reports, and wallet tools.
                </p>
            </div>

            <div class="card admin-portal-panel">
                <div class="admin-portal-panel__header">
                    <h3 class="admin-portal-panel__title">
                        <i class="fa-solid fa-user-clock" aria-hidden="true"></i>
                        Employee Status
                    </h3>
                    <a class="tx-link" href="adminUserSearch?role=employee">View all</a>
                </div>

                <div class="admin-employee-list">
                    <?php if (!empty($employee_status_list) && is_array($employee_status_list)): ?>
                        <?php foreach ($employee_status_list as $emp): ?>
                            <?php
                            $last = !empty($emp['last_login']) ? strtotime((string)$emp['last_login']) : 0;
                            $isActive = $last > 0 && (time() - $last) < 300;
                            ?>
                            <div class="admin-employee-row">
                                <div class="admin-employee-row__main">
                                    <div class="admin-employee-row__name"><?= htmlspecialchars((string)($emp['User_Name'] ?? '')) ?></div>
                                    <div class="admin-employee-row__meta"><?= htmlspecialchars((string)($emp['User_Email'] ?? '')) ?></div>
                                </div>
                                <div class="admin-employee-row__right">
                                    <span class="status-pill<?= $isActive ? '' : ' status-pending' ?>">
                                        <?= $isActive ? 'Active' : 'Inactive' ?>
                                    </span>
                                    <div class="admin-employee-row__time">
                                        <?= $last > 0 ? date('Y-m-d H:i', $last) : '—' ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="admin-portal-empty">No employees found.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card admin-portal-panel">
                <div class="admin-portal-panel__header">
                    <h3 class="admin-portal-panel__title">
                        <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
                        Alerts
                    </h3>
                </div>

                <div class="admin-portal-alerts">
                    <div class="admin-portal-alert <?= ((int)($stuck_pending_over_7d ?? 0)) > 0 ? 'is-warning' : 'is-ok' ?>">
                        <div class="admin-portal-alert__main">
                            <div class="admin-portal-alert__label">Pending transfers older than 7 days</div>
                            <div class="admin-portal-alert__value"><?= number_format((int)($stuck_pending_over_7d ?? 0)) ?></div>
                        </div>
                        <div class="admin-portal-alert__hint">
                            <?= ((int)($stuck_pending_over_7d ?? 0)) > 0 ? 'Review stuck items to prevent service delays.' : 'No delays detected.' ?>
                        </div>
                        <div class="admin-portal-alert__actions">
                            <a class="inline-btn" href="sellRequest">
                                <i class="fa-solid fa-list-check" aria-hidden="true"></i>
                                View Queue
                            </a>
                        </div>
                    </div>

                    <div class="admin-portal-alert <?= ($blockchain_tx_30d ?? null) === null ? 'is-warning' : 'is-ok' ?>">
                        <div class="admin-portal-alert__main">
                            <div class="admin-portal-alert__label">Blockchain KPI source</div>
                            <div class="admin-portal-alert__value"><?= ($blockchain_tx_30d ?? null) === null ? 'Not configured' : 'OK' ?></div>
                        </div>
                        <div class="admin-portal-alert__hint">
                            <?= ($blockchain_tx_30d ?? null) === null ? 'Set ETHERSCAN_API_KEY and ETHERSCAN_CONTRACT_ADDRESS in your env.' : 'Etherscan reachable; values cached briefly.' ?>
                        </div>
                        <div class="admin-portal-alert__actions">
                            <a class="inline-btn" href="connect">
                                <i class="fa-brands fa-ethereum" aria-hidden="true"></i>
                                Wallet / Contract
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>

</section>

