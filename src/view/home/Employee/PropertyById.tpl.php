<section>
    <h1 class="heading">Employee Portal</h1>

    <style>
        /* Container Styling */
        .container {
            max-width: 700px; /* Increased max width for the container */
            margin: 3rem auto; /* Center the container with more margin */
            padding: 3rem; /* Increased padding inside the container */
            background-color: var(--white); /* Background color for the container */
            border-radius: .5rem; /* Rounded corners */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Increased shadow for depth */
        }

        /* Heading Styling */
        .form-heading {
            font-size: 3rem; /* Increased font size for the heading */
            color: var(--black); /* Color for the heading */
            margin-bottom: 2rem; /* Increased space below the heading */
            text-align: center; /* Center align the heading */
        }

        /* Form Group Styling */
        .mb-3 {
            margin-bottom: 2rem; /* Increased space below each form group */
        }

        /* Label Styling */
        .form-label {
            font-size: 2rem; /* Increased font size for labels */
            color: var(--black); /* Color for labels */
            margin-bottom: .75rem; /* Increased space below the label */
            display: block; /* Ensure labels are block elements */
        }

        /* Input Styling */
        .form-control {
            width: 100%; /* Full width */
            padding: 1.5rem; /* Increased padding inside the input */
            font-size: 1.8rem; /* Increased font size for input text */
            border: var(--border); /* Border style */
            border-radius: .5rem; /* Rounded corners */
            background-color: var(--light-bg); /* Background color */
            color: var(--black); /* Text color */
            margin-bottom: .75rem; /* Increased space below the input */
            transition: border-color 0.3s; /* Smooth transition for border color */
        }

        /* Input Focus State */
        .form-control:focus {
            border-color: var(--main-color); /* Change border color on focus */
            outline: none; /* Remove default outline */
        }

        /* Help Text */
        .form-text {
            font-size: 1.6rem; /* Increased help text font size */
            color: var(--light-color); /* Help text color */
            margin-top: .5rem; /* Space above help text */
        }

        /* Button Styling */
        .btn-primary {
            background-color: var(--main-color); /* Button background color */
            color: var(--white); /* Button text color */
            font-size: 2rem; /* Increased button font size */
            padding: 1.5rem 2.5rem; /* Increased padding for the button */
            border-radius: .5rem; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
            transition: background-color 0.3s; /* Smooth transition for background color */
            width: 100%; /* Full width for the button */
        }

        /* Button Hover State */
        .btn-primary:hover {
            background-color: var(--black); /* Change background color on hover */
        }

        /* Output Styling */
        .output {
            margin-top: 2rem; /* Space above the output area */
            padding: 1.5rem; /* Padding inside the output area */
            border-radius: .5rem; /* Rounded corners */
            background-color: var(--light-bg); /* Background color for the output */
            color: var(--black); /* Text color for the output */
            font-size: 1.6rem; /* Font size for the output text */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
        }
    </style>

    <div class="container">
        <div class="mb-3">
            <h1 class="form-heading">Get Property By ID</h1>
            <label for="propertyId" class="form-label">Enter Property ID:</label>
            <input type="number" class="form-control" id="propertyId" placeholder="Property ID" aria-describedby="propertyIdHelp" required>
            <div id="propertyIdHelp" class="form-text">Enter the ID of the property you want to fetch.</div>
        </div>

        <div class="mb-3">
            <button class="btn btn-primary" onclick="getPropertyById()">Fetch Property</button>
        </div>

        <div class="output" id="output"></div>
    </div>

    <script>

        let web3;
        let contract;

        // Initialize web3
        window.addEventListener('load', async () => {
            if (window.ethereum) {
                web3 = new Web3(window.ethereum);
                await window.ethereum.request({ method: 'eth_requestAccounts' });
                contract = new web3.eth.Contract(contractABI, contractAddress);
            } else {
                alert("Please install MetaMask to use this feature.");
            }
        });

        async function getPropertyById() {
            const propertyId = document.getElementById("propertyId").value;
            const output = document.getElementById("output");

            if (!propertyId) {
                output.textContent = "Please enter a property ID.";
                return;
            }

            try {
                const property = await contract.methods.getPropertyById(propertyId).call();
                output.innerHTML = `
                    <p><strong>ID:</strong> ${property[0]}</p>
                    <p><strong>Owner:</strong> ${property[1]}</p>
                    <p><strong>Description:</strong> ${property[2]}</p>
                    <p><strong>Latitude:</strong> ${property[3]}</p>
                    <p><strong>Longitude:</strong> ${property[4]}</p>
                `;
            } catch (error) {
                output.textContent = `Error fetching property: ${error.message}`;
            }
        }
    </script>
</section>