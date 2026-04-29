<section>
    <h1 class="heading">Employee Portal</h1>

    <div>

        <h3 class="head">Property Count</h3>

        <div class="mb-3">
            <button id="getCount" class="btn btn-primary">Get Property Count</button>
        </div>

        <div class="mb-3">
            <p id="result" class="form-text">Property Count will appear here...</p>
        </div>
    </div>
    <script>

        let web3;
        let contract;

        // Initialize Web3
        window.addEventListener('load', async () => {
            if (window.ethereum) {
                web3 = new Web3(window.ethereum);
                try {
                    // Request account access if needed
                    await window.ethereum.request({ method: 'eth_requestAccounts' });
                    console.log('Connected to MetaMask');

                    // Initialize contract
                    contract = new web3.eth.Contract(contractABI, contractAddress);
                } catch (error) {
                    console.error('User denied account access', error);
                }
            } else {
                alert('Please install MetaMask!');
            }
        });

        // Function to get property count
        async function getPropertyCount() {
            try {
                const count = await contract.methods.getPropertyCount().call();
                document.getElementById('result').textContent = `Property Count: ${count}`;
            } catch (error) {
                console.error('Error fetching property count:', error);
                document.getElementById('result').textContent = 'Error fetching property count. Check the console for details.';
            }
        }

        // Attach event listener to the button
        document.getElementById('getCount').addEventListener('click', getPropertyCount);
    </script>
</section>