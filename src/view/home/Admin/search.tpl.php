<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

// Ensure only admins can access this page
AuthMiddleware::requireAdmin();
?>

<section class="search-section">
    <h1 class="heading">Search Employees</h1>

    <div class="search-container">
        <form action="search" method="GET" class="search-form">
            <div class="search-box">
                <input type="text" name="q" placeholder="Search by name, email, or ID..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="search-input">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
            </div>
            
            <div class="sort-options">
                <label for="sort">Sort by:</label>
                <select name="sort" id="sort" onchange="this.form.submit()">
                    <option value="date_desc" <?= ($_GET['sort'] ?? '') === 'date_desc' ? 'selected' : '' ?>>Newest First</option>
                    <option value="date_asc" <?= ($_GET['sort'] ?? '') === 'date_asc' ? 'selected' : '' ?>>Oldest First</option>
                    <option value="name_asc" <?= ($_GET['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>Name (A-Z)</option>
                    <option value="name_desc" <?= ($_GET['sort'] ?? '') === 'name_desc' ? 'selected' : '' ?>>Name (Z-A)</option>
                </select>
            </div>
        </form>

        <div class="results-container">
            <?php if (empty($users)): ?>
                <p class="no-results">No employees found.</p>
            <?php else: ?>
                <table class="results-table">
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
                                    <span class="status-badge <?= $isOnline ? 'online' : 'offline' ?>">
                                        <?= $isOnline ? 'Online' : 'Offline' ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="setEmpAuth?id=<?= $user['User_ID'] ?>" class="action-btn edit">Edit</a>
                                    <a href="checkEmpAuth?id=<?= $user['User_ID'] ?>" class="action-btn view">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.search-section {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.search-container {
    background: var(--white);
    border-radius: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    padding: 2rem;
    border: var(--border);
}

.search-form {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.search-box {
    flex: 1;
    min-width: 300px;
    position: relative;
}

.search-input {
    width: 100%;
    padding: 1rem 3rem 1rem 1rem;
    border: var(--border);
    border-radius: 0.5rem;
    font-size: 1.6rem;
}

.search-btn {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--main-color);
    font-size: 1.6rem;
    cursor: pointer;
}

.sort-options {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.sort-options select {
    padding: 0.8rem;
    border: var(--border);
    border-radius: 0.5rem;
    font-size: 1.4rem;
}

.results-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 2rem;
}

.results-table th,
.results-table td {
    padding: 1.2rem;
    text-align: left;
    border-bottom: 1px solid var(--border);
}

.results-table th {
    background: var(--light-bg);
    font-weight: 600;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-size: 1.2rem;
    font-weight: 500;
}

.status-badge.online {
    background: #e3f9e5;
    color: #1b4332;
}

.status-badge.offline {
    background: #f8f9fa;
    color: #6c757d;
}

.action-btn {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    text-decoration: none;
    font-size: 1.3rem;
    margin-right: 0.5rem;
}

.action-btn.edit {
    background: var(--main-color);
    color: white;
}

.action-btn.view {
    background: var(--light-bg);
    color: var(--black);
}

.no-results {
    text-align: center;
    padding: 2rem;
    color: var(--light-color);
    font-size: 1.6rem;
}
</style> 