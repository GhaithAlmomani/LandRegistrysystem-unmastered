<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

// Ensure only employees or admins can access this page
AuthMiddleware::requireStaff();
?>

<section class="admin-page">
    <h1 class="heading">QR Code Scanner</h1>

    <div class="card admin-page-card">
        <h3 class="title">Capture Address from QR</h3>
        <p class="tutor">Scan a QR code to capture and copy a wallet address quickly and accurately.</p>

        <div class="admin-page-actions">
            <button id="startButton" class="inline-btn" type="button">Loading...</button>
            <button id="copyButton" class="inline-btn" type="button" hidden onclick="copyQRContent()">
                <i class="fa-regular fa-copy" aria-hidden="true"></i>
                Copy Result
            </button>
        </div>

        <video id="video" hidden></video>
        <canvas id="canvas" hidden></canvas>

        <div id="output" class="admin-page-status" aria-live="polite">
            QR content will appear here
        </div>
    </div>
</section>

<script src="https://unpkg.com/jsqr@1.4.0/dist/jsQR.js"></script>

<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    const output = document.getElementById('output');
    const startButton = document.getElementById('startButton');
    const copyButton = document.getElementById('copyButton');
    let scanning = false;
    let qrContent = '';
    const qs = new URLSearchParams(window.location.search);
    const returnTo = qs.get('return'); // e.g. setEmpAuth
    const field = qs.get('field') || 'employeeAddress';
    const autostart = qs.get('autostart') === '1';

    function copyQRContent() {
        if (qrContent) {
            navigator.clipboard.writeText(qrContent)
                .then(() => {
                    copyButton.textContent = 'Copied!';
                    setTimeout(() => {
                        copyButton.textContent = 'Copy Result';
                    }, 2000);
                })
                .catch(err => {
                    console.error('Failed to copy: ', err);
                    copyButton.textContent = 'Failed to copy';
                    setTimeout(() => {
                        copyButton.textContent = 'Copy Result';
                    }, 2000);
                });
        }
    }

    window.addEventListener('load', function() {
        if (typeof jsQR !== 'undefined') {
            startButton.disabled = false;
            startButton.textContent = 'Scan QR Code';
        } else {
            output.textContent = 'Error: QR scanner failed to load. Please refresh the page.';
        }

        // Try to start camera immediately when launched from Set Authorization.
        // Some browsers require a user gesture; if blocked, the user can press the button.
        if (autostart) {
            setTimeout(() => {
                try { startButton.click(); } catch (e) {}
            }, 250);
        }
    });

    startButton.addEventListener('click', () => {
        if (scanning) {
            stopScanning();
        } else {
            startScanning();
        }
    });

    function startScanning() {
        navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: "environment",
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        })
            .then(function(stream) {
                scanning = true;
                startButton.textContent = 'Stop Scanning';
                startButton.className = 'inline-btn';
                video.srcObject = stream;
                video.setAttribute('playsinline', true);
                video.hidden = false;
                video.play()
                    .then(() => {
                        requestAnimationFrame(tick);
                    });
            })
            .catch(function(err) {
                output.textContent = 'Error accessing camera. Please make sure you have granted camera permissions.';
                output.className = 'admin-page-status is-error';
            });
    }

    function stopScanning() {
        scanning = false;
        startButton.textContent = 'Scan QR Code';
        startButton.className = 'inline-btn';
        video.hidden = true;
        if (video.srcObject) {
            video.srcObject.getTracks().forEach(track => track.stop());
        }
    }

    function tick() {
        if (video.readyState === video.HAVE_ENOUGH_DATA && scanning) {
            canvas.height = video.videoHeight;
            canvas.width = video.videoWidth;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

            try {
                const code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: "dontInvert",
                });

                if (code) {
                    qrContent = code.data;
                    output.textContent = `QR Code Content: ${qrContent}`;
                    output.className = 'admin-page-status is-success';
                    copyButton.hidden = false;
                    stopScanning();

                    // If this scanner was opened from Set Employee Authorization, return the result.
                    if (returnTo === 'setEmpAuth') {
                        const addr = encodeURIComponent(qrContent);
                        window.location.href = `setEmpAuth?employeeAddress=${addr}`;
                    }
                }
            } catch (err) {
                output.textContent = 'Error scanning QR code. Please try again.';
                output.className = 'admin-page-status is-error';
            }

            if (scanning) {
                requestAnimationFrame(tick);
            }
        } else if (scanning) {
            requestAnimationFrame(tick);
        }
    }
</script>
