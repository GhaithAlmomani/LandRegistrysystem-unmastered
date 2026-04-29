<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

AuthMiddleware::requireEmployee();
?>

<section class="admin-page registry-lookup-page">
    <header class="registry-lookup-hero card">
        <div class="registry-lookup-hero__badge admin-portal-badge">
            <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
            <span>Registry Lookup</span>
        </div>
        <h1 class="heading registry-lookup-hero__title">Property info</h1>
        <p class="registry-lookup-hero__lead">
            Retrieve extended parcel data including transfer authorization status. Uses the same contract as other lookup tools.
        </p>
    </header>

    <div id="alertContainer" class="registry-lookup-alerts" aria-live="polite"></div>
    <div id="walletStatus" class="admin-page-status" role="status">Connecting to wallet…</div>

    <div class="card admin-page-card">
        <h2 class="title" style="margin-top:0;">Look up</h2>
        <form id="propertyForm" class="admin-page-form" action="#" method="get">
            <label class="admin-page-label" for="propertyId">Property ID</label>
            <div class="admin-search-row">
                <input type="text" class="box" id="propertyId" name="propertyId" inputmode="numeric" required
                       placeholder="e.g. 0" autocomplete="off" aria-describedby="propertyIdHelp">
                <button type="submit" class="inline-btn">
                    <i class="fa-solid fa-arrow-down-short-wide" aria-hidden="true"></i>
                    Get info
                </button>
            </div>
            <p class="admin-page-help" id="propertyIdHelp">Returns coordinates, owner, and whether transfer is authorized on-chain.</p>
        </form>
    </div>

    <div class="card admin-page-card" id="detailsCard" hidden>
        <h2 class="title" style="margin-top:0;">Property details</h2>
        <dl class="registry-lookup-dl" id="propertyDl">
            <dt>ID</dt>
            <dd id="propertyIdDisplay">—</dd>
            <dt>Owner</dt>
            <dd id="owner" class="contract-wrap">—</dd>
            <dt>Description</dt>
            <dd id="description">—</dd>
            <dt>Latitude</dt>
            <dd id="latitude">—</dd>
            <dt>Longitude</dt>
            <dd id="longitude">—</dd>
            <dt>Authorized for transfer</dt>
            <dd id="isAuthorized">—</dd>
        </dl>
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

    // Support quick lookup: PropertyInfo?propertyId=123
    window.addEventListener('load', function () {
        try {
            var url = new URL(window.location.href);
            var qid = (url.searchParams.get('propertyId') || '').trim();
            if (qid) {
                var input = document.getElementById('propertyId');
                if (input && !input.value) {
                    input.value = qid;
                }
                // Auto-run once (after wallet/contract init completes)
                window.setTimeout(function () {
                    document.getElementById('propertyForm')?.dispatchEvent(new Event('submit', { cancelable: true }));
                }, 350);
            }
        } catch (_) {}
    });

    document.getElementById('propertyForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        var propertyId = document.getElementById('propertyId').value.trim();
        var detailsCard = document.getElementById('detailsCard');

        if (!propertyId) {
            showAlert('Please enter a property ID.', 'error');
            return;
        }
        if (!contract) {
            showAlert('Contract not ready. Connect MetaMask and wait.', 'error');
            return;
        }

        try {
            var result = await contract.methods.getPropertyInfo(propertyId).call();

            document.getElementById('propertyIdDisplay').textContent =
                result.id != null ? String(result.id) : String(result[0] != null ? result[0] : '');
            document.getElementById('owner').textContent =
                result.owner != null ? String(result.owner) : String(result[1] != null ? result[1] : '');
            document.getElementById('description').textContent =
                result.description != null ? String(result.description) : String(result[2] != null ? result[2] : '');
            document.getElementById('latitude').textContent =
                result.latitude != null ? String(result.latitude) : String(result[3] != null ? result[3] : '');
            document.getElementById('longitude').textContent =
                result.longitude != null ? String(result.longitude) : String(result[4] != null ? result[4] : '');

            var auth = result.isAuthorizedForTransfer;
            if (auth === undefined && result[5] !== undefined) auth = result[5];
            document.getElementById('isAuthorized').textContent =
                (auth === undefined || auth === null) ? '—' : (auth ? 'Yes' : 'No');

            detailsCard.hidden = false;
            showAlert('Property info loaded.', 'success');
        } catch (error) {
            detailsCard.hidden = true;
            console.error('Error fetching property info:', error);
            showAlert(
                (error && error.message) ? error.message : 'Failed to fetch property information.',
                'error'
            );
        }
    });
})();
</script>
