<section>

    <style>
        .user-info {
            background-color: var(--white);
            border-radius: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin: 2rem 0;
            width: 100%;
        }

        .user-info h1 {
            font-size: 2.5rem;
            color: var(--black);
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--main-color);
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
            padding-bottom: 1rem;
        }

        .table-container::-webkit-scrollbar {
            height: 8px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: var(--main-color);
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #004408;
        }

        .user-info table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 1rem;
            min-width: 1200px;
        }

        .user-info thead {
            background-color: var(--main-color);
            color: white;
            position: sticky;
            top: 0;
        }

        .user-info th {
            padding: 1.5rem;
            font-size: 1.4rem;
            font-weight: 600;
            text-align: left;
            white-space: nowrap;
        }

        .user-info th:first-child {
            border-top-left-radius: 0.8rem;
        }

        .user-info th:last-child {
            border-top-right-radius: 0.8rem;
        }

        .user-info tbody tr {
            transition: all 0.3s ease;
        }

        .user-info tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .user-info tbody tr:hover {
            background-color: rgba(0, 86, 15, 0.05);
        }

        .user-info td {
            padding: 1.2rem 1.5rem;
            font-size: 1.4rem;
            color: var(--black);
            border-bottom: 1px solid #eee;
            white-space: nowrap;
        }

        .user-info td:first-child {
            font-weight: 600;
            color: var(--main-color);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .search-box {
            display: flex;
            gap: 1rem;
            align-items: center;
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0.8rem;
            width: 100%;
            max-width: 400px;
        }

        .search-box input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            font-size: 1.4rem;
        }

        .search-box input:focus {
            border-color: var(--main-color);
            outline: none;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 0.8rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-card h3 {
            font-size: 1.6rem;
            color: var(--main-color);
            margin-bottom: 0.5rem;
        }

        .stat-card p {
            font-size: 2.4rem;
            font-weight: bold;
            color: var(--black);
        }

        .filter-container {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.8rem 1.5rem;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 1.4rem;
            transition: all 0.3s ease;
        }

        .filter-btn:hover, .filter-btn.active {
            background: var(--main-color);
            color: white;
            border-color: var(--main-color);
        }

        .export-btn {
            padding: 1rem 2rem;
            background: var(--main-color);
            color: white;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .export-btn:hover {
            background: #004408;
            transform: translateY(-2px);
        }
    </style>

    <div class="user-info">
        <div class="page-header">
            <h1>Assets Owned</h1>
            <button class="export-btn" onclick="exportToExcel()">
                <i class="fas fa-download"></i> Export to Excel
            </button>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <h3>Total Assets</h3>
                <p><?php echo htmlspecialchars($totalAssets ?? 0); ?></p>
            </div>
            <div class="stat-card">
                <h3>Orders Number</h3>
                <p><?php echo htmlspecialchars($ordersCount ?? 0); ?></p>
            </div>
            <div class="stat-card">
                <h3>Lands</h3>
                <p><?php echo htmlspecialchars($landCount ?? 0); ?></p>
            </div>
            <div class="stat-card">
                <h3>Apartments</h3>
                <p><?php echo htmlspecialchars($apartmentCount ?? 0); ?></p>
            </div>
        </div>

        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search assets..." onkeyup="searchTable()">
        </div>

        <div class="filter-container">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="land">Land</button>
            <button class="filter-btn" data-filter="apartment">Apartment</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Owner</th>
                        <th>District Name</th>
                        <th>Village</th>
                        <th>Block Name</th>
                        <th>Plot Number</th>
                        <th>Block Number</th>
                        <th>Apartment Number</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($error)) {
                        echo '<tr><td colspan="10" class="text-center text-danger">' . htmlspecialchars($error) . '</td></tr>';
                    } else if (empty($ownedProperties)) {
                        echo '<tr><td colspan="10" class="text-center">No owned properties found</td></tr>';
                    } else {
                        foreach ($ownedProperties as $property) {
                            // Determine property type: Land if apartment_number is null or '-', otherwise Apartment
                            $propertyType = (empty($property['apartment_number']) || $property['apartment_number'] === '-') ? 'Land' : 'Apartment';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($property['id']); ?></td>
                                <td><?php echo htmlspecialchars($property['owner_name']); ?></td>
                                <td><?php echo htmlspecialchars($property['district_name']); ?></td>
                                <td><?php echo htmlspecialchars($property['village']); ?></td>
                                <td><?php echo htmlspecialchars($property['block_name']); ?></td>
                                <td><?php echo htmlspecialchars($property['plot_number']); ?></td>
                                <td><?php echo htmlspecialchars($property['block_number']); ?></td>
                                <td><?php echo (empty($property['apartment_number']) || $property['apartment_number'] === '-') ? '-' : htmlspecialchars($property['apartment_number']); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($property['status'])); ?></td>
                                <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($property['created_at']))); ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.querySelector('table');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td');
                let found = false;
                
                // Search across all currently displayed columns
                for (let j = 0; j < td.length; j++) {
                    if (td[j]) { // Check if td[j] exists
                        if (td[j].textContent.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                
                tr[i].style.display = found ? '' : 'none';
            }
        }

        function filterTable(filter) {
            const buttons = document.querySelectorAll('.filter-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            
            if (event.target.classList.contains('filter-btn')){
                 event.target.classList.add('active');
            }

            applyFilters(); // Apply all active filters
        }

        function applyFilters() {
            const selectedType = document.querySelector('.filter-btn.active').getAttribute('data-filter');
            const searchFilter = document.getElementById('searchInput').value.toLowerCase();

            const table = document.querySelector('table');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td');
                let rowVisible = true;

                if (td.length > 7) { // Ensure row has enough columns for apartment number
                    // Get property type based on apartment number
                    const apartmentNumberText = td[7].textContent.trim();
                    const propertyType = (apartmentNumberText === '-' || apartmentNumberText === '') ? 'land' : 'apartment';
                    
                    // Apply type filter (Land/Apartment/All)
                    if (selectedType !== 'all') {
                        if (selectedType === 'land' && propertyType !== 'land') {
                            rowVisible = false;
                        } else if (selectedType === 'apartment' && propertyType !== 'apartment') {
                            rowVisible = false;
                        }
                    }

                    // Apply search filter
                    if (searchFilter) {
                        let searchMatch = false;
                        for (let j = 0; j < td.length; j++) {
                            if (td[j] && td[j].textContent.toLowerCase().includes(searchFilter)) {
                                searchMatch = true;
                                break;
                            }
                        }
                        if (!searchMatch) {
                            rowVisible = false;
                        }
                    }


                     tr[i].style.display = rowVisible ? '' : 'none';

                } else {
                    // Hide rows that don't have enough columns
                     tr[i].style.display = 'none';
                }
            }
        }

        // Initial display update and event listeners setup
        document.addEventListener('DOMContentLoaded', function() {
             // Attach event listeners to type filter buttons
            const typeFilterButtons = document.querySelectorAll('.filter-btn[data-filter]');
            typeFilterButtons.forEach(button => {
                button.addEventListener('click', function() {
                     // Set active class here
                     document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
                     this.classList.add('active');
                     applyFilters();
                });
            });

             // Attach event listener to search input
            document.getElementById('searchInput').addEventListener('keyup', function() {
                applyFilters();
            });

             // Initial filter application to display all properties
            applyFilters();
        });


        function exportToExcel() {
            const table = document.querySelector('table');
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
                        // Escape double quotes by doubling them
                        data = data.replace(/"/g, '""');
                        // Enclose data in double quotes if it contains commas, double quotes, or newlines
                        if (data.includes(',') || data.includes('"') || data.includes('\n')) {
                            data = '"' + data + '"';
                        }
                        rowData.push(data);
                    }
                    csv.push(rowData.join(','));
                }
            }

            // Create CSV file
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

    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</section>
