<?php
require_once __DIR__ . '/../../../../src/middleware/AuthMiddleware.php';
use MVC\middleware\AuthMiddleware;

// Ensure only employees can access this page
AuthMiddleware::requireEmployee();

require_once __DIR__ . '/../../layouts/navbar.tpl.php';
?>

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

    <!-- Custom Alert Styles -->
    <style>
        .custom-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            font-size: 1.6rem;
            color: white;
            z-index: 1000;
            display: none;
            animation: slideIn 0.5s ease-out;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .alert-success {
            background-color: #28a745;
            border-left: 5px solid #1e7e34;
        }

        .alert-error {
            background-color: #dc3545;
            border-left: 5px solid #bd2130;
        }

        .alert-info {
            background-color: #17a2b8;
            border-left: 5px solid #117a8b;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }

        .alert-close {
            margin-left: 15px;
            cursor: pointer;
            font-weight: bold;
            float: right;
        }
    </style>

    <!-- Alert Container -->
    <div id="alertContainer"></div>

    <form id="registerForm">

        <h1 class="heading">Property Registration</h1>

    <div class="mb-3">
        <label for="id" class="form-label">Property ID</label>
        <input type="text" class="form-control" id="id" aria-describedby="idHelp" required>
        <div id="idHelp" class="form-text">Enter the property ID</div>
    </div>

    <div class="mb-3">
        <label for="owner" class="form-label">Owner ID</label>
        <input type="text" class="form-control" id="owner" aria-describedby="ownerHelp" required>
        <div id="ownerHelp" class="form-text">Enter the Owner address</div>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <input type="text" class="form-control" id="description" aria-describedby="descriptionHelp" required>
        <div id="descriptionHelp" class="form-text">Enter a description for the property</div>
    </div>

    <div class="mb-3">
        <label for="latitude" class="form-label">latitude address</label>
        <input type="text" class="form-control" id="latitude" aria-describedby="latitudeHelp" required>
        <div id="latitudeHelp" class="form-text">Enter the property latitude</div>
    </div>

    <div class="mb-3">
        <label for="longitude" class="form-label">longitude address</label>
        <input type="text" class="form-control" id="longitude" aria-describedby="longitudeHelp" required>
        <div id="longitudeHelp" class="form-text">Enter the property longitude</div>
    </div>

    <button type="button" class="btn btn-primary" onclick="registerProperty()">Register Property</button>
</form>

<script>

        let web3;
        let contract;

        // Initialize web3
        if (typeof window.ethereum !== 'undefined') {
            web3 = new Web3(window.ethereum);
            window.ethereum.enable();
        } else {
            showAlert('Please install MetaMask to use this feature.', 'error');
        }

        // Initialize contract
        contract = new web3.eth.Contract(contractABI, contractAddress);

        // Custom Alert Function
        function showAlert(message, type = 'info') {
            const alertContainer = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = `custom-alert alert-${type}`;
            alert.innerHTML = `
                <span>${message}</span>
                <span class="alert-close" onclick="this.parentElement.remove()">&times;</span>
            `;
            alertContainer.appendChild(alert);
            alert.style.display = 'block';

            // Auto remove after 5 seconds
            setTimeout(() => {
                alert.style.animation = 'fadeOut 0.5s ease-out';
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 5000);
        }

        // Register property function
        async function registerProperty() {
            const id = document.getElementById('id').value;
            const owner = document.getElementById('owner').value;
            const description = document.getElementById('description').value;
            const latitude = document.getElementById('latitude').value;
            const longitude = document.getElementById('longitude').value;

            if (!id || !owner || !description || !latitude || !longitude) {
                showAlert('Please fill in all fields', 'error');
                return;
            }

            try {
                const accounts = await web3.eth.getAccounts();
                
                showAlert('Processing transaction...', 'info');

                contract.methods.registerProperty(id, owner, description, latitude, longitude)
                .send({ from: accounts[0] })
                .on('transactionHash', function(hash) {
                    showAlert(`Transaction sent! Hash: ${hash.substring(0, 10)}...`, 'info');
                })
                .on('receipt', function(receipt) {
                    showAlert('Property registered successfully!', 'success');
                    console.log(receipt);
                    // Clear form
                    document.getElementById('registerForm').reset();
                })
                .on('error', function(error) {
                    showAlert(`Error: ${error.message}`, 'error');
                    console.error(error);
                });
            } catch (error) {
                showAlert(`Error: ${error.message}`, 'error');
                console.error(error);
            }
        }
</script>

</section>