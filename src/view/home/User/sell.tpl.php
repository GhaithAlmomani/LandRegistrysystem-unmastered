<section class="admin-page sell-list-page">
    <header class="registry-lookup-hero card">
        <div class="registry-lookup-hero__badge admin-portal-badge">
            <i class="fa-solid fa-file-contract" aria-hidden="true"></i>
            <span>Transfer initiation</span>
        </div>
        <h1 class="heading registry-lookup-hero__title">Sell your property</h1>
        <p class="registry-lookup-hero__lead">
            Select one of your registered parcels to start a formal transfer request. The next step collects seller/buyer declarations and verifies identity records.
        </p>
    </header>

    <?php if (!empty($error)): ?>
        <div class="error-message"><?= htmlspecialchars((string)$error) ?></div>
    <?php endif; ?>

    <div class="card admin-page-card">
        <div class="admin-records-cardhead">
            <h2 class="title" style="margin:0;">Your available properties</h2>
            <span class="status-pill"><?= number_format((int)(is_array($properties ?? null) ? count($properties) : 0)) ?> item(s)</span>
        </div>

        <?php if (empty($properties)): ?>
            <p class="tutor" style="margin-top:0.85rem;">No properties found for your account.</p>
        <?php else: ?>
            <div class="table-container sell-list-table">
                <table>
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>District</th>
                        <th>Village</th>
                        <th>Block Name</th>
                        <th>Plot</th>
                        <th>Block No.</th>
                        <th>Type</th>
                        <th>Area</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($properties as $index => $property): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars((string)($property['district_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string)($property['village'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string)($property['block_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string)($property['plot_number'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string)($property['block_number'] ?? '')) ?></td>
                            <?php
                                $type = strtolower((string)($property['type'] ?? ''));
                                if ($type === '') {
                                    $type = (empty($property['apartment_number']) || $property['apartment_number'] === '-') ? 'land' : 'apartment';
                                }
                            ?>
                            <td><?= htmlspecialchars(ucfirst($type)) ?></td>
                            <td><?= htmlspecialchars((string)(isset($property['area']) && $property['area'] !== null && $property['area'] !== '' ? $property['area'] . ' m²' : '—')) ?></td>
                            <td>
                                <?php $isPending = strtolower((string)($property['status'] ?? '')) === 'pending_transfer'; ?>
                                <?php if ($isPending): ?>
                                    <span class="status-pill" title="A transfer request is already pending">Pending transfer</span>
                                    <a href="myRequests" class="inline-btn sell-list-action" style="margin-left:8px;">
                                        View request
                                    </a>
                                <?php else: ?>
                                    <a href="sellReq?property_id=<?= (int)($property['id'] ?? 0) ?>" class="inline-btn sell-list-action">
                                        Start request
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>
