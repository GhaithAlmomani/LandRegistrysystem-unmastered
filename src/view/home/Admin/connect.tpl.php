
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MetaMask Connect Button</title>
    <style>
        :root{
            --main-color:#00560f;
            --red:#e74c3c;
            --orange:#f39c12;
            --light-color:#888;
            --light-bg:#eee;
            --black:#2c3e50;
            --white:#fff;
            --border:.1rem solid rgba(0,0,0,.2);
        }

        * {
            font-family: 'Nunito', sans-serif;
            margin:0;
            padding:0;
            box-sizing: border-box;
        }

        .wallet-container {
            background-color: var(--white);
            border-radius: .5rem;
            padding: 2rem;
            max-width: 50rem;
            margin: 2rem auto;
        }

        .connect-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            width: 100%;
            background-color: var(--main-color);
            color: var(--white);
            font-size: 1.8rem;
            padding: 1.5rem 3rem;
            cursor: pointer;
            text-transform: capitalize;
            border-radius: .5rem;
            border: none;
            transition: .2s linear;
        }

        .connect-button:hover {
            background-color: var(--black);
            transform: translateY(-2px);
        }

        .metamask-logo {
            width: 2.4rem;
            height: 2.4rem;
        }

        #status {
            margin-top: 2rem;
            font-size: 1.7rem;
            color: var(--light-color);
            text-align: center;
        }

        .connected {
            background-color: var(--black) !important;
        }
    </style>
</head>
<body>
<div class="wallet-container">
    <button class="connect-button" onclick="connectWallet()">
        <svg class="metamask-logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 318.6 318.6">
            <path fill="#E2761B" stroke="#E2761B" stroke-linecap="round" stroke-linejoin="round" d="M274.1 35.5l-99.5 73.9L193 51.6z"/>
            <path fill="#E4761B" stroke="#E4761B" stroke-linecap="round" stroke-linejoin="round" d="M44.4 35.5l98.7 74.6-17.5-58.3zm193.9 171.3l-26.5 40.6 56.7 15.6 16.3-55.3zm-204.4.9L50.1 263l56.7-15.6-26.5-40.6z"/>
        </svg>
        Connect MetaMask
    </button>
    <div id="status"></div>
</div>

<script>
    async function connectWallet() {
        const status = document.getElementById('status');
        const button = document.querySelector('.connect-button');

        if (typeof window.ethereum !== 'undefined') {
            try {
                const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                const account = accounts[0];
                status.innerHTML = `Connected: ${account.substring(0, 6)}...${account.substring(38)}`;
                button.classList.add('connected');
                button.innerHTML = `
                        <svg class="metamask-logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 318.6 318.6">
                            <path fill="#E2761B" stroke="#E2761B" stroke-linecap="round" stroke-linejoin="round" d="M274.1 35.5l-99.5 73.9L193 51.6z"/>
                            <path fill="#E4761B" stroke="#E4761B" stroke-linecap="round" stroke-linejoin="round" d="M44.4 35.5l98.7 74.6-17.5-58.3zm193.9 171.3l-26.5 40.6 56.7 15.6 16.3-55.3zm-204.4.9L50.1 263l56.7-15.6-26.5-40.6z"/>
                        </svg>
                        Connected
                    `;
            } catch (error) {
                status.innerHTML = 'Error connecting to MetaMask';
            }
        } else {
            status.innerHTML = 'Please install MetaMask';
            window.open('https://metamask.io/download.html', '_blank');
        }
    }
</script>
</body>
</html>




