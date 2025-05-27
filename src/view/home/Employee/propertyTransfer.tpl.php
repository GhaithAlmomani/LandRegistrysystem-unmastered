<section>
    <h1 class="heading">Employee Portal</h1>


    <style>

    /* Form Container */
    #registerForm {
        background-color: var(--white);
        border-radius: .5rem;
        padding: 2rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin: 2rem auto; /* Center the form */
        max-width: 600px; /* Set a max width for the form */
    }

    /* Form Labels */
    .form-label {
        font-size: 1.8rem;
        color: var(--black);
        margin-bottom: 1rem; /* Space below the label */
        display: block; /* Ensure labels are block elements */
    }

    /* Form Inputs */
    .form-control {
        width: 100%;
        padding: 1.2rem; /* Padding inside the input */
        font-size: 1.6rem; /* Font size for input text */
        border: var(--border);
        border-radius: .5rem;
        background-color: var(--light-bg);
        color: var(--black);
        margin-bottom: 1.5rem; /* Space below each input */
        transition: border-color 0.3s; /* Smooth transition for border color */
    }

    /* Input Focus State */
    .form-control:focus {
        border-color: var(--main-color); /* Change border color on focus */
        outline: none; /* Remove default outline */
    }

    /* Help Text */
    .form-text {
        font-size: 1.4rem;
        color: var(--light-color);
        margin-top: .5rem; /* Space above help text */
    }

    /* Button Styling */
    .btn-primary {
        background-color: var(--main-color);
        color: var(--white);
        font-size: 1.8rem;
        padding: 1rem 2rem; /* Padding for the button */
        border-radius: .5rem;
        cursor: pointer;
        transition: background-color 0.3s; /* Smooth transition for background color */
    }

    /* Button Hover State */
    .btn-primary:hover {
        background-color: var(--black); /* Change background color on hover */
    }
    /* Form Container */
    #transferOwnershipForm {
        background-color: var(--white);
        border-radius: .5rem;
        padding: 2rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin: 2rem auto; /* Center the form */
        max-width: 600px; /* Set a max width for the form */
    }

    /* Form Labels */
    .form-label {
        font-size: 1.8rem;
        color: var(--black);
        margin-bottom: 1rem; /* Space below the label */
        display: block; /* Ensure labels are block elements */
    }

    /* Form Inputs */
    .form-control {
        width: 100%;
        padding: 1.2rem; /* Padding inside the input */
        font-size: 1.6rem; /* Font size for input text */
        border: var(--border);
        border-radius: .5rem;
        background-color: var(--light-bg);
        color: var(--black);
        margin-bottom: 1.5rem; /* Space below each input */
        transition: border-color 0.3s; /* Smooth transition for border color */
    }

    /* Input Focus State */
    .form-control:focus {
        border-color: var(--main-color); /* Change border color on focus */
        outline: none; /* Remove default outline */
    }

    /* Help Text */
    .form-text {
        font-size: 1.4rem;
        color: var(--light-color);
        margin-top: .5rem; /* Space above help text */
    }

    /* Button Styling */
    .btn-primary {
        background-color: var(--main-color);
        color: var(--white);
        font-size: 1.8rem;
        padding: 1rem 2rem; /* Padding for the button */
        border-radius: .5rem;
        cursor: pointer;
        transition: background-color 0.3s; /* Smooth transition for background color */
    }

    /* Button Hover State */
    .btn-primary:hover {
        background-color: var(--black); /* Change background color on hover */
    }
</style>
<form id="transferOwnershipForm">

    <h1 class="heading">Property Transfer</h1>


    <div class="mb-3">
        <label for="propertyId" class="form-label">Property ID</label>
        <input type="text" class="form-control" id="propertyId" aria-describedby="propertyIdHelp" required>
        <div id="propertyIdHelp" class="form-text">Enter The ID for the property</div>
    </div>

    <div class="mb-3">
        <label for="newOwner" class="form-label">New Owner Address</label>
        <input type="text" class="form-control" id="newOwner" aria-describedby="newOwnerHelp" required>
        <div id="newOwnerdHelp" class="form-text">Enter The new owner ID</div>
    </div>

    <div class="mb-3">
        <label for="perviousOwner" class="form-label">Previous Owner Address</label>
        <input type="text" class="form-control" id="previousOwner" aria-describedby="previousOwnerHelp" required>
        <div id="previousOwnerdHelp" class="form-text">Enter The previous owner ID</div>
    </div>

    <button type="submit" class="btn btn-primary">Transfer Ownership</button>


    <p id="status"></p>

</form>

<!--<p id="status"></p> -->

<script>
    let web3;
    let contract;
    window.addEventListener('load', async () => {
        if (window.ethereum) {
            web3 = new Web3(window.ethereum);
            await window.ethereum.request({ method: 'eth_requestAccounts' });

            const accounts = await web3.eth.getAccounts();
            const account = accounts[0];

            contract = new web3.eth.Contract(contractABI, contractAddress);
            document.getElementById("status").innerText = "Connected as: " + account;
        } else {
            document.getElementById("status").innerText = "Please install MetaMask!";
        }
    });
    document.getElementById('transferOwnershipForm').addEventListener('submit', async (event) => {
        event.preventDefault();

        const propertyId = document.getElementById('propertyId').value;
        const newOwner = document.getElementById('newOwner').value;
        const previousOwner = document.getElementById('previousOwner').value;

        try {
            const accounts = await web3.eth.getAccounts();
            await contract.methods.transferOwnership(
                propertyId,
                previousOwner,
                newOwner
            ).send({ from: accounts[0] });

            document.getElementById("status").innerText = "Ownership transferred successfully!";
        } catch (error) {
            console.error(error);
            document.getElementById("status").innerText = "Error during ownership transfer.";
        }
    });
    // Optional: Additional event listeners for property registration or authorization management
    document.getElementById('registerPropertyForm')?.addEventListener('submit', async (event) => {
        event.preventDefault();

        const propertyId = document.getElementById('propertyId').value;
        const owner = document.getElementById('owner').value;
        const description = document.getElementById('description').value;
        const latitude = document.getElementById('latitude').value;
        const longitude = document.getElementById('longitude').value;

        try {
            const accounts = await web3.eth.getAccounts();
            await contract.methods.registerProperty(
                propertyId,
                owner,
                description,
                latitude,
                longitude
            ).send({ from: accounts[0] });

            document.getElementById("status").innerText = "Property registered successfully!";
        } catch (error) {
            console.error(error);
            document.getElementById("status").innerText = "Error during property registration.";
        }
    });
</script>
</section>