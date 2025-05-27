<section>
    <h1 class="heading">Admin Portal</h1>
    <style>
        /* Form Container */
        #checkForm {
            background-color: var(--white); /* Background color for the form */
            border-radius: .5rem; /* Rounded corners */
            padding: 2rem; /* Padding inside the form */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
            margin: 2rem auto; /* Center the form */
            max-width: 400px; /* Set a max width for the form */
        }

        /* Form Labels */
        .form-label {
            font-size: 1.8rem; /* Font size for labels */
            color: var(--black); /* Color for labels */
            margin-bottom: .5rem; /* Space below the label */
            display: block; /* Ensure labels are block elements */
        }

        /* Form Inputs */
        .form-control {
            width: 100%; /* Full width */
            padding: 1.2rem; /* Padding inside the input */
            font-size: 1.6rem; /* Font size for input text */
            border: var(--border); /* Border style */
            border-radius: .5rem; /* Rounded corners */
            background-color: var(--light-bg); /* Background color */
            color: var(--black); /* Text color */
            margin-bottom: 1.5rem; /* Space below the input */
            transition: border-color 0.3s; /* Smooth transition for border color */
        }

        /* Input Focus State */
        .form-control:focus {
            border-color: var(--main-color); /* Change border color on focus */
            outline: none; /* Remove default outline */
        }

        /* Help Text */
        .form-text {
            font-size: 1.4rem; /* Help text font size */
            color: var(--light-color); /* Help text color */
            margin-top: .5rem; /* Space above help text */
        }

        /* Button Styling */
        .btn-primary {
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
        .btn-primary:hover {
            background-color: var(--black); /* Change background color on hover */
        }

        /* Result Display */
        #result {
            margin-top: 2rem; /* Space above the result display */
            padding: 1.5rem; /* Padding inside the result display */
            border-radius: .5rem; /* Rounded corners */
            background-color: var(--light-bg); /* Background color for the result */
            color: var(--black); /* Text color for the result */
            font-size: 1.6rem; /* Font size for the result text */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
        }
    </style>

    <form id="checkForm">
        <h1 class="heading">Check Employee Authorization</h1>

        <div class="mb-3">
            <label for="employeeAddress" class="form-label">Employee Address:</label>
            <input type="text" class="form-control" id="employeeAddress" aria-describedby="employeeAddressHelp" required>
            <div id="employeeAddressHelp" class="form-text">Enter the employee address</div>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Check Authorization</button>
        </div>
    </form>

    <div id="result"></div>

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
                    try {
                        const isAuthorized = await contract.methods.authorizedEmployees(employeeAddress).call();
                        document.getElementById('result').textContent = `Authorization Status for: ${employeeAddress}: ${isAuthorized}`;
                    } catch (error) {
                        console.error("Error checking authorization:", error);
                        document.getElementById('result').textContent = 'Failed to check authorization.';
                    }
                });
            } else {
                alert('Please install MetaMask to interact with this application.');
            }
        });
    </script>
</section>