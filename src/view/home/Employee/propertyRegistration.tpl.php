<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

AuthMiddleware::requireEmployee();
?>

<section class="admin-page property-registration-page">
    <header class="property-registration-hero card">
        <div class="property-registration-hero__badge admin-portal-badge">
            <i class="fa-solid fa-map-location-dot" aria-hidden="true"></i>
            <span>Blockchain</span>
        </div>
        <h1 class="heading property-registration-hero__title">Property Registration</h1>
        <p class="property-registration-hero__lead">
            Register a new parcel on-chain after internal checks. Property IDs are assigned by the contract when the transaction succeeds.
            Connect MetaMask, complete the form, then confirm the transaction.
        </p>
    </header>

    <div id="alertContainer" class="property-registration-alerts" aria-live="polite"></div>

    <div id="walletStatus" class="admin-page-status" role="status">Connecting to wallet…</div>

    <div class="card admin-page-card">
        <h2 class="title" style="margin-top:0;">Registration details</h2>
        <p class="tutor" style="margin-top:0;">
            Provide the owner wallet, a short description, and coordinates (latitude −90…90°, longitude −180…180°). The server validates before any gas is used.
        </p>

        <form id="registerForm" method="POST" action="propertyRegistration" class="admin-page-form">
            <?= \MVC\core\CSRFToken::generateFormField() ?>

            <label class="admin-page-label" for="owner">Owner wallet</label>
            <input type="text" class="box contract-address-input" id="owner" name="owner" required maxlength="42"
                   placeholder="0x…" autocomplete="off" spellcheck="false" aria-describedby="ownerHelp">
            <p class="admin-page-help" id="ownerHelp">Ethereum address of the registered owner.</p>

            <label class="admin-page-label" for="description">Description</label>
            <input type="text" class="box" id="description" name="description" required maxlength="255"
                   placeholder="Brief parcel description" autocomplete="off" aria-describedby="descriptionHelp">
            <p class="admin-page-help" id="descriptionHelp">Short label or notes stored with the property (max 255 characters).</p>

            <div class="property-registration-coords">
                <div>
                    <label class="admin-page-label" for="latitude">Latitude</label>
                    <input type="text" class="box" id="latitude" name="latitude" required inputmode="decimal"
                           placeholder="e.g. 31.9539" autocomplete="off" aria-describedby="latitudeHelp">
                    <p class="admin-page-help" id="latitudeHelp">Degrees north/south (−90 to 90).</p>
                </div>
                <div>
                    <label class="admin-page-label" for="longitude">Longitude</label>
                    <input type="text" class="box" id="longitude" name="longitude" required inputmode="decimal"
                           placeholder="e.g. 35.9106" autocomplete="off" aria-describedby="longitudeHelp">
                    <p class="admin-page-help" id="longitudeHelp">Degrees east/west (−180 to 180).</p>
                </div>
            </div>

            <div class="admin-page-actions property-registration-actions">
                <button type="submit" class="inline-btn">
                    <i class="fa-solid fa-plus-circle" aria-hidden="true"></i>
                    Register property
                </button>
            </div>
        </form>
    </div>

    <div class="card admin-page-card property-registration-notes">
        <h3 class="title" style="margin-top:0;">Before you submit</h3>
        <ul class="property-registration-notes__list">
            <li>Use the network where the registry contract is deployed (e.g. Sepolia).</li>
            <li>Coordinates must fall within valid latitude/longitude ranges or validation will fail.</li>
            <li>After a successful receipt, the form clears—you can register another parcel.</li>
        </ul>
    </div>
</section>

<script>
(function () {
    let web3;
    let contract;

    function showAlert(message, type) {
        type = type || 'info';
        var alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) return;

        var map = { success: 'pt-alert--success', error: 'pt-alert--error', info: 'pt-alert--info' };
        var cls = map[type] || map.info;

        var el = document.createElement('div');
        el.className = 'pt-alert ' + cls;
        el.setAttribute('role', 'status');
        el.innerHTML =
            '<span class="pt-alert__msg"></span>' +
            '<button type="button" class="pt-alert__close" aria-label="Dismiss">&times;</button>';
        el.querySelector('.pt-alert__msg').textContent = message;

        el.querySelector('.pt-alert__close').addEventListener('click', function () {
            el.remove();
        });

        alertContainer.appendChild(el);

        window.setTimeout(function () {
            el.classList.add('pt-alert--fade');
            window.setTimeout(function () {
                el.remove();
            }, 320);
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
            var account = accounts[0];

            contract = new web3.eth.Contract(contractABI, contractAddress);

            if (statusEl) {
                statusEl.innerHTML =
                    'Connected: <span class="contract-address">' + account + '</span>';
                statusEl.className = 'admin-page-status is-success';
            }
            showAlert('Wallet connected.', 'success');
        } catch (error) {
            showAlert(
                'Could not connect to MetaMask: ' + (error && error.message ? error.message : String(error)),
                'error'
            );
            if (statusEl) {
                statusEl.textContent = 'Wallet connection failed.';
                statusEl.className = 'admin-page-status is-error';
            }
        }
    });

    var form = document.getElementById('registerForm');
    if (!form) return;

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        var fd = new FormData(form);
        var owner = (fd.get('owner') || '').toString().trim();
        var description = (fd.get('description') || '').toString().trim();
        var latitude = (fd.get('latitude') || '').toString().trim();
        var longitude = (fd.get('longitude') || '').toString().trim();

        if (!owner || !description || !latitude || !longitude) {
            showAlert('Please fill in all fields.', 'error');
            return;
        }

        if (!web3 || !contract) {
            showAlert('Wallet or contract not ready. Refresh and connect MetaMask.', 'error');
            return;
        }

        try {
            var validateResp = await fetch('propertyRegistration', {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            var validateJson = await validateResp.json().catch(function () {
                return null;
            });
            if (!validateResp.ok) {
                var msg =
                    validateJson && validateJson.errors
                        ? Object.values(validateJson.errors).flat().join('\n')
                        : 'Validation failed.';
                showAlert(msg, 'error');
                return;
            }

            showAlert('Processing… approve the transaction in MetaMask.', 'info');

            var accounts = await web3.eth.getAccounts();

            contract.methods
                .registerProperty(owner, description, latitude, longitude)
                .send({ from: accounts[0] })
                .on('transactionHash', function (hash) {
                    showAlert('Submitted. Tx: ' + hash.substring(0, 12) + '…', 'info');
                })
                .on('receipt', function () {
                    showAlert('Property registered successfully.', 'success');
                    form.reset();
                    var st = document.getElementById('walletStatus');
                    if (st && accounts[0]) {
                        st.innerHTML = 'Connected: <span class="contract-address">' + accounts[0] + '</span>';
                        st.className = 'admin-page-status is-success';
                    }
                })
                .on('error', function (error) {
                    showAlert(error && error.message ? error.message : 'Transaction failed.', 'error');
                    var st = document.getElementById('walletStatus');
                    if (st && accounts[0]) {
                        st.innerHTML = 'Connected: <span class="contract-address">' + accounts[0] + '</span>';
                        st.className = 'admin-page-status is-warning';
                    }
                });
        } catch (error) {
            showAlert(error && error.message ? error.message : String(error), 'error');
        }
    });
})();
</script>
