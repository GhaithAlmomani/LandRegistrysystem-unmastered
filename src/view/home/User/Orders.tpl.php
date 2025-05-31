<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

// Ensure only regular users can access this page
AuthMiddleware::requireUser();

require_once __DIR__ . '/../../layouts/navbar.tpl.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h2>My Orders</h2>
            
            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-3">
                            <label for="statusFilter" class="form-label">Status</label>
                            <select class="form-select form-select-lg" id="statusFilter">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="dateFilter" class="form-label">Date Range</label>
                            <input type="date" class="form-control form-control-lg" id="dateFilter">
                        </div>
                        <div class="col-md-3">
                            <label for="searchOrder" class="form-label">Search Order</label>
                            <input type="text" class="form-control form-control-lg" id="searchOrder" placeholder="Order ID or Property">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-lg">Apply Filters</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Property</th>
                                    <th>Date</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($error)) {
                                    echo '<tr><td colspan="6" class="text-center text-danger">' . htmlspecialchars($error) . '</td></tr>';
                                } else if (empty($orders)) {
                                    echo '<tr><td colspan="6" class="text-center">No orders found</td></tr>';
                                } else {
                                    foreach ($orders as $order) {
                                        $statusClass = match($order['status']) {
                                            'pending' => 'bg-warning',
                                            'processing' => 'bg-info',
                                            'completed' => 'bg-success',
                                            'cancelled' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                        
                                        $roleClass = match($order['user_role']) {
                                            'seller' => 'bg-primary',
                                            'buyer' => 'bg-success',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <tr>
                                            <td>#<?php echo htmlspecialchars($order['tracking_number']); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($order['property_name']); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($order['property_location']); ?></small>
                                            </td>
                                            <td><?php echo date('Y-m-d', strtotime($order['created_at'])); ?></td>
                                            <td>
                                                <span class="badge <?php echo $roleClass; ?>">
                                                    <?php echo ucfirst(htmlspecialchars($order['user_role'])); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $statusClass; ?>">
                                                    <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#orderDetailsModal"
                                                        data-order='<?php echo htmlspecialchars(json_encode($order)); ?>'>
                                                    View Details
                                                </button>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #217a36; color: white;">
                <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6 border-end">
                        <h6><i class="bi bi-info-circle me-2"></i>Order Information</h6>
                        <hr>
                        <p><strong>Order ID:</strong> <span id="modalOrderId"></span></p>
                        <p><strong>Date:</strong> <span id="modalOrderDate"></span></p>
                        <p><strong>Status:</strong> <span id="modalOrderStatus"></span></p>
                        <p><strong>Your Role:</strong> <span id="modalUserRole"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="bi bi-house-door me-2"></i>Property Details</h6>
                        <hr>
                        <p><strong>Property Name:</strong> <span id="modalPropertyName"></span></p>
                        <p><strong>Location:</strong> <span id="modalPropertyLocation"></span></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6><i class="bi bi-clock-history me-2"></i>Transaction History</h6>
                        <hr>
                        <div class="timeline">
                            <!-- Timeline items will be added here by JS -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for filtering
    const filterForm = document.getElementById('filterForm');
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const status = document.getElementById('statusFilter').value;
        const date = document.getElementById('dateFilter').value;
        const search = document.getElementById('searchOrder').value.toLowerCase();
        
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const rowStatus = row.cells[4].querySelector('.badge').textContent.toLowerCase();
            const rowDate = new Date(row.cells[2].textContent);
            const rowContent = row.textContent.toLowerCase();
            
            const statusMatch = !status || rowStatus.includes(status);
            const dateMatch = !date || (rowDate >= new Date(date));
            const searchMatch = !search || rowContent.includes(search);
            
            row.style.display = statusMatch && dateMatch && searchMatch ? '' : 'none';
        });
    });

    // Add event listeners for order details modal
    const orderDetailsButtons = document.querySelectorAll('[data-bs-target="#orderDetailsModal"]');
    orderDetailsButtons.forEach(button => {
        button.addEventListener('click', function() {
            const orderData = JSON.parse(this.dataset.order);
            
            // Populate modal with order details
            document.getElementById('modalOrderId').textContent = orderData.tracking_number;
            document.getElementById('modalOrderDate').textContent = new Date(orderData.created_at).toLocaleDateString();
            document.getElementById('modalOrderStatus').textContent = orderData.status;
            document.getElementById('modalUserRole').textContent = orderData.user_role;
            document.getElementById('modalPropertyName').textContent = orderData.property_name;
            document.getElementById('modalPropertyLocation').textContent = orderData.property_location;
            
            // Add transaction history
            const timeline = document.querySelector('.timeline');
            timeline.innerHTML = `
                <div class="timeline-item">
                    <div class="timeline-point"></div>
                    <div class="timeline-content">
                        <h6>Order Created</h6>
                        <p><strong>Date:</strong> ${new Date(orderData.created_at).toLocaleString()}</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-point"></div>
                    <div class="timeline-content">
                        <h6>Current Status: ${orderData.status}</h6>
                        <p><strong>Last updated:</strong> ${new Date(orderData.updated_at || orderData.created_at).toLocaleString()}</p>
                    </div>
                </div>
            `;

             // Add more timeline items based on status if needed
             // Example: if (orderData.status === 'completed') { ... }
        });
    });
});
</script>

<style>
/* Table body styling */
.table tbody tr:nth-child(even) {
    background-color: #f2f2f2; /* Light grey for even rows */
}

.table tbody td {
    padding: 12px 8px; /* Adjust padding for better spacing */
}

/* Basic timeline styling */
.timeline {
    position: relative;
    padding: 0;
    list-style: none;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 30px;
    width: 2px;
    background-color: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
    padding-left: 45px;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-point {
    position: absolute;
    top: 0;
    left: 20px;
    width: 20px;
    height: 20px;
    background-color: #007bff;
    border-radius: 50%;
    z-index: 1;
}

.timeline-content {
    padding: 10px 15px;
    background-color: #f8f9fa;
    border-radius: 4px;
}

.timeline-content h6 {
    margin-top: 0;
    color: #007bff;
}

.timeline-content p {
    margin-bottom: 0;
    font-size: 0.9em;
    color: #6c757d;
}

/* Add some icons - requires Bootstrap Icons CSS */
@import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css");

.form-control {
    padding: 12px;
}

.form-label {
    font-size: 1.1em; /* Increase font size slightly */
}
</style>

<?php
require_once __DIR__ . '/../../layouts/footer.tpl.php';
?> 