<style>
    /* Container for Admin Address Section */
    container {
        display: block; /* Ensure the container behaves like a block element */
        background-color: var(--white); /* Background color for the container */
        border-radius: .5rem; /* Rounded corners */
        padding: 2rem; /* Padding inside the container */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
        margin: 2rem auto; /* Center the container */
        max-width: 600px; /* Set a max width for the container */
        text-align: center; /* Center align text within the container */
    }

    /* Subheading */
    .head {
        font-size: 2rem; /* Font size for the subheading */
        color: var(--black); /* Color for the subheading */
        margin-bottom: 1.5rem; /* Space below the subheading */
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
        margin-bottom: 1.5rem; /* Space below the button */
    }

    /* Button Hover State */
    .btn-primary:hover {
        background-color: var(--black); /* Change background color on hover */
    }

    /* Admin Address Display */
    h5 {
        font-size: 1.6rem; /* Font size for the address display */
        color: var(--black); /* Color for the address text */
        margin-top: 1rem; /* Space above the address display */
    }

    /* Admin Address Span */
    #adminAddress {
        font-weight: bold; /* Bold text for the admin address */
        color: var(--main-color); /* Color for the admin address */
    }
</style>

<section>
    <h1 class="heading">Admin Portal</h1>

    <container>
        <h3 class="head">Admin Address</h3>
        <button class="btn btn-primary" id="getAdminButton">Get Admin</button>
        <h5>Admin Address: <span id="adminAddress">N/A</span></h5>
    </container>

<script>
// get admin line 24
    document.addEventListener("DOMContentLoaded", async () => {
        // Check if MetaMask is available
        if (typeof window.ethereum !== "undefined") {
            const web3 = new Web3(window.ethereum);

            // Request account access if needed
            try {
                await window.ethereum.request({ method: "eth_requestAccounts" });

                // Create the contract instance
                const contract = new web3.eth.Contract(contractABI, contractAddress);

                // Set up the button click listener
                document.getElementById("getAdminButton").addEventListener("click", async () => {
                    try {
                        // Call the admin function from the contract
                        const adminAddress = await contract.methods.admin().call();
                        // Display the admin address in the UI + (return whatever you want as a "call" after the mothod)
                        document.getElementById("adminAddress").textContent = adminAddress;
                    } catch (error) {
                        console.error("Error fetching admin address:", error);
                        alert("Failed to fetch admin address. Check the console for details.");
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