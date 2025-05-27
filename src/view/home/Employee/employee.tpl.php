
<section class="property-management">
    <!-- Employee Management Section -->
    <!--<h1 class="heading">Employee Management</h1>
    <div class="box-container">
        <div class="box">

            <h3 class="title">Authorize Employee</h3>
            <form class="flex" action="" method="post">
                <input type="text" id="employeeAddress" placeholder="Employee Address" class="box">
                <select id="authorizationStatus" class="box">
                    <option value="true">Authorize</option>
                    <option value="false">Revoke</option>
                </select>
                <input id="UpdateAuthorization" type="submit" value="Update Authorization" class="btn">
                <button class="btn btn-primary" id="UpdateAuthorization">Vote</button>
            </form>
        </div>
    </div>

    <!-- Property Registration Section -->
    <h1 class="heading">Property Registration</h1>
    <div class="box-container">
        <div class="box">
            <h3 class="title">Register New Property</h3>
            <form class="flex">
                <input type="number" id="propertyId" placeholder="Property ID" class="box">
                <input type="text" id="ownerAddress" placeholder="Owner Address" class="box">
                <input type="text" id="propertyDescription" placeholder="Property Description" class="box">
                <input type="submit" value="Register Property" class="btn">
            </form>
        </div>
    </div>

    <!-- Property Transfer Section -->
    <h1 class="heading">Property Transfer</h1>
    <div class="box-container">
        <div class="box">
            <h3 class="title">Transfer Property</h3>
            <form class="flex">
                <input type="number" id="transferPropertyId" placeholder="Property ID" class="box">
                <input type="text" id="newOwnerAddress" placeholder="New Owner Address" class="box">
                <input type="submit" value="Transfer Property" class="btn">
            </form>
        </div>
    </div>

    <!-- Property List Section -->
    <h1 class="heading">Property List</h1>
    <div class="table-container">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Owner</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody id="propertyList">
            <!-- Property list will be populated here -->
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script>
</section>
<script>

    // Initialize Web3 provider (MetaMask or Infura)
    const web3 = new Web3(window.ethereum);

    // Initialize the contract
    const contract = new web3.eth.Contract(contractABI, contractAddress);
    document.getElementById("UpdateAuthorization").addEventListener("click", async () => {
        try {

            // Request user accounts from MetaMask (this will prompt MetaMask to open if not connected)
            const accounts = await ethereum.request({ method: 'eth_requestAccounts' });
            if (accounts.length === 0) {
                alert('No accounts found. Please log in to your wallet.');
                return;
            }
            const userAddress = accounts[0];  // Get the first account

            const publicAdd = document.getElementById('employeeAddress').value;
            const auth = document.getElementById('authorizationStatus').value;
//
            // Send vote transaction to the smart contract
            await contract.methods.setEmployeeAuthorization(publicAdd,auth).send({ from: userAddress });

            alert(Successfully add  !);

            // Optionally refresh the chart or update the UI dynamically
        } catch (error) {
            console.error("Error while voting:", error.message || error);
            alert("Voting failed. Make sure you are connected to the correct network.");
        }
    });

</script>




