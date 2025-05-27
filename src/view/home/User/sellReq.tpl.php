<section>
    <div class="flex-container">

        <style>

            .flex-container {
                display: flex;
                justify-content: space-between;
                gap: 5rem;
                margin-bottom: 5rem;
            }

            .table-container {
                flex: 1;
                background-color: var(--white);
                border-radius: 1rem;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                padding: 2rem;
                transition: transform 0.3s ease;
            }

            .table-container:hover {
                transform: translateY(-5px);
                box-shadow: 0 5px 15px rgba(0, 86, 15, 0.15);
            }

            .table-container table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0 1rem;
            }

            .table-container th {
                text-align: center;
                padding: 1rem;
                font-size: 1.6rem;
                color: var(--black);
                border-bottom: 2px solid var(--main-color);
            }

            .table-container td {
                padding: 1rem;
                font-size: 1.4rem;
            }

            .table-container td:first-child {
                font-weight: 600;
                color: var(--black);
            }

            .table-container .box {
                width: 100%;
                padding: 1.2rem;
                border: 1px solid #ddd;
                border-radius: 0.5rem;
                font-size: 1.4rem;
                transition: all 0.3s ease;
                background-color: #f8f9fa;
            }

            .table-container .box:hover {
                border-color: var(--main-color);
                background-color: #fff;
            }

            .table-container .box:focus {
                border-color: var(--main-color);
                box-shadow: 0 0 0 2px rgba(0, 86, 15, 0.1);
                background-color: #fff;
            }

            .table-container .box::placeholder {
                color: #aaa;
            }

            .submit-container {
                text-align: center;
                margin-top: 2rem;
            }

            .submit-container .btn {
                padding: 1.2rem 4rem;
                font-size: 1.6rem;
                border-radius: 0.5rem;
                background-color: var(--main-color);
                color: #fff;
                border: none;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 2px 5px rgba(0, 86, 15, 0.2);
            }

            .submit-container .btn:hover {
                background-color: #003d06;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 86, 15, 0.2);
            }

            /* Popup Styling */
            .popup {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                justify-content: center;
                align-items: center;
                z-index: 1000;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .popup.active {
                opacity: 1;
            }

            .popup-content {
                background-color: #fff;
                border-radius: 1.2rem;
                padding: 3rem;
                text-align: center;
                max-width: 450px;
                width: 90%;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
                transform: translateY(-20px);
                transition: transform 0.3s ease;
                position: relative;
                border: 2px solid var(--main-color);
            }

            .popup.active .popup-content {
                transform: translateY(0);
            }

            .popup h2 {
                font-size: 2rem;
                margin-bottom: 1.5rem;
                color: var(--black);
            }

            .popup .tracking-container {
                display: flex;
                align-items: center;
                gap: 1rem;
                margin: 2rem 0;
            }

            .popup .tracking-number {
                font-size: 2.2rem;
                color: var(--main-color);
                font-weight: bold;
                padding: 1.5rem;
                background-color: #f8f9fa;
                border-radius: 0.8rem;
                border: 2px dashed var(--main-color);
                letter-spacing: 1px;
                flex: 1;
            }

            .popup .copy-btn {
                background-color: var(--main-color);
                color: white;
                border: none;
                padding: 1.5rem;
                border-radius: 0.8rem;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.6rem;
            }

            .popup .copy-btn:hover {
                background-color: #003d06;
                transform: translateY(-2px);
            }

            .popup .copy-btn.copied {
                background-color: #28a745;
            }

            .popup .save-message {
                font-size: 1.6rem;
                color: #666;
                margin-top: 1rem;
            }

            .close {
                position: absolute;
                top: 1.5rem;
                right: 1.5rem;
                font-size: 2.4rem;
                font-weight: bold;
                color: #666;
                cursor: pointer;
                width: 3rem;
                height: 3rem;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                transition: all 0.3s ease;
            }

            .close:hover {
                background-color: #f8f9fa;
                color: var(--main-color);
                transform: rotate(90deg);
            }

            .popup .icon {
                font-size: 4rem;
                color: var(--main-color);
                margin-bottom: 1.5rem;
            }
        </style>

        <div class="table-container">
            <h1 class="heading">Seller Information</h1>
            <table>
                <tr>
                    <th>Field</th>
                    <th>Details</th>
                </tr>
                <tr>
                    <td>Full name</td>
                    <td><input type="text" placeholder="Enter your full name" class="box" /></td>
                </tr>
                <tr>
                    <td>National ID</td>
                    <td><input type="email" placeholder="Enter your national ID" class="box" /></td>
                </tr>
                <tr>
                    <td>Phone</td>
                    <td><input type="tel" placeholder="Enter your phone number" class="box" /></td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td><input type="text" placeholder="Enter your address" class="box" /></td>
                </tr>
            </table>
        </div>

        <div class="table-container">
            <h1 class="heading">Buyer Information</h1>
            <table>
                <tr>
                    <th>Field</th>
                    <th>Details</th>
                </tr>
                <tr>
                    <td>Full name</td>
                    <td><input type="text" placeholder="Enter your full name" class="box" /></td>
                </tr>
                <tr>
                    <td>National ID</td>
                    <td><input type="email" placeholder="Enter your national ID" class="box" /></td>
                </tr>
                <tr>
                    <td>Phone</td>
                    <td><input type="tel" placeholder="Enter your phone number" class="box" /></td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td><input type="text" placeholder="Enter your address" class="box" /></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="submit-container">
        <button type="button" class="btn" onclick="generateTrackingNumber()">Submit</button>
    </div>

    <!-- Popup Modal -->
    <div id="popup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <div class="icon">✓</div>
            <h2>Request Submitted Successfully!</h2>
            <div class="tracking-container">
                <p class="tracking-number" id="tracking-number"></p>
                <button class="copy-btn" onclick="copyTrackingNumber()">Copy</button>
            </div>
            <p class="save-message">Please save this tracking number for future reference</p>
        </div>
    </div>

    <script>
        function generateTrackingNumber() {
            const trackingNumber = 'TRK-' + Math.random().toString(36).substr(2, 9).toUpperCase();
            document.getElementById('tracking-number').innerText = trackingNumber;
            const popup = document.getElementById('popup');
            popup.style.display = 'flex';
            setTimeout(() => {
                popup.classList.add('active');
            }, 10);
        }

        function closePopup() {
            const popup = document.getElementById('popup');
            popup.classList.remove('active');
            setTimeout(() => {
                popup.style.display = 'none';
            }, 300);
        }

        function copyTrackingNumber() {
            const trackingNumber = document.getElementById('tracking-number').innerText;
            navigator.clipboard.writeText(trackingNumber).then(() => {
                const copyBtn = document.querySelector('.copy-btn');
                copyBtn.textContent = 'Copied!';
                copyBtn.classList.add('copied');
                
                setTimeout(() => {
                    copyBtn.textContent = 'Copy';
                    copyBtn.classList.remove('copied');
                }, 2000);
            });
        }
    </script>

</section>

