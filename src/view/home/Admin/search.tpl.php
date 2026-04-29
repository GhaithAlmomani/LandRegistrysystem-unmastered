<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

// Ensure only admins can access this page
AuthMiddleware::requireAdmin();
?>

<section class="search-section">
    <h1 class="heading">Employee Search</h1>

    <div class="card admin-page-card">
        <h3 class="title">Search & Manage Staff</h3>
        <p class="tutor">Find employees by name, email, or ID. Use sorting to review recent activity.</p>

        <form action="search" method="GET" class="admin-page-form admin-search-form">
            <label class="admin-page-label" for="q">Search</label>
            <div class="admin-search-row">
                <input id="q" type="text" name="q" class="box" placeholder="Search by name, email, or ID..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button type="submit" class="inline-btn">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    Search
                </button>
            </div>

            <label class="admin-page-label" for="sort">Sort by</label>
            <select name="sort" id="sort" class="box" onchange="this.form.submit()">
                <option value="date_desc" <?= ($_GET['sort'] ?? '') === 'date_desc' ? 'selected' : '' ?>>Newest First</option>
                <option value="date_asc" <?= ($_GET['sort'] ?? '') === 'date_asc' ? 'selected' : '' ?>>Oldest First</option>
                <option value="name_asc" <?= ($_GET['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>Name (A-Z)</option>
                <option value="name_desc" <?= ($_GET['sort'] ?? '') === 'name_desc' ? 'selected' : '' ?>>Name (Z-A)</option>
            </select>
        </form>

        <div class="table-container admin-search-results">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Last Login</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="6">No employees found.</td></tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['User_ID']) ?></td>
                            <td><?= htmlspecialchars($user['User_Name']) ?></td>
                            <td><?= htmlspecialchars($user['User_Email']) ?></td>
                            <td><?= $user['last_login'] ? date('Y-m-d H:i', strtotime($user['last_login'])) : 'Never' ?></td>
                            <td>
                                <?php
                                $lastLogin = strtotime($user['last_login']);
                                $isOnline = (time() - $lastLogin) < 300; // 5 minutes
                                ?>
                                <span class="status-pill<?= $isOnline ? '' : ' status-pending' ?>"><?= $isOnline ? 'Online' : 'Offline' ?></span>
                            </td>
                            <td class="admin-table-actions">
                                <a href="setEmpAuth?id=<?= $user['User_ID'] ?>" class="tx-link">Edit</a>
                                <a href="checkEmpAuth?id=<?= $user['User_ID'] ?>" class="tx-link">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
