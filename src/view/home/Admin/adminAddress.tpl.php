<section>
    <h1 class="heading">Governance</h1>

    <div class="card admin-page-card">
        <h3 class="title">Verify Admin Address</h3>
        <p class="tutor">Read the current on-chain admin address for audit and security checks.</p>

        <div class="admin-page-actions">
            <button class="inline-btn" id="getAdminButton" type="button">
                <i class="fa-solid fa-fingerprint" aria-hidden="true"></i>
                Fetch Admin Address
            </button>
            <a class="inline-btn" href="connect">
                <i class="fa-brands fa-ethereum" aria-hidden="true"></i>
                Wallet Connection
            </a>
        </div>

        <div class="admin-page-status" id="adminStatus" aria-live="polite">
            Admin Address:
            <span id="adminAddress" class="contract-address">N/A</span>
        </div>
    </div>

<script>
// get admin line 24
    document.addEventListener("DOMContentLoaded", async () => {
        // Check if MetaMask is available
        if (typeof window.ethereum !== "undefined") {
            const web3 = new Web3(window.ethereum);

            // Request account access if needed
            try {
                await window.ethereum.request({ method: "eth_requestAccounts" });

                const statusEl = document.getElementById('adminStatus');
                const setStatus = (msg, kind) => {
                    statusEl.className = 'admin-page-status' + (kind ? ` ${kind}` : '');
                    statusEl.innerHTML = msg;
                };

                // Basic diagnostics: network + contract code
                let chainId = null;
                try {
                    chainId = await web3.eth.getChainId();
                } catch (e) {}

                // Sepolia chainId = 11155111
                const expectedChainId = 11155111;
                if (chainId !== null && chainId !== expectedChainId) {
                    setStatus(
                        `Wrong network detected. Please switch MetaMask to <strong>Sepolia</strong>.<br>` +
                        `Current chainId: <span class="contract-address">${chainId}</span>`,
                        'is-warning'
                    );
                }

                const code = await web3.eth.getCode(contractAddress);
                if (!code || code === '0x') {
                    setStatus(
                        `No contract found at:<br><span class="contract-address">${contractAddress}</span><br>` +
                        `This usually means <strong>wrong network</strong> or <strong>wrong contract address</strong>.`,
                        'is-error'
                    );
                    return;
                }

                // Create the contract instance (ABI must match deployed contract)
                const contract = new web3.eth.Contract(contractABI, contractAddress);

                // Set up the button click listener
                document.getElementById("getAdminButton").addEventListener("click", async () => {
                    try {
                        // Call the admin function from the contract
                        const adminAddress = await contract.methods.admin().call();
                        // Display the admin address in the UI + (return whatever you want as a "call" after the mothod)
                        document.getElementById("adminAddress").textContent = adminAddress;
                        setStatus(
                            `Admin Address: <span id="adminAddress" class="contract-address">${adminAddress}</span>`,
                            'is-success'
                        );
                    } catch (error) {
                        console.error("Error fetching admin address:", error);
                        setStatus(
                            `Failed to read admin address.<br>` +
                            `Common causes: <strong>ABI mismatch</strong> or <strong>wrong contract address</strong>.<br>` +
                            `Contract: <span class="contract-address">${contractAddress}</span>`,
                            'is-error'
                        );
                    }
                });
            } catch (error) {
                console.error("User denied account access:", error);
                alert("Please allow MetaMask access to use this feature.");
            }
        } else {
            alert("MetaMask is not detected. Please install MetaMask to use this feature.");
        }
    });
</script>
</section>