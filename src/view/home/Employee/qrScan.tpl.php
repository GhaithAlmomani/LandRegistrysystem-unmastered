<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

// Ensure only employees or admins can access this page
if (!isset($_SESSION['Username'])) {
    header('Location: /login');
    exit();
}
if (!isset($_SESSION['role']) || ($_SESSION['role'] != \MVC\middleware\AuthMiddleware::ROLE_EMPLOYEE && $_SESSION['role'] != \MVC\middleware\AuthMiddleware::ROLE_ADMIN)) {
    header('Location: /home');
    exit();
}

require_once __DIR__ . '/../../layouts/navbar.tpl.php';
?>

<section class="container">
    <div class="scanner-container">
        <h3>QR Code Scanner</h3>

        <button id="startButton" class="btn">Loading...</button>

        <video id="video" hidden></video>
        <canvas id="canvas" hidden></canvas>

        <div id="output">
            QR content will appear here
        </div>
        <button id="copyButton" class="btn" hidden onclick="copyQRContent()">Copy Result</button>
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
                startButton.className = 'delete-btn';
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
                output.style.color = 'var(--red)';
            });
    }

    function stopScanning() {
        scanning = false;
        startButton.textContent = 'Start Scanning';
        startButton.className = 'btn';
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
                    output.style.color = 'var(--main-color)';
                    copyButton.hidden = false;
                    stopScanning();
                }
            } catch (err) {
                output.textContent = 'Error scanning QR code. Please try again.';
                output.style.color = 'var(--red)';
            }

            if (scanning) {
                requestAnimationFrame(tick);
            }
        } else if (scanning) {
            requestAnimationFrame(tick);
        }
    }
</script>
