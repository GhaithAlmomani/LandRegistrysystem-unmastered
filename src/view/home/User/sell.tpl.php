<style>
    .sell-button {
        display: inline-block;
        padding: 8px 16px;
        background-color: #ffc720;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        text-align: center;
    }

    .sell-button:hover {
        background-color: #f39c12;
    }
</style>

<section>
    <h1 class="heading">Sell your Property</h1>
    <table>
        <thead>
        <tr>
            <th>Number</th>
            <th>District Name</th>
            <th>Village</th>
            <th>Block Name</th>
            <th>Plot Number</th>
            <th>Block Number</th>
            <th>Apartment Number</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        // Fetch properties from database
        $dsn = 'mysql:host=127.0.0.1;dbname=wise';
        $user = 'root';
        $pass = '994422Gg';
        $option = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',);
        
        try {
            $con = new PDO($dsn, $user, $pass, $option);
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // First get the user ID from the username
            $stmt = $con->prepare("SELECT User_ID FROM user WHERE User_Name = ?");
            $stmt->execute([$_SESSION['Username']]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($userData) {
                // Get properties owned by the current user
                $stmt = $con->prepare("SELECT * FROM properties WHERE owner_id = ?");
                $stmt->execute([$userData['User_ID']]);
                $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (empty($properties)) {
                    echo "<tr><td colspan='8' style='text-align: center;'>No properties found</td></tr>";
                } else {
                    foreach ($properties as $index => $property) {
                        echo "<tr>";
                        echo "<td>" . ($index + 1) . "</td>";
                        echo "<td>" . htmlspecialchars($property['district_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($property['village']) . "</td>";
                        echo "<td>" . htmlspecialchars($property['block_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($property['plot_number']) . "</td>";
                        echo "<td>" . htmlspecialchars($property['block_number']) . "</td>";
                        echo "<td>" . htmlspecialchars($property['apartment_number']) . "</td>";
                        echo "<td><a href='sellReq?property_id=" . $property['id'] . "' class='sell-button'>Sell</a></td>";
                        echo "</tr>";
                    }
                }
            } else {
                echo "<tr><td colspan='8' style='text-align: center; color: red;'>User not found</td></tr>";
            }
        } catch (PDOException $e) {
            echo "<tr><td colspan='8' style='text-align: center; color: red;'>Error loading properties: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
        }
        ?>
        </tbody>
    </table>
</section>

<script>
function selectProperty(propertyId) {
    document.getElementById('selectedPropertyId').value = propertyId;
    const form = document.getElementById('sellForm');
    form.action = window.location.pathname.replace('sell', 'sellReq');
    form.submit();
}
</script>
