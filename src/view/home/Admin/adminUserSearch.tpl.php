<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';

use MVC\middleware\AuthMiddleware;

AuthMiddleware::requireAdmin();
?>

<section class="admin-page">
    <h1 class="heading">Employee / User Search</h1>

    <div class="card admin-page-card">
        <h3 class="title">Search & Manage Accounts</h3>
        <p class="tutor">Search by name, email, ID, or national ID. Filter by role.</p>

        <form method="GET" action="adminUserSearch" class="admin-page-form">
            <label class="admin-page-label" for="q">Search</label>
            <div class="admin-search-row">
                <input id="q" type="text" name="q" class="box" value="<?= htmlspecialchars((string)($q ?? '')) ?>" placeholder="Name, email, ID, national ID">
                <button type="submit" class="inline-btn">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    Search
                </button>
            </div>

            <div class="admin-reports-grid" style="margin-top: 0.9rem;">
                <div>
                    <label class="admin-page-label" for="role">Role</label>
                    <select class="box" id="role" name="role" onchange="this.form.submit()">
                        <option value="all" <?= ($role ?? 'all') === 'all' ? 'selected' : '' ?>>All</option>
                        <option value="user" <?= ($role ?? '') === 'user' ? 'selected' : '' ?>>Users</option>
                        <option value="employee" <?= ($role ?? '') === 'employee' ? 'selected' : '' ?>>Employees</option>
                        <option value="admin" <?= ($role ?? '') === 'admin' ? 'selected' : '' ?>>Admins</option>
                    </select>
                </div>
                <div>
                    <label class="admin-page-label" for="sort">Sort</label>
                    <select class="box" id="sort" name="sort" onchange="this.form.submit()">
                        <option value="date_desc" <?= ($sort ?? 'date_desc') === 'date_desc' ? 'selected' : '' ?>>Newest Login</option>
                        <option value="date_asc" <?= ($sort ?? '') === 'date_asc' ? 'selected' : '' ?>>Oldest Login</option>
                        <option value="name_asc" <?= ($sort ?? '') === 'name_asc' ? 'selected' : '' ?>>Name (A-Z)</option>
                        <option value="name_desc" <?= ($sort ?? '') === 'name_desc' ? 'selected' : '' ?>>Name (Z-A)</option>
                    </select>
                </div>
                <div class="admin-page-actions" style="margin-top: 1.7rem;">
                    <a class="inline-btn" href="adminUserSearch">
                        <i class="fa-solid fa-rotate-left" aria-hidden="true"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="card admin-page-card">
        <h3 class="title">Results</h3>
        <p class="tutor"><?= number_format(count($users ?? [])) ?> record(s)</p>

        <div class="table-container">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>National ID</th>
                    <th>Role</th>
                    <th>Last Login</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="6">No results.</td></tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <?php
                        $roleName = AuthMiddleware::getRoleName((int)($u['AdminID'] ?? 0));
                        ?>
                        <tr>
                            <td><?= htmlspecialchars((string)($u['User_ID'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string)($u['User_Name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string)($u['User_Email'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string)($u['User_NationalID'] ?? '')) ?></td>
                            <td><span class="status-pill"><?= htmlspecialchars($roleName) ?></span></td>
                            <td><?= !empty($u['last_login']) ? date('Y-m-d H:i', strtotime((string)$u['last_login'])) : 'Never' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

