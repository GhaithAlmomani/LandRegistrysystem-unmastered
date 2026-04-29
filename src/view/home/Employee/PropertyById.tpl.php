<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

AuthMiddleware::requireEmployee();
?>

<section class="admin-page registry-lookup-page">
    <header class="registry-lookup-hero card">
        <div class="registry-lookup-hero__badge admin-portal-badge">
            <i class="fa-solid fa-hashtag" aria-hidden="true"></i>
            <span>Registry Lookup</span>
        </div>
        <h1 class="heading registry-lookup-hero__title">Property by ID</h1>
        <p class="registry-lookup-hero__lead">
            Fetch a single parcel by its on-chain property identifier. Connect MetaMask on the same network as the registry contract.
        </p>
    </header>

    <div id="alertContainer" class="registry-lookup-alerts" aria-live="polite"></div>
    <div id="walletStatus" class="admin-page-status" role="status">Connecting to wallet…</div>

    <div class="card admin-page-card">
        <h2 class="title" style="margin-top:0;">Look up</h2>
        <form id="propertyByIdForm" class="admin-page-form" action="#" method="get">
            <label class="admin-page-label" for="propertyId">Property ID</label>
            <div class="admin-search-row">
                <input type="text" class="box" id="propertyId" name="propertyId" inputmode="numeric" required
                       placeholder="e.g. 0" autocomplete="off" aria-describedby="propertyIdHelp">
                <button type="submit" class="inline-btn">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    Fetch
                </button>
            </div>
            <p class="admin-page-help" id="propertyIdHelp">On-chain property ID from the registry.</p>
        </form>
    </div>

    <div class="card admin-page-card" id="resultCard" hidden>
        <h2 class="title" style="margin-top:0;">Result</h2>
        <div id="output" class="registry-lookup-output"></div>
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

    function escapeHtml(s) {
        var d = document.createElement('div');
        d.textContent = s == null ? '' : String(s);
        return d.innerHTML;
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
            contract = new web3.eth.Contract(contractABI, contractAddress);
            var accounts = await web3.eth.getAccounts();
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

    document.getElementById('propertyByIdForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        var propertyId = document.getElementById('propertyId').value.trim();
        var output = document.getElementById('output');
        var resultCard = document.getElementById('resultCard');

        if (!propertyId) {
            showAlert('Please enter a property ID.', 'error');
            return;
        }
        if (!contract) {
            showAlert('Contract not ready. Connect MetaMask and wait.', 'error');
            return;
        }

        try {
            var property = await contract.methods.getPropertyById(propertyId).call();
            var id = property.id != null ? property.id : property[0];
            var owner = property.owner != null ? property.owner : property[1];
            var description = property.description != null ? property.description : property[2];
            var latitude = property.latitude != null ? property.latitude : property[3];
            var longitude = property.longitude != null ? property.longitude : property[4];

            output.innerHTML =
                '<dl class="registry-lookup-dl">' +
                '<dt>ID</dt><dd>' + escapeHtml(id) + '</dd>' +
                '<dt>Owner</dt><dd class="contract-wrap">' + escapeHtml(owner) + '</dd>' +
                '<dt>Description</dt><dd>' + escapeHtml(description) + '</dd>' +
                '<dt>Latitude</dt><dd>' + escapeHtml(latitude) + '</dd>' +
                '<dt>Longitude</dt><dd>' + escapeHtml(longitude) + '</dd>' +
                '</dl>';
            resultCard.hidden = false;
            showAlert('Property loaded.', 'success');
        } catch (error) {
            resultCard.hidden = true;
            output.innerHTML = '';
            showAlert((error && error.message) ? error.message : 'Failed to fetch property.', 'error');
        }
    });
})();
</script>
