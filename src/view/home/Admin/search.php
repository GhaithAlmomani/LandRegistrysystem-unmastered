<?php 
/** @var array $users */
?>

<div class="search-page">
    <div class="container">
        <div class="search-header text-center mb-4">
            <h1 class="mb-2"><i class="fas fa-users-cog text-primary me-2"></i>User Management</h1>
            <p class="lead text-muted">Search and manage system users with advanced filters</p>
        </div>
        
        <!-- Search Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <form action="search" method="GET" class="search-form">
                    <div class="search-bar input-group">
                        <input type="text" 
                               name="q" 
                               class="form-control form-control-lg" 
                               placeholder="Search users by name, email, or ID..." 
                               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                               aria-label="Search users"
                               aria-describedby="button-search">
                        <button class="btn btn-primary px-4" type="submit" id="button-search">
                            <i class="fas fa-search me-2"></i> Search
                        </button>
                    </div>
                    
                    <div class="filters-container mt-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select name="sort" class="form-select" id="sortBy">
                                        <option value="name_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : '' ?>>Name (A-Z)</option>
                                        <option value="name_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_desc') ? 'selected' : '' ?>>Name (Z-A)</option>
                                        <option value="date_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'date_asc') ? 'selected' : '' ?>>Date (Oldest First)</option>
                                        <option value="date_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'date_desc' || !isset($_GET['sort'])) ? 'selected' : '' ?>>Date (Newest First)</option>
                                    </select>
                                    <label for="sortBy"><i class="fas fa-sort me-2"></i>Sort Employees</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div class="text-muted">
                                <?php if (isset($users)): ?>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-users me-1"></i>
                                        <?= count($users) ?> user<?= count($users) != 1 ? 's' : '' ?> found
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <a href="search" class="btn btn-outline-secondary">
                                    <i class="fas fa-sync-alt me-1"></i> Reset Filters
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Results Section -->
        <?php if (isset($users) && !empty($users)): ?>
            <div class="search-results mt-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">USER</th>
                                        <th>EMAIL</th>
                                        <th>ROLE</th>
                                        <th>LAST LOGIN</th>
                                        <th class="text-end pe-4">ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): 
                                        $typeClass = '';
                                        $typeIcon = 'user';
                                        $typeText = 'Individual';
                                        $badgeClass = 'bg-secondary';
                                        
                                        if ($user['AdminID'] == \MVC\middleware\AuthMiddleware::ROLE_ADMIN) {
                                            $typeClass = 'text-danger';
                                            $typeIcon = 'user-shield';
                                            $typeText = 'Admin';
                                            $badgeClass = 'bg-danger';
                                        } elseif ($user['AdminID'] == \MVC\middleware\AuthMiddleware::ROLE_EMPLOYEE) {
                                            $typeClass = 'text-primary';
                                            $typeIcon = 'user-tie';
                                            $typeText = 'Employee';
                                            $badgeClass = 'bg-primary';
                                        }
                                        
                                        $lastLogin = strtotime($user['last_login']);
                                        $isOnline = (time() - $lastLogin) < 300; // 5 minutes
                                    ?>
                                        <tr class="user-row">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="position-relative me-3">
                                                        <img src="<?= htmlspecialchars($user['User_Avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($user['User_Name']) . '&background=0056b3&color=fff' ) ?>" 
                                                             alt="<?= htmlspecialchars($user['User_Name']) ?>" 
                                                             class="rounded-circle"
                                                             onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($user['User_Name']) ?>&background=0056b3&color=fff'">
                                                        <span class="position-absolute bottom-0 end-0 p-1 bg-<?= $isOnline ? 'success' : 'secondary' ?> border border-2 border-white rounded-circle"></span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold"><?= htmlspecialchars($user['User_Name']) ?></div>
                                                        <small class="text-muted">ID: <?= htmlspecialchars($user['User_ID']) ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="mailto:<?= htmlspecialchars($user['User_Email']) ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($user['User_Email']) ?>
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge <?= $badgeClass ?> bg-opacity-10 <?= $typeClass ?> px-3 py-2 rounded-pill">
                                                    <i class="fas fa-<?= $typeIcon ?> me-1"></i> <?= $typeText ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-clock text-muted me-2"></i>
                                                    <div class="d-flex flex-column">
                                                        <span class="small"><?= date('M d, Y', $lastLogin) ?></span>
                                                        <small class="text-muted"><?= date('h:i A', $lastLogin) ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-primary" title="View Profile" data-bs-toggle="tooltip">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary" title="Edit User" data-bs-toggle="tooltip">
                                                        <i class="fas fa-edit"></i>
                                                    </button>

                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif (isset($_GET['q'])): ?>
            <div class="text-center py-5 my-5">
                <div class="mb-4">
                    <i class="fas fa-search fa-4x text-light bg-primary bg-opacity-10 p-4 rounded-circle"></i>
                </div>
                <h3 class="fw-semibold mb-3">No users found</h3>
                <p class="text-muted mb-4">
                    We couldn't find any users matching <span class="fw-medium">"<?= htmlspecialchars($_GET['q']) ?>"</span>.
                    <br>
                    Try adjusting your search or filter criteria.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="search" class="btn btn-outline-primary">
                        <i class="fas fa-undo me-2"></i> Clear Search
                    </a>
                    <button type="button" class="btn btn-primary" onclick="document.querySelector('input[name=\'q\']').focus()">
                        <i class="fas fa-search me-2"></i> Try New Search
                    </button>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5 my-5">
                <div class="position-relative d-inline-block mb-4">
                    <div class="position-absolute top-0 start-50 translate-middle">
                        <div class="p-3 bg-primary bg-opacity-10 rounded-circle">
                            <i class="fas fa-search fa-3x text-primary"></i>
                        </div>
                    </div>
                    <div class="position-relative">
                        <img src="https://cdn.pixabay.com/photo/2018/08/14/13/23/ocean-3605547_1280.jpg" 
                             class="img-fluid rounded-4 shadow-sm" 
                             alt="Search illustration">
                    </div>
                </div>
                <h2 class="fw-semibold mb-3">Employee Management</h2>
                <p class="lead text-muted mb-4 px-3 px-md-5 mx-auto">
                    Search and manage employee accounts. Use the search bar to find employees by name, email, or ID.
                    Only employees with appropriate access levels are shown in this view.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
                    <button class="btn btn-primary px-4" onclick="document.querySelector('input[name=\'q\']').focus()">
                        <i class="fas fa-search me-2"></i> Start Searching
                    </button>
                    <button class="btn btn-outline-primary px-4" data-bs-toggle="modal" data-bs-target="#searchTips">
                        <i class="fas fa-lightbulb me-2"></i> Search Tips
                    </button>
                </div>
                <div class="d-flex justify-content-center">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                        <i class="fas fa-user-tie me-2"></i>Employee Management View
                    </span>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Search Tips Modal -->
<div class="modal fade" id="searchTips" tabindex="-1" aria-labelledby="searchTipsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="searchTipsLabel">
                    <i class="fas fa-lightbulb text-warning me-2"></i>Employee Search Tips
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <h6 class="fw-semibold mb-2">Employee Search</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-search text-primary me-2"></i>
                            Search by employee name, email, or ID
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-filter text-primary me-2"></i>
                            Filter by account type (Employees/Admins)
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-sort text-primary me-2"></i>
                            Sort by name or last login date
                        </li>
                    </ul>
                </div>
                
                <div class="mb-4">
                    <h6 class="fw-semibold mb-2">Search Operators</h6>
                    <div class="bg-light p-3 rounded-3">
                        <code class="d-block mb-2">"exact phrase"</code>
                        <small class="text-muted d-block mb-3">Find exact matches (e.g., "john smith")</small>
                        
                        <code class="d-block mb-2">john -doe</code>
                        <small class="text-muted d-block">Exclude terms (e.g., results with john but not doe)</small>
                    </div>
                </div>
                
                <div class="alert alert-info mb-0">
                    <i class="fas fa-shield-alt me-2"></i>
                    <small class="d-inline-block">
                        <strong>Employee Management:</strong> This view shows all employee accounts.
                        Use the search function to quickly find specific employees.
                    </small>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Got it!</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Handle form submission with loading state
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Searching...';
                submitBtn.disabled = true;
                
                // Add loading class to search container
                const searchContainer = document.querySelector('.search-page');
                if (searchContainer) {
                    searchContainer.classList.add('search-loading');
                }
            }
        });
    }

    // Initialize clipboard for copy buttons
    const copyButtons = document.querySelectorAll('[data-copy]');
    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const textToCopy = this.getAttribute('data-copy');
            navigator.clipboard.writeText(textToCopy).then(() => {
                // Show tooltip with feedback
                const tooltip = new bootstrap.Tooltip(this, {
                    title: 'Copied!',
                    trigger: 'manual'
                });
                tooltip.show();
                
                // Hide tooltip after 2 seconds
                setTimeout(() => {
                    tooltip.hide();
                    // Destroy the tooltip to prevent memory leaks
                    setTimeout(() => tooltip.dispose(), 100);
                }, 2000);
            });
        });
    });

    // Handle row clicks
    const userRows = document.querySelectorAll('.user-row');
    userRows.forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', (e) => {
            // Don't navigate if a button was clicked
            if (!e.target.closest('button, a, [role="button"]')) {
                const userId = row.getAttribute('data-user-id');
                if (userId) {
                    window.location.href = `user/profile/${userId}`;
                }
            }
        });
    });
});

// Debounce function to limit how often a function can be called
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Handle search input with debounce
const searchInput = document.querySelector('input[name="q"]');
if (searchInput) {
    searchInput.addEventListener('input', debounce(function() {
        // Auto-submit form after 500ms of no typing
        if (this.value.length > 2 || this.value.length === 0) {
            this.form.submit();
        }
    }, 500));
}
</script>
