<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

// Ensure only employees can access this page
AuthMiddleware::requireEmployee();

/**
 * Format a short age label like "2h", "3d".
 */
function employeeAgeLabel(?string $dateTime): string
{
    if (!$dateTime) return '—';
    $ts = strtotime($dateTime);
    if ($ts === false) return '—';
    $delta = max(0, time() - $ts);

    if ($delta < 3600) return max(1, (int)floor($delta / 60)) . 'm';
    if ($delta < 86400) return (int)floor($delta / 3600) . 'h';
    return (int)floor($delta / 86400) . 'd';
}

/**
 * Sort pending transfers by oldest first (created_at asc).
 */
if (!empty($pending_transfers_latest) && is_array($pending_transfers_latest)) {
    usort($pending_transfers_latest, function ($a, $b) {
        $at = strtotime((string)($a['created_at'] ?? '')) ?: PHP_INT_MAX;
        $bt = strtotime((string)($b['created_at'] ?? '')) ?: PHP_INT_MAX;
        return $at <=> $bt;
    });
}
?>

<section class="admin-portal home-grid employee-portal">
    <div class="admin-portal-layout">
        <div class="admin-portal-main">
            <header class="admin-portal-header card">
                <div class="admin-portal-header__badge admin-portal-badge">
                    <i class="fa-solid fa-stamp" aria-hidden="true"></i>
                    <span>Notary Console</span>
                </div>

                <div class="admin-portal-header__top">
                    <div>
                        <h1 class="heading admin-portal-title">Department of Land & Survey — Notary</h1>
                        <p class="admin-portal-subtitle">
                            Review transfer requests, verify parties, and complete registration actions with an auditable workflow.
                        </p>
                    </div>
                    <div class="admin-portal-header__meta">
                        <span class="admin-portal-meta-pill">
                            <i class="fa-solid fa-clipboard-check" aria-hidden="true"></i>
                            Transfers workflow
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
                        <div class="admin-portal-kpi__hint">Requests awaiting your decision</div>
                    </div>
                    <div class="admin-portal-kpi">
                        <div class="admin-portal-kpi__top">
                            <div class="admin-portal-kpi__icon"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i></div>
                            <div class="admin-portal-kpi__label">Stuck &gt; 7 Days</div>
                        </div>
                        <div class="admin-portal-kpi__value"><?= number_format((int)($stuck_pending_over_7d ?? 0)) ?></div>
                        <div class="admin-portal-kpi__hint">Prevent service delays</div>
                    </div>
                    <div class="admin-portal-kpi">
                        <div class="admin-portal-kpi__top">
                            <div class="admin-portal-kpi__icon"><i class="fa-solid fa-arrows-rotate" aria-hidden="true"></i></div>
                            <div class="admin-portal-kpi__label">Properties In Transfer</div>
                        </div>
                        <div class="admin-portal-kpi__value"><?= number_format((int)($properties_pending_transfer ?? 0)) ?></div>
                        <div class="admin-portal-kpi__hint">Marked as pending transfer</div>
                    </div>
                    <div class="admin-portal-kpi">
                        <div class="admin-portal-kpi__top">
                            <div class="admin-portal-kpi__icon"><i class="fa-solid fa-building-columns" aria-hidden="true"></i></div>
                            <div class="admin-portal-kpi__label">Total Registered Assets</div>
                        </div>
                        <div class="admin-portal-kpi__value"><?= number_format((int)($total_assets ?? 0)) ?></div>
                        <div class="admin-portal-kpi__hint">All properties in registry</div>
                    </div>
                </div>
            </header>

            <section class="card admin-portal-panel employee-quick-lookup">
                <div class="admin-portal-panel__header">
                    <h3 class="admin-portal-panel__title">
                        <i class="fa-solid fa-bolt" aria-hidden="true"></i>
                        Quick Lookup
                    </h3>
                </div>

                <div class="employee-quick-lookup__grid">
                    <form class="employee-quick-lookup__form" action="sellRequest" method="GET">
                        <label class="admin-page-label" for="ql_tracking">Tracking number</label>
                        <div class="admin-search-row">
                            <input id="ql_tracking" name="tracking_number" class="box" required
                                   placeholder="e.g. TRK-2026-000123" value="">
                            <button class="inline-btn" type="submit">
                                <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                                Open
                            </button>
                        </div>
                    </form>

                    <form class="employee-quick-lookup__form" action="PropertyInfo" method="GET"
                          onsubmit="event.preventDefault(); window.location.href='PropertyInfo?propertyId=' + encodeURIComponent(document.getElementById('ql_property').value.trim());">
                        <label class="admin-page-label" for="ql_property">Property ID</label>
                        <div class="admin-search-row">
                            <input id="ql_property" class="box" required inputmode="numeric" placeholder="e.g. 25">
                            <button class="inline-btn" type="submit">
                                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                                Lookup
                            </button>
                        </div>
                        <p class="admin-page-help" style="margin:6px 0 0;">Opens Property Info and pre-fills the ID.</p>
                    </form>
                </div>
            </section>

            <section class="card admin-portal-panel">
                <div class="admin-portal-panel__header">
                    <h3 class="admin-portal-panel__title">
                        <i class="fa-solid fa-clipboard-list" aria-hidden="true"></i>
                        Pending Transfers (Latest)
                    </h3>
                    <a class="inline-btn admin-portal-panel__cta" href="sellRequest">
                        <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                        Open Review Queue
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
                                <th>Age</th>
                                <th>Created</th>
                                <th>Open</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($pending_transfers_latest as $t): ?>
                                <tr>
                                    <td><span class="contract-address"><?= htmlspecialchars((string)($t['tracking_number'] ?? '')) ?></span></td>
                                    <td><?= htmlspecialchars((string)($t['district_name'] ?? '')) ?>, <?= htmlspecialchars((string)($t['village'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($t['seller_name'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($t['buyer_name'] ?? '')) ?></td>
                                    <?php
                                        $ageLabel = employeeAgeLabel($t['created_at'] ?? null);
                                        $ageDays = 0;
                                        $ts = strtotime((string)($t['created_at'] ?? ''));
                                        if ($ts !== false) $ageDays = (int)floor((time() - $ts) / 86400);
                                        $ageClass = $ageDays >= 7 ? 'is-danger' : ($ageDays >= 3 ? 'is-warn' : 'is-ok');
                                    ?>
                                    <td><span class="age-pill <?= $ageClass ?>"><?= htmlspecialchars($ageLabel) ?></span></td>
                                    <td><?= htmlspecialchars((string)($t['created_at'] ?? '')) ?></td>
                                    <td><a class="tx-link" href="sellRequest?tracking_number=<?= urlencode((string)($t['tracking_number'] ?? '')) ?>">Details</a></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="admin-portal-empty">No pending transfers right now.</p>
                <?php endif; ?>
            </section>

            <section class="card admin-portal-panel employee-tools">
                <div class="admin-portal-panel__header">
                    <h3 class="admin-portal-panel__title">
                        <i class="fa-solid fa-toolbox" aria-hidden="true"></i>
                        Notary Tools
                    </h3>
                </div>

                <div class="box-container employee-tools__grid">
                    <div class="box admin-portal-box">
                        <h3 class="title"><i class="fa-solid fa-file-circle-plus" aria-hidden="true"></i> Register Property</h3>
                        <p class="tutor">Register new properties with verified owner details.</p>
                        <div class="flex">
                            <a href="propertyRegistration"><i class="fa-solid fa-plus" aria-hidden="true"></i> Open Registration</a>
                            <a href="qrScan"><i class="fa-solid fa-qrcode" aria-hidden="true"></i> QR Scan (Address)</a>
                        </div>
                    </div>

                    <div class="box admin-portal-box">
                        <h3 class="title"><i class="fa-solid fa-right-left" aria-hidden="true"></i> Transfer Ownership</h3>
                        <p class="tutor">Execute ownership transfers once documents are validated.</p>
                        <div class="flex">
                            <a href="propertyTransfer"><i class="fa-solid fa-rotate" aria-hidden="true"></i> Transfer Tool</a>
                            <a href="sellRequest"><i class="fa-solid fa-clipboard-check" aria-hidden="true"></i> Review Requests</a>
                        </div>
                    </div>

                    <div class="box admin-portal-box">
                        <h3 class="title"><i class="fa-solid fa-map-location-dot" aria-hidden="true"></i> Registry Lookup</h3>
                        <p class="tutor">Check registry records quickly during notarization.</p>
                        <div class="flex">
                            <a href="allProperties"><i class="fa-solid fa-list" aria-hidden="true"></i> All Properties</a>
                            <a href="PropertyById"><i class="fa-solid fa-hashtag" aria-hidden="true"></i> Property by ID</a>
                            <a href="PropertyInfo"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Property Info</a>
                        </div>
                    </div>

                    <div class="box admin-portal-box">
                        <h3 class="title"><i class="fa-solid fa-chart-simple" aria-hidden="true"></i> Counts & Summary</h3>
                        <p class="tutor">Quick volume checks for day-to-day operations.</p>
                        <div class="flex">
                            <a href="PropertyCount"><i class="fa-solid fa-calculator" aria-hidden="true"></i> Property Count</a>
                            <a href="qrScan"><i class="fa-solid fa-camera" aria-hidden="true"></i> QR Scanner</a>
                        </div>
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
                    <a class="inline-btn" href="sellRequest"><i class="fa-solid fa-list-check" aria-hidden="true"></i> Review Transfers</a>
                    <a class="inline-btn" href="propertyRegistration"><i class="fa-solid fa-file-circle-plus" aria-hidden="true"></i> Register Property</a>
                    <a class="inline-btn" href="propertyTransfer"><i class="fa-solid fa-right-left" aria-hidden="true"></i> Transfer Ownership</a>
                    <a class="inline-btn" href="qrScan"><i class="fa-solid fa-qrcode" aria-hidden="true"></i> QR Scan</a>
                </div>
                <p class="admin-portal-callout__note">Use the queue first, then proceed with registration/transfer tools.</p>
            </div>

            <div class="card admin-portal-panel employee-chain-health">
                <div class="admin-portal-panel__header">
                    <h3 class="admin-portal-panel__title">
                        <i class="fa-brands fa-ethereum" aria-hidden="true"></i>
                        Chain Health
                    </h3>
                </div>
                <div id="employee-chain-health" class="admin-page-status is-warning">
                    Checking MetaMask network &amp; contract…
                </div>
                <p class="tutor" style="margin:10px 0 0;">
                    If you see “wrong network” or “no contract code”, switch networks or update the configured contract address.
                </p>
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
                            <?= ((int)($stuck_pending_over_7d ?? 0)) > 0 ? 'Prioritize stuck items to prevent delays.' : 'No delays detected.' ?>
                        </div>
                        <div class="admin-portal-alert__actions">
                            <a class="inline-btn" href="sellRequest">
                                <i class="fa-solid fa-list-check" aria-hidden="true"></i>
                                Open Queue
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</section>

<script>
(function () {
    const el = document.getElementById('employee-chain-health');
    if (!el) return;

    async function run() {
        if (!window.ethereum || !window.Web3) {
            el.textContent = 'MetaMask not detected.';
            el.className = 'admin-page-status is-error';
            return;
        }

        try {
            const web3 = new Web3(window.ethereum);
            const chainId = await window.ethereum.request({ method: 'eth_chainId' });
            const expected = '0xaa36a7'; // Sepolia (11155111)
            if (chainId !== expected) {
                el.innerHTML = 'Wrong network: <span class="contract-address">' + chainId + '</span>. Expected Sepolia.';
                el.className = 'admin-page-status is-warning';
                return;
            }

            const code = await web3.eth.getCode(contractAddress);
            if (!code || code === '0x') {
                el.innerHTML = 'No contract code at <span class="contract-address">' + contractAddress + '</span>.';
                el.className = 'admin-page-status is-error';
                return;
            }

            el.innerHTML = 'Ready: Sepolia + contract loaded (<span class="contract-address">' + contractAddress + '</span>).';
            el.className = 'admin-page-status is-success';
        } catch (e) {
            el.textContent = 'Chain check failed: ' + ((e && e.message) ? e.message : String(e));
            el.className = 'admin-page-status is-error';
        }
    }

    window.addEventListener('load', run);
})();
</script>
