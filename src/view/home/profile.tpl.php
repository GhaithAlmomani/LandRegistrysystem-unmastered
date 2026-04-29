<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['Username'])) {
    header('Location: login');
    exit();
}
?>
<section class="admin-page profile-page">
    <header class="card profile-hero">
        <div class="profile-hero__top">
            <div class="profile-hero__identity">
                <div class="profile-hero__meta">
                    <h1 class="heading profile-hero__title"><?= htmlspecialchars((string)($userData['User_FullName'] ?? $userData['User_Name'] ?? 'User')) ?></h1>
                    <div class="profile-hero__sub">
                        <span class="status-pill profile-role-pill">
                            <i class="fa-solid fa-id-badge" aria-hidden="true"></i>
                            <?= htmlspecialchars(\MVC\middleware\AuthMiddleware::getRoleName($userData['AdminID'] ?? null)) ?>
                        </span>
                        <?php if (!empty($userData['User_Email'])): ?>
                            <span class="profile-hero__hint"><?= htmlspecialchars((string)$userData['User_Email']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="profile-hero__actions">
                <a href="update-profile" class="inline-btn">
                    <i class="fa-solid fa-user-pen" aria-hidden="true"></i>
                    Update profile
                </a>
            </div>
        </div>

        <div class="profile-kpis">
            <div class="profile-kpi">
                <div class="profile-kpi__icon"><i class="fa-solid fa-house" aria-hidden="true"></i></div>
                <div>
                    <div class="profile-kpi__value"><?= number_format((int)($assetsOwned ?? 0)) ?></div>
                    <div class="profile-kpi__label">Assets owned</div>
                </div>
                <a class="tx-link profile-kpi__link" href="ownedassets">View</a>
            </div>

            <div class="profile-kpi">
                <div class="profile-kpi__icon"><i class="fa-solid fa-list-ul" aria-hidden="true"></i></div>
                <div>
                    <div class="profile-kpi__value"><?= number_format((int)($ordersCount ?? 0)) ?></div>
                    <div class="profile-kpi__label">Orders</div>
                </div>
                <a class="tx-link profile-kpi__link" href="orders">View</a>
            </div>

            <div class="profile-kpi">
                <div class="profile-kpi__icon"><i class="fa-solid fa-receipt" aria-hidden="true"></i></div>
                <div>
                    <div class="profile-kpi__value">—</div>
                    <div class="profile-kpi__label">Recent transactions</div>
                </div>
                <a class="tx-link profile-kpi__link" href="recentTransaction">Open</a>
            </div>
        </div>
    </header>

    <div class="card admin-page-card">
        <h2 class="title" style="margin-top:0;">Account details</h2>
        <div class="profile-details">
            <div class="profile-details__row">
                <div class="profile-details__label">Username</div>
                <div class="profile-details__value"><?= htmlspecialchars((string)($userData['User_Name'] ?? '—')) ?></div>
            </div>
            <div class="profile-details__row">
                <div class="profile-details__label">National ID</div>
                <div class="profile-details__value"><?= htmlspecialchars((string)($userData['User_NationalID'] ?? '—')) ?></div>
            </div>
            <div class="profile-details__row">
                <div class="profile-details__label">Phone</div>
                <div class="profile-details__value"><?= htmlspecialchars((string)($userData['User_Phone'] ?? $userData['Phone'] ?? '—')) ?></div>
            </div>
            <div class="profile-details__row">
                <div class="profile-details__label">Address</div>
                <div class="profile-details__value"><?= htmlspecialchars((string)($userData['User_Address'] ?? $userData['Address'] ?? '—')) ?></div>
            </div>
        </div>
    </div>
</section>