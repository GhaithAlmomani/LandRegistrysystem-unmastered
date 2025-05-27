
<section class="container">
    <div class="scanner-container" style="background-color: var(--white); border-radius: .5rem; padding: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 60rem; margin: 0 auto;">
        <h3 style="font-size: 2.5rem; color: var(--black); margin-bottom: 2.5rem; border-bottom: var(--border); padding-bottom: 1.5rem; text-transform: capitalize;">QR Code Scanner</h3>

        <button id="startButton" class="btn" style="width: auto; display: inline-block; margin: 1rem auto;">Loading...</button>

        <video id="video" style="display: none; margin: 1rem 0; max-width: 100%; border-radius: .5rem;"></video>
        <canvas id="canvas" style="display: none;"></canvas>

        <div id="output" style="margin-top: 2rem; padding: 1.5rem; border: var(--border); border-radius: .5rem; background-color: var(--light-bg); font-size: 1.8rem; color: var(--light-color);">
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
    let scanning = false;

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
                video.style.display = 'block';
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
        video.style.display = 'none';
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
                    output.textContent = `QR Code Content: ${code.data}`;
                    output.style.color = 'var(--main-color)';
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
