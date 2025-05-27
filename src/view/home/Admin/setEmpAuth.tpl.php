<section>
    <h1 class="heading">Admin Portal</h1>

<style>
    /* Form Container */
    #authorizationForm {
        background-color: var(--white);
        border-radius: .5rem;
        padding: 2rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin: 2rem auto; /* Center the form */
        max-width: 600px; /* Set a max width for the form */
    }

    /* Form Labels */
    .form-label {
        font-size: 1.8rem; /* Font size for labels */
        color: var(--black); /* Label color */
        margin-bottom: 1rem; /* Space below the label */
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
        margin-bottom: 1.5rem; /* Space below each input */
        transition: border-color 0.3s; /* Smooth transition for border color */
    }

    /* Input Focus State */
    .form-control:focus {
        border-color: var(--main-color); /* Change border color on focus */
        outline: none; /* Remove default outline */
    }

    /* Select Dropdown */
    .form-select {
        width: 100%; /* Full width */
        padding: 1.2rem; /* Padding inside the select */
        font-size: 1.6rem; /* Font size for select text */
        border: var(--border); /* Border style */
        border-radius: .5rem; /* Rounded corners */
        background-color: var(--light-bg); /* Background color */
        color: var(--black); /* Text color */
        margin-bottom: 1.5rem; /* Space below select */
        transition: border-color 0.3s; /* Smooth transition for border color */
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
    }

    /* Button Hover State */
    .btn-primary:hover {
        background-color: var(--black); /* Change background color on hover */
    }

    /* Status Message */
    #status {
        font-size: 1.6rem; /* Status message font size */
        color: var(--light-color); /* Status message color */
        margin-top: 1rem; /* Space above status message */
        text-align: center; /* Center align status message */
    }
</style>
<form id="authorizationForm">
    <h1 class="heading">Set Employee Authorization</h1>


    <div class="mb-3">
        <label for="employeeAddress" class="form-label">Employee Address</label>
        <input type="text" class="form-control" id="employeeAddress" aria-describedby="employeeAddressHelp" required>
        <div id="employeeAddressHelp" class="form-text">Enter The Employee Address</div>
    </div>
    <select id="isAuthorized" class="form-select" aria-label="Default select example" required>
        <option value="true">True</option>
        <option value="false">False</option>
    </select><br><br>

    <!-- <button type="submit">Submit</button> -->
    <button type="submit" class="btn btn-primary">Set Employee Authorization</button>

    <p id="status"></p>
</form>


<!--<p id="status"></p> -->

<script>

    // Initialize Web3
    if (typeof window.ethereum !== 'undefined') {
        const web3 = new Web3(window.ethereum);

        // Prompt user to connect their wallet
        window.ethereum.request({ method: 'eth_requestAccounts' });

        // Get the smart contract
        const contract = new web3.eth.Contract(contractABI, contractAddress);

        // Handle form submission
        document.getElementById('authorizationForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const employeeAddress = document.getElementById('employeeAddress').value;
            const isAuthorized = document.getElementById('isAuthorized').value === 'true';

            try {
                const accounts = await web3.eth.getAccounts();
                const sender = accounts[0];

                // Call the setEmployeeAuthorization function
                await contract.methods.setEmployeeAuthorization(employeeAddress, isAuthorized).send({ from: sender });

                document.getElementById('status').textContent = "Authorization updated successfully!";
            } catch (error) {
                console.error(error);
                document.getElementById('status').textContent = "Error: " + error.message;
            }
        });
    } else {
        document.getElementById('status').textContent = "Please install MetaMask to use this feature.";
    }
</script>
</section>