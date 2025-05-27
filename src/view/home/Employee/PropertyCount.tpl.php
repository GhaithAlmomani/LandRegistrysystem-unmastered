<section>
    <h1 class="heading">Employee Portal</h1>

    <style>
        /* Button Container */
        .mb-3 {
            margin-bottom: 1.5rem; /* Space below the button container */
        }

        /* Button Styling */
        #getCount {
            background-color: var(--main-color); /* Button background color */
            color: var(--white); /* Button text color */
            font-size: 1.8rem; /* Button font size */
            padding: 1rem 2rem; /* Padding for the button */
            border-radius: .5rem; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
            transition: background-color 0.3s; /* Smooth transition for background color */
            width: 100%; /* Full width for the button */
        }

        /* Button Hover State */
        #getCount:hover {
            background-color: var(--black); /* Change background color on hover */
        }

        /* Result Paragraph Styling */
        #result {
            font-size: 1.6rem; /* Font size for the result text */
            color: var(--black); /* Text color for the result */
            margin-top: 1rem; /* Space above the result text */
            padding: 1rem; /* Padding inside the result area */
            border-radius: .5rem; /* Rounded corners */
            background-color: var(--light-bg); /* Background color for the result */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
        }

        /* Help Text Styling */
        .form-text {
            font-size: 1.4rem; /* Help text font size */
            color: var(--light-color); /* Help text color */
            margin-top: .5rem; /* Space above help text */
        }
    </style>

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