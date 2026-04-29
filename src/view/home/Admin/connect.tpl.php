
<section class="admin-page">
    <h1 class="heading">Wallet Connection</h1>

    <div class="wallet-connect card admin-page-card">
        <h3 class="title">MetaMask</h3>
        <p class="tutor">Connect an authorized wallet to perform administrative on-chain actions.</p>

        <button type="button" class="inline-btn connect-button" onclick="connectWallet()">
        <svg class="metamask-logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 318.6 318.6" aria-hidden="true">
            <path fill="#E2761B" stroke="#E2761B" stroke-linecap="round" stroke-linejoin="round" d="M274.1 35.5l-99.5 73.9L193 51.6z"/>
            <path fill="#E4761B" stroke="#E4761B" stroke-linecap="round" stroke-linejoin="round" d="M44.4 35.5l98.7 74.6-17.5-58.3zm193.9 171.3l-26.5 40.6 56.7 15.6 16.3-55.3zm-204.4.9L50.1 263l56.7-15.6-26.5-40.6z"/>
        </svg>
        Connect MetaMask
    </button>
        <div id="status" class="admin-page-status" aria-live="polite"></div>
    </div>
</section>

<script>
    async function connectWallet() {
        const status = document.getElementById('status');
        const button = document.querySelector('.connect-button');

        if (typeof window.ethereum !== 'undefined') {
            try {
                const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                const account = accounts[0];
                status.className = 'admin-page-status is-success';
                status.innerHTML = 'Connected: <span class="contract-address">' + account.substring(0, 6) + '...' + account.substring(38) + '</span>';
                button.classList.add('connected');
                button.innerHTML = `
                        <svg class="metamask-logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 318.6 318.6">
                            <path fill="#E2761B" stroke="#E4761B" stroke-linecap="round" stroke-linejoin="round" d="M274.1 35.5l-99.5 73.9L193 51.6z"/>
                            <path fill="#E4761B" stroke="#E4761B" stroke-linecap="round" stroke-linejoin="round" d="M44.4 35.5l98.7 74.6-17.5-58.3zm193.9 171.3l-26.5 40.6 56.7 15.6 16.3-55.3zm-204.4.9L50.1 263l56.7-15.6-26.5-40.6z"/>
                        </svg>
                        Connected
                    `;
            } catch (error) {
                status.className = 'admin-page-status is-error';
                status.innerHTML = 'Error connecting to MetaMask';
            }
        } else {
            status.className = 'admin-page-status is-warning';
            status.innerHTML = 'Please install MetaMask';
            window.open('https://metamask.io/download.html', '_blank');
        }
    }
</script>
