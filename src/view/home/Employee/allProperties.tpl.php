<section>
    <h1 class="heading">Employee Portal</h1>



    <div class="mb-3">

        <h1 class="head">Registered Properties</h1>

        <button id="fetchProperties" class="btn btn-primary">Get All Properties</button>
    </div>
    <table id="propertiesTable">
        <thead>
        <tr>
            <th>ID</th>
            <th>Owner</th>
            <th>Description</th>
            <th>Latitude</th>
            <th>Longitude</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script>

        async function loadWeb3() {
            if (window.ethereum) {
                window.web3 = new Web3(window.ethereum);
                await window.ethereum.request({ method: 'eth_requestAccounts' });
            } else {
                alert('Please install MetaMask!');
            }
        }

        async function getAllProperties() {
            const web3 = window.web3;
            const contract = new web3.eth.Contract(contractABI, contractAddress);
            try {
                const properties = await contract.methods.getAllProperties().call();
                const tableBody = document.querySelector("#propertiesTable tbody");
                tableBody.innerHTML = "";

                properties.forEach(property => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${property.id}</td>
                        <td>${property.owner}</td>
                        <td>${property.description}</td>
                        <td>${property.latitude}</td>
                        <td>${property.longitude}</td>
                    `;
                    tableBody.appendChild(row);
                });
            } catch (error) {
                console.error("Error fetching properties:", error);
            }
        }

        document.getElementById("fetchProperties").addEventListener("click", getAllProperties);

        // Load Web3 on page load
        window.addEventListener("load", loadWeb3);
    </script>
</section>
