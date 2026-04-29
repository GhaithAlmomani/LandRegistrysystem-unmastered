<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

AuthMiddleware::requireEmployee();
?>

<section class="admin-page property-transfer-page">
    <header class="property-transfer-hero card">
        <div class="property-transfer-hero__badge admin-portal-badge">
            <i class="fa-solid fa-link" aria-hidden="true"></i>
            <span>Blockchain</span>
        </div>
        <h1 class="heading property-transfer-hero__title">Property Transfer</h1>
        <p class="property-transfer-hero__lead">
            Record an on-chain ownership transfer after internal review is complete. Connect MetaMask, validate inputs,
            then submit the contract transaction from your authorized wallet.
        </p>
    </header>

    <div id="alertContainer" class="property-transfer-alerts" aria-live="polite"></div>

    <div id="walletStatus" class="admin-page-status" role="status">Connecting to wallet…</div>

    <div class="card admin-page-card">
        <h2 class="title" style="margin-top:0;">Transfer details</h2>
        <p class="tutor" style="margin-top:0;">
            Enter the property identifier and Ethereum addresses exactly as registered on the contract. Server-side checks run before the blockchain call.
        </p>

        <form id="transferOwnershipForm" method="POST" action="propertyTransfer" class="admin-page-form">
            <?= \MVC\core\CSRFToken::generateFormField() ?>

            <label class="admin-page-label" for="propertyId">Property ID</label>
            <input type="text" class="box" id="propertyId" name="propertyId" required maxlength="64"
                   autocomplete="off" placeholder="e.g. property identifier from the registry">
            <p class="admin-page-help" id="propertyIdHelp">On-chain property identifier to transfer.</p>

            <label class="admin-page-label" for="newOwner">New owner wallet</label>
            <input type="text" class="box contract-address-input" id="newOwner" name="newOwner" required maxlength="42"
                   placeholder="0x…" autocomplete="off" spellcheck="false">
            <p class="admin-page-help" id="newOwnerHelp">Buyer or transferee wallet address (42 characters).</p>

            <label class="admin-page-label" for="previousOwner">Previous owner wallet</label>
            <input type="text" class="box contract-address-input" id="previousOwner" name="previousOwner" required maxlength="42"
                   placeholder="0x…" autocomplete="off" spellcheck="false">
            <p class="admin-page-help" id="previousOwnerHelp">Current owner wallet address on the contract.</p>

            <div class="admin-page-actions property-transfer-actions">
                <button type="submit" class="inline-btn">
                    <i class="fa-solid fa-arrow-right-arrow-left" aria-hidden="true"></i>
                    Transfer ownership
                </button>
            </div>
        </form>
    </div>

    <div class="card admin-page-card property-transfer-notes">
        <h3 class="title" style="margin-top:0;">Before you submit</h3>
        <ul class="property-transfer-notes__list">
            <li>Use the same network the registry contract is deployed on (e.g. Sepolia).</li>
            <li>Ensure your connected account is authorized to perform this action.</li>
            <li>If validation fails, fix the fields shown and try again—no gas is spent until the contract call runs.</li>
        </ul>
    </div>
</section>

<script>
(function () {
    let web3;
    let contract;

    function showAlert(message, type = 'info') {
        const alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) return;

        const map = { success: 'pt-alert--success', error: 'pt-alert--error', info: 'pt-alert--info' };
        const cls = map[type] || map.info;

        const el = document.createElement('div');
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
        const statusEl = document.getElementById('walletStatus');
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

            const accounts = await web3.eth.getAccounts();
            const account = accounts[0];

            contract = new web3.eth.Contract(contractABI, contractAddress);

            if (statusEl) {
                statusEl.innerHTML =
                    'Connected: <span class="contract-address">' + account + '</span>';
                statusEl.className = 'admin-page-status is-success';
            }
            showAlert('Wallet connected.', 'success');
        } catch (error) {
            showAlert('Could not connect to MetaMask: ' + (error && error.message ? error.message : String(error)), 'error');
            if (statusEl) {
                statusEl.textContent = 'Wallet connection failed.';
                statusEl.className = 'admin-page-status is-error';
            }
        }
    });

    var form = document.getElementById('transferOwnershipForm');
    if (!form) return;

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        const fd = new FormData(form);
        var propertyId = (fd.get('propertyId') || '').toString().trim();
        var newOwner = (fd.get('newOwner') || '').toString().trim();
        var previousOwner = (fd.get('previousOwner') || '').toString().trim();

        if (!propertyId || !newOwner || !previousOwner) {
            showAlert('Please fill in all fields.', 'error');
            return;
        }

        if (!web3 || !contract) {
            showAlert('Wallet or contract not ready. Refresh and connect MetaMask.', 'error');
            return;
        }

        try {
            const validateResp = await fetch('propertyTransfer', {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const validateJson = await validateResp.json().catch(function () { return null; });
            if (!validateResp.ok) {
                var msg = validateJson && validateJson.errors
                    ? Object.values(validateJson.errors).flat().join('\n')
                    : 'Validation failed.';
                showAlert(msg, 'error');
                return;
            }

            showAlert('Processing transfer… approve the transaction in MetaMask.', 'info');

            const accounts = await web3.eth.getAccounts();

            await contract.methods
                .transferOwnership(propertyId, previousOwner, newOwner)
                .send({ from: accounts[0] })
                .on('transactionHash', function (hash) {
                    showAlert('Submitted. Tx: ' + hash.substring(0, 12) + '…', 'info');
                })
                .on('receipt', function () {
                    showAlert('Ownership transferred successfully.', 'success');
                    var st = document.getElementById('walletStatus');
                    if (st && accounts[0]) {
                        st.innerHTML = 'Connected: <span class="contract-address">' + accounts[0] + '</span>';
                        st.className = 'admin-page-status is-success';
                    }
                    form.reset();
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
            var st2 = document.getElementById('walletStatus');
            if (st2 && typeof web3 !== 'undefined') {
                web3.eth.getAccounts().then(function (accs) {
                    if (accs && accs[0]) {
                        st2.innerHTML = 'Connected: <span class="contract-address">' + accs[0] + '</span>';
                        st2.className = 'admin-page-status is-error';
                    }
                }).catch(function () {});
            }
        }
    });
})();
</script>
