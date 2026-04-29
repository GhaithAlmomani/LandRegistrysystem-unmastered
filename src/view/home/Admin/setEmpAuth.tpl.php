<section>
    <h1 class="heading">Employee Authorization</h1>

    <div class="card admin-page-card">
        <h3 class="title">Set Authorization</h3>
        <p class="tutor">Grant or revoke employee permissions using the verified blockchain address.</p>

        <?php
        $prefillEmployeeAddress = isset($_GET['employeeAddress']) ? trim((string)$_GET['employeeAddress']) : '';
        ?>

        <form id="authorizationForm" class="admin-page-form">
            <label class="admin-page-label" for="employeeAddress">Employee Address</label>
            <div class="admin-input-with-action">
                <input type="text" class="box" id="employeeAddress" placeholder="0x..." required value="<?= htmlspecialchars($prefillEmployeeAddress) ?>">
                <button type="button" class="admin-input-action-btn" id="scanQrBtn" aria-label="Scan QR code">
                    <i class="fa-solid fa-qrcode" aria-hidden="true"></i>
                </button>
            </div>
            <p class="admin-page-help">Paste the employee wallet address exactly as provided.</p>

            <label class="admin-page-label" for="isAuthorized">Authorization</label>
            <select id="isAuthorized" class="box" required>
                <option value="true">Authorized</option>
                <option value="false">Not authorized</option>
            </select>

            <div class="admin-page-actions">
                <button type="submit" class="inline-btn">
                    <i class="fa-solid fa-user-check" aria-hidden="true"></i>
                    Save Authorization
                </button>
            </div>

            <div id="status" class="admin-page-status" aria-live="polite"></div>
        </form>
    </div>

    <div class="card admin-page-card admin-qr-inline" id="qrInline" hidden>
        <h3 class="title">Scan QR Code</h3>
        <p class="tutor">Point the camera at the QR code. The result will be pasted into Employee Address automatically.</p>

        <div class="admin-page-actions">
            <button type="button" class="inline-btn" id="startInlineScanBtn">
                <i class="fa-solid fa-camera" aria-hidden="true"></i>
                Start Camera
            </button>
            <button type="button" class="inline-btn" id="stopInlineScanBtn" disabled>
                <i class="fa-solid fa-circle-stop" aria-hidden="true"></i>
                Stop
            </button>
        </div>

        <video id="inlineVideo" hidden playsinline></video>
        <canvas id="inlineCanvas" hidden></canvas>

        <div id="inlineOutput" class="admin-page-status" aria-live="polite">Ready to scan.</div>
    </div>


<!--<p id="status"></p> -->

<script src="https://unpkg.com/jsqr@1.4.0/dist/jsQR.js"></script>
<script>

    // Initialize Web3
    if (typeof window.ethereum !== 'undefined') {
        const web3 = new Web3(window.ethereum);

        // Prompt user to connect their wallet
        window.ethereum.request({ method: 'eth_requestAccounts' });

        // Get the smart contract
        const contract = new web3.eth.Contract(contractABI, contractAddress);

        // Handle form submission
        document.getElementById('authorizationForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const employeeAddress = document.getElementById('employeeAddress').value.trim();
            const isAuthorized = document.getElementById('isAuthorized').value === 'true';
            const statusEl = document.getElementById('status');
            statusEl.className = 'admin-page-status';
            statusEl.textContent = 'Submitting transaction...';

            try {
                if (!employeeAddress || !employeeAddress.startsWith('0x') || employeeAddress.length < 10) {
                    statusEl.classList.add('is-warning');
                    statusEl.textContent = 'Please enter a valid wallet address.';
                    return;
                }

                const accounts = await web3.eth.getAccounts();
                const sender = accounts[0];

                // Call the setEmployeeAuthorization function
                await contract.methods.setEmployeeAuthorization(employeeAddress, isAuthorized).send({ from: sender });

                statusEl.classList.add('is-success');
                statusEl.textContent = "Authorization updated successfully.";
            } catch (error) {
                console.error(error);
                statusEl.classList.add('is-error');
                statusEl.textContent = "Error: " + (error?.message ?? 'Transaction failed');
            }
        });
    } else {
        const statusEl = document.getElementById('status');
        statusEl.className = 'admin-page-status is-error';
        statusEl.textContent = "MetaMask is required to use this feature.";
    }

    // Inline QR scanning (fills Employee Address on success)
    const qrInline = document.getElementById('qrInline');
    const scanQrBtn = document.getElementById('scanQrBtn');
    const startInlineScanBtn = document.getElementById('startInlineScanBtn');
    const stopInlineScanBtn = document.getElementById('stopInlineScanBtn');
    const inlineVideo = document.getElementById('inlineVideo');
    const inlineCanvas = document.getElementById('inlineCanvas');
    const inlineCtx = inlineCanvas.getContext('2d');
    const inlineOutput = document.getElementById('inlineOutput');
    const employeeAddressInput = document.getElementById('employeeAddress');
    let inlineStream = null;
    let inlineScanning = false;

    function showInlineScanner() {
        qrInline.hidden = false;
        qrInline.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function setInlineStatus(text, kind) {
        inlineOutput.className = 'admin-page-status';
        if (kind) inlineOutput.classList.add(kind);
        inlineOutput.textContent = text;
    }

    async function startInlineScan() {
        if (inlineScanning) return;
        if (typeof jsQR === 'undefined') {
            setInlineStatus('QR scanner failed to load. Please refresh the page.', 'is-error');
            return;
        }

        try {
            inlineStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: "environment", width: { ideal: 1280 }, height: { ideal: 720 } }
            });
            inlineScanning = true;
            startInlineScanBtn.disabled = true;
            stopInlineScanBtn.disabled = false;
            inlineVideo.srcObject = inlineStream;
            inlineVideo.hidden = false;
            await inlineVideo.play();
            setInlineStatus('Scanning…', null);
            requestAnimationFrame(inlineTick);
        } catch (err) {
            setInlineStatus('Camera access denied or unavailable.', 'is-error');
        }
    }

    function stopInlineScan() {
        inlineScanning = false;
        startInlineScanBtn.disabled = false;
        stopInlineScanBtn.disabled = true;
        inlineVideo.hidden = true;

        if (inlineStream) {
            inlineStream.getTracks().forEach(t => t.stop());
            inlineStream = null;
        }
        setInlineStatus('Stopped.', 'is-warning');
    }

    function inlineTick() {
        if (!inlineScanning) return;

        if (inlineVideo.readyState === inlineVideo.HAVE_ENOUGH_DATA) {
            inlineCanvas.height = inlineVideo.videoHeight;
            inlineCanvas.width = inlineVideo.videoWidth;
            inlineCtx.drawImage(inlineVideo, 0, 0, inlineCanvas.width, inlineCanvas.height);
            const imageData = inlineCtx.getImageData(0, 0, inlineCanvas.width, inlineCanvas.height);

            const code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: "dontInvert" });
            if (code && code.data) {
                const scanned = String(code.data).trim();
                employeeAddressInput.value = scanned;
                employeeAddressInput.focus();
                setInlineStatus('Scanned and pasted into Employee Address.', 'is-success');
                stopInlineScan();
                return;
            }
        }

        requestAnimationFrame(inlineTick);
    }

    scanQrBtn.addEventListener('click', () => { showInlineScanner(); startInlineScan(); });
    startInlineScanBtn.addEventListener('click', startInlineScan);
    stopInlineScanBtn.addEventListener('click', stopInlineScan);
</script>
</section>