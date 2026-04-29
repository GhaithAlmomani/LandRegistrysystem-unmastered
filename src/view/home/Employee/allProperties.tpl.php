<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

AuthMiddleware::requireEmployee();
?>

<section class="admin-page registry-lookup-page">
    <header class="registry-lookup-hero card">
        <div class="registry-lookup-hero__badge admin-portal-badge">
            <i class="fa-solid fa-list" aria-hidden="true"></i>
            <span>Registry Lookup</span>
        </div>
        <h1 class="heading registry-lookup-hero__title">All registered properties</h1>
        <p class="registry-lookup-hero__lead">
            Read the on-chain registry and list every property returned by the contract. Requires MetaMask on the correct network.
        </p>
    </header>

    <div id="alertContainer" class="registry-lookup-alerts" aria-live="polite"></div>
    <div id="walletStatus" class="admin-page-status" role="status">Connecting to wallet…</div>

    <div class="card admin-page-card registry-lookup-table-wrap">
        <h2 class="title" style="margin-top:0;">Blockchain registry</h2>
        <p class="tutor" style="margin-top:0;">
            Results come from the registry contract referenced by your frontend ABI (<code>Abi.js</code>). Ensure MetaMask is on the matching network.
        </p>
        <div class="admin-page-actions" style="margin-top:0.5rem;">
            <button type="button" id="fetchProperties" class="inline-btn">
                <i class="fa-solid fa-rotate" aria-hidden="true"></i>
                Load all properties
            </button>
        </div>

        <div class="table-container">
            <table id="propertiesTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Owner</th>
                    <th>Description</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <p id="emptyHint" class="tutor" style="margin-bottom:0;">No rows loaded yet. Click the button above after connecting MetaMask.</p>
    </div>
</section>

<script>
(function () {
    var web3;
    var contract;

    function showAlert(message, type) {
        type = type || 'info';
        var elBox = document.getElementById('alertContainer');
        if (!elBox) return;
        var map = { success: 'pt-alert--success', error: 'pt-alert--error', info: 'pt-alert--info' };
        var cls = map[type] || map.info;
        var el = document.createElement('div');
        el.className = 'pt-alert ' + cls;
        el.setAttribute('role', 'status');
        el.innerHTML =
            '<span class="pt-alert__msg"></span>' +
            '<button type="button" class="pt-alert__close" aria-label="Dismiss">&times;</button>';
        el.querySelector('.pt-alert__msg').textContent = message;
        el.querySelector('.pt-alert__close').addEventListener('click', function () { el.remove(); });
        elBox.appendChild(el);
        window.setTimeout(function () {
            el.classList.add('pt-alert--fade');
            window.setTimeout(function () { el.remove(); }, 320);
        }, 5200);
    }

    window.addEventListener('load', async function () {
        var statusEl = document.getElementById('walletStatus');
        if (!window.ethereum) {
            if (statusEl) {
                statusEl.textContent = 'MetaMask is not installed.';
                statusEl.className = 'admin-page-status is-error';
            }
            showAlert('Please install MetaMask to use this feature.', 'error');
            return;
        }
        try {
            web3 = new Web3(window.ethereum);
            await window.ethereum.request({ method: 'eth_requestAccounts' });
            var accounts = await web3.eth.getAccounts();
            contract = new web3.eth.Contract(contractABI, contractAddress);
            if (statusEl) {
                statusEl.innerHTML = 'Connected: <span class="contract-address">' + (accounts[0] || '') + '</span>';
                statusEl.className = 'admin-page-status is-success';
            }
            showAlert('Wallet connected.', 'success');
        } catch (e) {
            showAlert((e && e.message) || String(e), 'error');
            if (statusEl) {
                statusEl.textContent = 'Wallet connection failed.';
                statusEl.className = 'admin-page-status is-error';
            }
        }
    });

    document.getElementById('fetchProperties').addEventListener('click', async function () {
        if (!contract || !web3) {
            showAlert('Wallet or contract not ready. Refresh the page.', 'error');
            return;
        }
        var tableBody = document.querySelector('#propertiesTable tbody');
        var emptyHint = document.getElementById('emptyHint');
        try {
            showAlert('Fetching properties…', 'info');
            var properties = await contract.methods.getAllProperties().call();
            tableBody.innerHTML = '';

            if (!properties || !properties.length) {
                if (emptyHint) emptyHint.textContent = 'The contract returned no properties.';
                showAlert('No properties returned.', 'info');
                return;
            }

            properties.forEach(function (property) {
                var row = document.createElement('tr');
                var id = property.id != null ? property.id : property[0];
                var owner = property.owner != null ? property.owner : property[1];
                var desc = property.description != null ? property.description : property[2];
                var lat = property.latitude != null ? property.latitude : property[3];
                var lon = property.longitude != null ? property.longitude : property[4];

                [id, owner, desc, lat, lon].forEach(function (val) {
                    var td = document.createElement('td');
                    td.textContent = val != null ? String(val) : '';
                    row.appendChild(td);
                });
                tableBody.appendChild(row);
            });
            if (emptyHint) emptyHint.textContent = 'Showing ' + properties.length + ' row(s).';
            showAlert('Properties loaded.', 'success');
        } catch (error) {
            console.error('Error fetching properties:', error);
            showAlert((error && error.message) ? error.message : 'Failed to load properties.', 'error');
        }
    });
})();
</script>
