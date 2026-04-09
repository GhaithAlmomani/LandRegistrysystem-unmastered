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
        <?php if (!empty($error)): ?>
            <tr><td colspan="8" style="text-align: center; color: red;"><?= htmlspecialchars($error) ?></td></tr>
        <?php elseif (empty($properties)): ?>
            <tr><td colspan="8" style="text-align: center;">No properties found</td></tr>
        <?php else: ?>
            <?php foreach ($properties as $index => $property): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($property['district_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($property['village'] ?? '') ?></td>
                    <td><?= htmlspecialchars($property['block_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($property['plot_number'] ?? '') ?></td>
                    <td><?= htmlspecialchars($property['block_number'] ?? '') ?></td>
                    <td><?= htmlspecialchars($property['apartment_number'] ?? '') ?></td>
                    <td><a href="sellReq?property_id=<?= (int)($property['id'] ?? 0) ?>" class="sell-button">Sell</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
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
