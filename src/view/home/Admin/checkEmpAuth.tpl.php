<section>
    <h1 class="heading">Employee Authorization</h1>

    <div class="card admin-page-card">
        <h3 class="title">Check Authorization</h3>
        <p class="tutor">Verify whether a specific employee wallet address is authorized.</p>

        <form id="checkForm" class="admin-page-form">
            <label class="admin-page-label" for="employeeAddress">Employee Address</label>
            <input type="text" class="box" id="employeeAddress" placeholder="0x..." required>

            <div class="admin-page-actions">
                <button type="submit" class="inline-btn">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    Check
                </button>
                <a class="inline-btn" href="setEmpAuth">
                    <i class="fa-solid fa-user-check" aria-hidden="true"></i>
                    Set Authorization
                </a>
            </div>
        </form>

        <div id="result" class="admin-page-status" aria-live="polite"></div>
    </div>

    <script>

        let web3;
        let contract;

        window.addEventListener('load', async () => {
            if (typeof window.ethereum !== 'undefined') {
                web3 = new Web3(window.ethereum);
                await window.ethereum.request({ method: 'eth_requestAccounts' });
                const accounts = await web3.eth.getAccounts();
                console.log("Connected account:", accounts[0]);

                contract = new web3.eth.Contract(contractABI, contractAddress);

                document.getElementById('checkForm').addEventListener('submit', async (event) => {
                    event.preventDefault();
                    const employeeAddress = document.getElementById('employeeAddress').value;
                    const resultEl = document.getElementById('result');
                    resultEl.className = 'admin-page-status';
                    resultEl.textContent = 'Checking...';
                    try {
                        const isAuthorized = await contract.methods.authorizedEmployees(employeeAddress).call();
                        resultEl.classList.add(isAuthorized ? 'is-success' : 'is-warning');
                        resultEl.innerHTML = `
                            <div><strong>Address:</strong> <span class="contract-address">${employeeAddress}</span></div>
                            <div><strong>Status:</strong> ${isAuthorized ? 'Authorized' : 'Not authorized'}</div>
                        `;
                    } catch (error) {
                        console.error("Error checking authorization:", error);
                        resultEl.classList.add('is-error');
                        resultEl.textContent = 'Failed to check authorization.';
                    }
                });
            } else {
                alert('Please install MetaMask to interact with this application.');
            }
        });
    </script>
</section>