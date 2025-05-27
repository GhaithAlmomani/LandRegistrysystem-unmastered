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
                <p>2</p>
            </div>
            <div class="stat-card">
                <h3>Total Area</h3>
                <p>1,786 M²</p>
            </div>
            <div class="stat-card">
                <h3>Properties in Amman</h3>
                <p>1</p>
            </div>
            <div class="stat-card">
                <h3>Properties in Zarqa</h3>
                <p>1</p>
            </div>
        </div>

        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search assets..." onkeyup="searchTable()">
        </div>

        <div class="filter-container">
            <button class="filter-btn active" onclick="filterTable('all')">All</button>
            <button class="filter-btn" onclick="filterTable('amman')">Amman</button>
            <button class="filter-btn" onclick="filterTable('zarqa')">Zarqa</button>
            <button class="filter-btn" onclick="filterTable('land')">Land</button>
            <button class="filter-btn" onclick="filterTable('apartment')">Apartment</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Number</th>
                        <th>Owner</th>
                        <th>Land Directorate</th>
                        <th>District Name</th>
                        <th>Village</th>
                        <th>Block Name</th>
                        <th>Type</th>
                        <th>Plot Number</th>
                        <th>Block Number</th>
                        <th>District Number</th>
                        <th>Apartment Number</th>
                        <th>Apartment floor</th>
                        <th>Area (M²)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Ghaith Almomani</td>
                        <td>Zarqa Directorate</td>
                        <td>Zarqa</td>
                        <td>Albatrawi</td>
                        <td>Southern Batrawi</td>
                        <td>Own</td>
                        <td>0021</td>
                        <td>22514</td>
                        <td>14</td>
                        <td>-</td>
                        <td>-</td>
                        <td>866</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Khaled Khader</td>
                        <td>Amman Directorate</td>
                        <td>Amman</td>
                        <td>Almsherfeh</td>
                        <td>Northen Marka</td>
                        <td>Own</td>
                        <td>0031</td>
                        <td>28124</td>
                        <td>16</td>
                        <td>-</td>
                        <td>-</td>
                        <td>920</td>
                    </tr>
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
                
                for (let j = 0; j < td.length; j++) {
                    if (td[j].textContent.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                
                tr[i].style.display = found ? '' : 'none';
            }
        }

        function filterTable(filter) {
            const buttons = document.querySelectorAll('.filter-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            const table = document.querySelector('table');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td');
                if (filter === 'all') {
                    tr[i].style.display = '';
                } else {
                    const district = td[3].textContent.toLowerCase();
                    const type = td[6].textContent.toLowerCase();
                    
                    if (filter === 'amman' && district === 'amman') {
                        tr[i].style.display = '';
                    } else if (filter === 'zarqa' && district === 'zarqa') {
                        tr[i].style.display = '';
                    } else if (filter === 'land' && type === 'land') {
                        tr[i].style.display = '';
                    } else if (filter === 'apartment' && type === 'apartment') {
                        tr[i].style.display = '';
                    } else {
                        tr[i].style.display = 'none';
                    }
                }
            }
        }

        function exportToExcel() {
            // This is a placeholder for Excel export functionality
            alert('Export to Excel functionality will be implemented here');
        }
    </script>

    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</section>
