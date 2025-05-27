<section>
    <h1 class="heading">Employee Portal</h1>

    <style>
        /* Form Container */
        .mb-3 {
            margin-bottom: 3rem; /* Consistent margin for spacing */
        }

        /* Form Label */
        .form-label {
            font-size: 1.8rem; /* Font size consistent with existing styles */
            color: var(--black); /* Text color */
            margin-bottom: 1rem; /* Space below the label */
            display: block; /* Block display for proper spacing */
        }

        /* Form Control */
        .form-control {
            width: 100%; /* Full width */
            padding: 1.4rem; /* Padding for input */
            font-size: 1.6rem; /* Font size */
            border-radius: .5rem; /* Rounded corners */
            border: var(--border); /* Border style */
            background-color: var(--light-bg); /* Background color */
            color: var(--black); /* Text color */
            margin-bottom: 1rem; /* Space below input */
        }

        /* Form Text */
        .form-text {
            font-size: 1.4rem; /* Font size for help text */
            color: var(--light-color); /* Color for help text */
        }

        /* Button */
        .btn-primary {
            background-color: var(--main-color); /* Primary button color */
            color: #fff; /* Text color */
            font-size: 1.8rem; /* Font size */
            padding: 1rem 3rem; /* Padding for button */
            border-radius: .5rem; /* Rounded corners */
            cursor: pointer; /* Pointer cursor */
            text-transform: capitalize; /* Capitalize text */
            display: inline-block; /* Inline block for button */
        }

        /* Button Hover Effect */
        .btn-primary:hover {
            background-color: var(--black); /* Change background on hover */
            color: var(--white); /* Change text color on hover */
        }

        /* Form Heading */
        .form-heading {
            font-size: 2.5rem; /* Font size for heading */
            color: var(--black); /* Text color */
            margin: 2.5rem 0; /* Margin for spacing */
            text-transform: capitalize; /* Capitalize text */
        }

        /* Property Details Section */
        #propertyDetails {
            background-color: var(--white); /* Background color */
            border-radius: .5rem; /* Rounded corners */
            padding: 2rem; /* Padding for details */
            margin-top: 2rem; /* Space above details */
        }

        /* Property Details Paragraph */
        #propertyDetails p {
            font-size: 1.6rem; /* Font size for details */
            color: var(--black); /* Text color */
            margin: 1rem 0; /* Margin for spacing */
        }

        /* Strong Text in Property Details */
        #propertyDetails strong {
            color: var(--main-color); /* Color for strong text */
        }
    </style>

    <div class="mb-3">
        <h3 class="head">Get Property Info</h3>

        <form id="propertyForm">
            <label for="propertyId" class="form-label">Property ID:</label>
            <input type="number" class="form-control" id="propertyId" name="propertyId" required aria-describedby="propertyIdHelp">
            <div id="propertyIdHelp" class="form-text">Enter the ID of the property you want to fetch.</div>
            <button type="submit" class="btn btn-primary">Get Info</button>
        </form>
    </div>

    <h2 class="form-heading">Property Details</h2>
    <div id="propertyDetails" class="mb-3">
        <p><strong>ID:</strong> <span id="propertyIdDisplay"></span></p>
        <p><strong>Owner:</strong> <span id="owner"></span></p>
        <p><strong>Description:</strong> <span id="description"></span></p>
        <p><strong>Latitude:</strong> <span id="latitude"></span></p>
        <p><strong>Longitude:</strong> <span id="longitude"></span></p>
        <p><strong>Authorized for Transfer:</strong> <span id="isAuthorized"></span></p>
    </div>
    <script>

        // Initialize Web3
        let web3;
        if (window.ethereum) {
            web3 = new Web3(window.ethereum);
            window.ethereum.request({ method: 'eth_requestAccounts' });
        } else {
            alert('Please install MetaMask to use this feature.');
        }

        const contract = new web3.eth.Contract(contractABI, contractAddress);

        document.getElementById('propertyForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const propertyId = document.getElementById('propertyId').value;

            try {
                const result = await contract.methods.getPropertyInfo(propertyId).call();

                // Display the property details
                document.getElementById('propertyIdDisplay').innerText = result.id;
                document.getElementById('owner').innerText = result.owner;
                document.getElementById('description').innerText = result.description;
                document.getElementById('latitude').innerText = result.latitude;
                document.getElementById('longitude').innerText = result.longitude;
                document.getElementById('isAuthorized').innerText = result.isAuthorizedForTransfer ? 'Yes' : 'No';
            } catch (error) {
                console.error('Error fetching property info:', error);
                alert('Failed to fetch property information. Please check the console for details.');
            }
        });
    </script>
</section>
