<section class="admin-page ownedassets-page">
    <header class="registry-lookup-hero card">
        <div class="registry-lookup-hero__badge admin-portal-badge">
            <i class="fa-solid fa-house-circle-check" aria-hidden="true"></i>
            <span>Asset registry</span>
        </div>
        <h1 class="heading registry-lookup-hero__title">Assets owned</h1>
        <p class="registry-lookup-hero__lead">Review your registered parcels, filter quickly, and export a clean snapshot for your records.</p>
    </header>

    <div class="ownedassets-kpis">
        <div class="ownedassets-kpi">
            <div class="ownedassets-kpi__label">Total Assets</div>
            <div class="ownedassets-kpi__value"><?= htmlspecialchars((string)($totalAssets ?? 0)); ?></div>
        </div>
        <div class="ownedassets-kpi">
            <div class="ownedassets-kpi__label">Orders</div>
            <div class="ownedassets-kpi__value"><?= htmlspecialchars((string)($ordersCount ?? 0)); ?></div>
        </div>
        <div class="ownedassets-kpi">
            <div class="ownedassets-kpi__label">Lands</div>
            <div class="ownedassets-kpi__value"><?= htmlspecialchars((string)($landCount ?? 0)); ?></div>
        </div>
        <div class="ownedassets-kpi">
            <div class="ownedassets-kpi__label">Apartments</div>
            <div class="ownedassets-kpi__value"><?= htmlspecialchars((string)($apartmentCount ?? 0)); ?></div>
        </div>
    </div>

    <div class="card admin-page-card">
        <div class="ownedassets-controls">
            <div class="ownedassets-search">
                <input type="text" id="searchInput" class="box" placeholder="Search assets by district, village, block, plot..." onkeyup="applyFilters()">
            </div>
            <div class="ownedassets-filters">
                <button class="filter-btn active" data-filter="all" type="button">All</button>
                <button class="filter-btn" data-filter="land" type="button">Land</button>
                <button class="filter-btn" data-filter="apartment" type="button">Apartment</button>
            </div>
            <button class="inline-btn ownedassets-export" type="button" onclick="exportToExcel()">
                <i class="fas fa-download" aria-hidden="true"></i> Export CSV
            </button>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-message" style="margin-top: 0.75rem;"><?= htmlspecialchars((string)$error) ?></div>
        <?php endif; ?>

        <div class="table-container ownedassets-table">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Owner</th>
                    <th>District</th>
                    <th>Village</th>
                    <th>Block Name</th>
                    <th>Plot</th>
                    <th>Block No.</th>
                    <th>Type</th>
                    <th>Area</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($ownedProperties)): ?>
                    <tr><td colspan="11" class="text-center">No owned properties found</td></tr>
                <?php else: ?>
                    <?php foreach ($ownedProperties as $property): ?>
                        <?php
                            $propertyType = strtolower((string)($property['type'] ?? ''));
                            if ($propertyType === '') {
                                $propertyType = (empty($property['apartment_number']) || $property['apartment_number'] === '-') ? 'land' : 'apartment';
                            }
                        ?>
                        <tr data-type="<?= htmlspecialchars($propertyType) ?>">
                            <td><?= htmlspecialchars((string)$property['id']); ?></td>
                            <td><?= htmlspecialchars((string)$property['owner_name']); ?></td>
                            <td><?= htmlspecialchars((string)$property['district_name']); ?></td>
                            <td><?= htmlspecialchars((string)$property['village']); ?></td>
                            <td><?= htmlspecialchars((string)$property['block_name']); ?></td>
                            <td><?= htmlspecialchars((string)$property['plot_number']); ?></td>
                            <td><?= htmlspecialchars((string)$property['block_number']); ?></td>
                            <td><?= htmlspecialchars(ucfirst($propertyType)); ?></td>
                            <td><?= htmlspecialchars((string)(isset($property['area']) && $property['area'] !== null && $property['area'] !== '' ? $property['area'] . ' m²' : '—')); ?></td>
                            <td><span class="status-pill<?= (($property['status'] ?? '') === 'pending_transfer') ? ' status-pending' : '' ?>"><?= htmlspecialchars(ucfirst((string)$property['status'])); ?></span></td>
                            <td><?= htmlspecialchars(date('Y-m-d', strtotime((string)$property['created_at']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function applyFilters() {
            const activeBtn = document.querySelector('.filter-btn.active');
            const selectedType = activeBtn ? activeBtn.getAttribute('data-filter') : 'all';
            const searchFilter = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('.ownedassets-table tbody tr');
            rows.forEach((row) => {
                const tds = row.querySelectorAll('td');
                if (!tds.length) return;

                let rowVisible = true;
                const propertyType = row.getAttribute('data-type') || 'land';
                if (selectedType !== 'all' && propertyType !== selectedType) {
                    rowVisible = false;
                }

                if (rowVisible && searchFilter) {
                    const content = row.textContent.toLowerCase();
                    if (!content.includes(searchFilter)) {
                        rowVisible = false;
                    }
                }

                row.style.display = rowVisible ? '' : 'none';
            });
        }

        // Initial display update and event listeners setup
        document.addEventListener('DOMContentLoaded', function() {
             // Attach event listeners to type filter buttons
            const typeFilterButtons = document.querySelectorAll('.ownedassets-filters .filter-btn[data-filter]');
            typeFilterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    document.querySelectorAll('.ownedassets-filters .filter-btn').forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    applyFilters();
                });
            });

            applyFilters();
        });


        function exportToExcel() {
            const table = document.querySelector('.ownedassets-table table');
            const rows = table.querySelectorAll('tr');
            const csv = [];

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                // Only include visible rows (excluding the header row based on display style)
                if (i === 0 || row.style.display !== 'none') {
                    const cols = row.querySelectorAll('td, th');
                    const rowData = [];
                    for (let j = 0; j < cols.length; j++) {
                        let data = cols[j].textContent.trim();
                        data = data.replace(/"/g, '""');
                        if (data.includes(',') || data.includes('"') || data.includes('\n')) {
                            data = '"' + data + '"';
                        }
                        rowData.push(data);
                    }
                    csv.push(rowData.join(','));
                }
            }

            const csvString = csv.join('\n');
            const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.setAttribute('download', 'owned_assets.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</section>
