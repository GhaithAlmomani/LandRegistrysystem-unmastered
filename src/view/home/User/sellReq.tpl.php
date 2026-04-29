<?php
$formErrors = $formErrors ?? [];
$formOld = $formOld ?? [];
$userDetails = $userDetails ?? null;
$propertyDetails = $propertyDetails ?? null;
$trackingFlash = $trackingFlash ?? null;
$pendingForSeller = $pendingForSeller ?? null;

$sellerNameDefault = (string)($formOld['seller_full_name'] ?? ($userDetails['User_Name'] ?? ''));
$sellerNidDefault = (string)($formOld['seller_national_id'] ?? ($userDetails['User_NationalID'] ?? ''));
$sellerEmailDefault = (string)($formOld['seller_email'] ?? ($userDetails['User_Email'] ?? ''));
$sellerPhoneDefault = (string)($formOld['seller_phone'] ?? ($userDetails['User_Phone'] ?? ''));

$buyerNameDefault = (string)($formOld['buyer_name'] ?? '');
$buyerNidDefault = (string)($formOld['buyer_national_id'] ?? '');
$buyerEmailDefault = (string)($formOld['buyer_email'] ?? '');
$buyerPhoneDefault = (string)($formOld['buyer_phone'] ?? '');
$buyerAddressDefault = (string)($formOld['buyer_address'] ?? '');

$propertyIdVal = (string)($formOld['property_id'] ?? ($propertyDetails['id'] ?? ($_GET['property_id'] ?? '')));

function sellReqFieldErr(array $formErrors, string $key): string
{
    if (empty($formErrors[$key]) || !is_array($formErrors[$key])) {
        return '';
    }
    return implode(' ', $formErrors[$key]);
}
?>

<section class="admin-page sell-req-page">
    <header class="registry-lookup-hero card">
        <div class="registry-lookup-hero__badge admin-portal-badge">
            <i class="fa-solid fa-file-signature" aria-hidden="true"></i>
            <span>Land transfer request</span>
        </div>
        <h1 class="heading registry-lookup-hero__title">Voluntary transfer declaration</h1>
        <p class="registry-lookup-hero__lead">
            File a formal request to transfer ownership of a parcel you hold. Declarations must match the civil registry
            on record for both seller and buyer. Requests that fail verification cannot be submitted.
        </p>
    </header>

    <?php if (!empty($errorFlash)): ?>
        <div class="error-message"><?= htmlspecialchars($errorFlash) ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="admin-page-status is-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($formErrors['seller_identity']) || !empty($formErrors['buyer_identity'])): ?>
        <div class="admin-page-status is-warning">
            <?php if (!empty($formErrors['seller_identity'])): ?>
                <strong>Seller:</strong> <?= htmlspecialchars(implode(' ', $formErrors['seller_identity'])) ?>
            <?php endif; ?>
            <?php if (!empty($formErrors['buyer_identity'])): ?>
                <?php if (!empty($formErrors['seller_identity'])): ?><br><?php endif; ?>
                <strong>Buyer:</strong> <?= htmlspecialchars(implode(' ', $formErrors['buyer_identity'])) ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($formErrors['property_id'])): ?>
        <div class="admin-page-status is-warning">
            <?= htmlspecialchars(implode(' ', (array)$formErrors['property_id'])) ?>
        </div>
    <?php endif; ?>

    <?php if ($propertyDetails): ?>
        <div class="card admin-page-card">
            <h2 class="title" style="margin-top:0;">Property covered by this request</h2>
            <dl class="registry-lookup-dl">
                <dt>District</dt><dd><?= htmlspecialchars((string)($propertyDetails['district_name'] ?? '')) ?></dd>
                <dt>Village</dt><dd><?= htmlspecialchars((string)($propertyDetails['village'] ?? '')) ?></dd>
                <dt>Block</dt><dd><?= htmlspecialchars((string)($propertyDetails['block_name'] ?? '')) ?> / <?= htmlspecialchars((string)($propertyDetails['block_number'] ?? '')) ?></dd>
                <dt>Plot</dt><dd><?= htmlspecialchars((string)($propertyDetails['plot_number'] ?? '')) ?></dd>
                <?php
                    $propertyType = strtolower((string)($propertyDetails['type'] ?? ''));
                    if ($propertyType === '') {
                        $propertyType = (empty($propertyDetails['apartment_number']) || $propertyDetails['apartment_number'] === '-') ? 'land' : 'apartment';
                    }
                ?>
                <dt>Type</dt><dd><?= htmlspecialchars(ucfirst($propertyType)) ?></dd>
                <dt>Area</dt><dd><?= htmlspecialchars((string)(isset($propertyDetails['area']) && $propertyDetails['area'] !== null && $propertyDetails['area'] !== '' ? $propertyDetails['area'] . ' m²' : '—')) ?></dd>
            </dl>
        </div>
    <?php endif; ?>

    <?php if ($propertyDetails && !empty($pendingForSeller) && !empty($pendingForSeller['tracking_number'])): ?>
        <div class="admin-page-status is-warning">
            A transfer request for this property is already <strong>pending</strong>. Tracking number:
            <strong><?= htmlspecialchars((string)$pendingForSeller['tracking_number']) ?></strong>.
            You can view/cancel it from <a class="tx-link" href="myRequests">My requests</a>.
        </div>
    <?php endif; ?>

    <?php if (!$propertyDetails): ?>
        <div class="card admin-page-card">
            <p class="tutor" style="margin:0;">Select a property from <a class="tx-link" href="sell">Sell your property</a> first. This page opens when you choose a parcel to transfer.</p>
        </div>
    <?php else: ?>

        <form method="POST" action="sellReq" id="transferForm" class="sell-req-stack">
            <?= \MVC\core\CSRFToken::generateFormField() ?>
            <input type="hidden" name="property_id" value="<?= htmlspecialchars($propertyIdVal) ?>">

            <div class="card admin-page-card">
                <h2 class="title" style="margin-top:0;">Seller declaration</h2>
                <p class="admin-page-help" style="margin-top:0;">
                    Enter details exactly as registered on your national account. Automated checks compare full name, national ID, email, and phone.
                </p>

                <label class="admin-page-label" for="seller_full_name">Full name (as on civil record)</label>
                <input id="seller_full_name" type="text" name="seller_full_name" class="box" required maxlength="255" readonly
                       value="<?= htmlspecialchars($sellerNameDefault) ?>">
                <?php if ($e = sellReqFieldErr($formErrors, 'seller_full_name')): ?>
                    <p class="admin-page-help" style="color:var(--danger);"><?= htmlspecialchars($e) ?></p>
                <?php endif; ?>

                <label class="admin-page-label" for="seller_national_id">National ID</label>
                <input id="seller_national_id" type="text" name="seller_national_id" class="box" required maxlength="32" inputmode="numeric" readonly
                       autocomplete="off" value="<?= htmlspecialchars($sellerNidDefault) ?>">
                <?php if ($e = sellReqFieldErr($formErrors, 'seller_national_id')): ?>
                    <p class="admin-page-help" style="color:var(--danger);"><?= htmlspecialchars($e) ?></p>
                <?php endif; ?>

                <label class="admin-page-label" for="seller_email">Email</label>
                <input id="seller_email" type="email" name="seller_email" class="box" required maxlength="255"
                       value="<?= htmlspecialchars($sellerEmailDefault) ?>">
                <?php if ($e = sellReqFieldErr($formErrors, 'seller_email')): ?>
                    <p class="admin-page-help" style="color:var(--danger);"><?= htmlspecialchars($e) ?></p>
                <?php endif; ?>

                <label class="admin-page-label" for="seller_phone">Phone</label>
                <input id="seller_phone" type="tel" name="seller_phone" class="box" required maxlength="32"
                       value="<?= htmlspecialchars($sellerPhoneDefault) ?>">
                <?php if ($e = sellReqFieldErr($formErrors, 'seller_phone')): ?>
                    <p class="admin-page-help" style="color:var(--danger);"><?= htmlspecialchars($e) ?></p>
                <?php endif; ?>
            </div>

            <div class="card admin-page-card">
                <h2 class="title" style="margin-top:0;">Buyer declaration</h2>
                <p class="admin-page-help" style="margin-top:0;">
                    The buyer must already have a portal account. Details must match that account exactly (full name, national ID, email, phone).
                </p>

                <label class="admin-page-label" for="buyer_name">Buyer full name</label>
                <input id="buyer_name" type="text" name="buyer_name" class="box" required maxlength="120"
                       value="<?= htmlspecialchars($buyerNameDefault) ?>">
                <?php if ($e = sellReqFieldErr($formErrors, 'buyer_name')): ?>
                    <p class="admin-page-help" style="color:var(--danger);"><?= htmlspecialchars($e) ?></p>
                <?php endif; ?>

                <label class="admin-page-label" for="buyer_national_id">Buyer national ID</label>
                <input id="buyer_national_id" type="text" name="buyer_national_id" class="box" required maxlength="32" inputmode="numeric"
                       autocomplete="off" value="<?= htmlspecialchars($buyerNidDefault) ?>">
                <?php if ($e = sellReqFieldErr($formErrors, 'buyer_national_id')): ?>
                    <p class="admin-page-help" style="color:var(--danger);"><?= htmlspecialchars($e) ?></p>
                <?php endif; ?>

                <label class="admin-page-label" for="buyer_email">Buyer email</label>
                <input id="buyer_email" type="email" name="buyer_email" class="box" required maxlength="255"
                       value="<?= htmlspecialchars($buyerEmailDefault) ?>">
                <?php if ($e = sellReqFieldErr($formErrors, 'buyer_email')): ?>
                    <p class="admin-page-help" style="color:var(--danger);"><?= htmlspecialchars($e) ?></p>
                <?php endif; ?>

                <label class="admin-page-label" for="buyer_phone">Buyer phone</label>
                <input id="buyer_phone" type="tel" name="buyer_phone" class="box" required maxlength="32"
                       value="<?= htmlspecialchars($buyerPhoneDefault) ?>">
                <?php if ($e = sellReqFieldErr($formErrors, 'buyer_phone')): ?>
                    <p class="admin-page-help" style="color:var(--danger);"><?= htmlspecialchars($e) ?></p>
                <?php endif; ?>

                <label class="admin-page-label" for="buyer_address">Buyer correspondence address</label>
                <input id="buyer_address" type="text" name="buyer_address" class="box" required maxlength="255"
                       placeholder="Street / area used for official correspondence"
                       value="<?= htmlspecialchars($buyerAddressDefault) ?>">
                <?php if ($e = sellReqFieldErr($formErrors, 'buyer_address')): ?>
                    <p class="admin-page-help" style="color:var(--danger);"><?= htmlspecialchars($e) ?></p>
                <?php endif; ?>
            </div>

            <div class="admin-page-actions sell-req-submit" style="justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <button type="submit" class="inline-btn" <?= (!empty($pendingForSeller) ? 'disabled' : '') ?>>
                    <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                    Submit transfer request
                </button>
                <a class="inline-btn" href="myRequests">
                    <i class="fa-solid fa-folder-open" aria-hidden="true"></i>
                    View my requests
                </a>
            </div>
        </form>

    <?php endif; ?>

    <div id="sellReqModal" class="sell-req-modal <?= !empty($trackingFlash) ? 'is-open' : '' ?>" role="dialog" aria-modal="true" aria-labelledby="sellReqModalTitle">
        <div class="sell-req-modal__backdrop" onclick="closeSellReqModal()"></div>
        <div class="sell-req-modal__dialog">
            <button type="button" class="sell-req-modal__close" onclick="closeSellReqModal()" aria-label="Close">&times;</button>
            <div class="sell-req-modal__icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <h2 id="sellReqModalTitle" class="sell-req-modal__title">Request submitted</h2>
            <p class="sell-req-modal__subtitle">Your tracking number is required for in-person DLS notary verification.</p>

            <div class="sell-req-modal__tracking">
                <span id="tracking-number"><?= htmlspecialchars((string)($trackingFlash ?? '')) ?></span>
                <button type="button" id="copy-btn" class="inline-btn" onclick="copyTrackingNumber()">
                    <i class="fa-solid fa-copy" aria-hidden="true"></i>
                    Copy
                </button>
            </div>

            <?php if (!empty($trackingFlash)): ?>
                <div class="admin-page-actions" style="margin-top:0.85rem;">
                    <a class="inline-btn" target="_blank" rel="noopener"
                       href="sellReqReceipt?tracking_number=<?= urlencode((string)$trackingFlash) ?>">
                        <i class="fa-solid fa-file-pdf" aria-hidden="true"></i>
                        Download request slip (PDF)
                    </a>
                </div>
            <?php endif; ?>

            <p class="sell-req-modal__note">
                Seller and buyer must attend the DLS office and present this tracking number to the notary, who will verify both parties.
            </p>
        </div>
    </div>

    <script>
        function closeSellReqModal() {
            const modal = document.getElementById('sellReqModal');
            if (!modal) return;
            modal.classList.remove('is-open');
            document.body.classList.remove('modal-open');
        }

        function copyTrackingNumber() {
            const el = document.getElementById('tracking-number');
            if (!el) return;
            navigator.clipboard.writeText(el.innerText).then(() => {
                const copyBtn = document.getElementById('copy-btn');
                if (!copyBtn) return;
                copyBtn.innerHTML = '<i class="fa-solid fa-check" aria-hidden="true"></i> Copied';
                setTimeout(() => {
                    copyBtn.innerHTML = '<i class="fa-solid fa-copy" aria-hidden="true"></i> Copy';
                }, 1800);
            });
        }

        <?php if (!empty($trackingFlash)): ?>
        document.addEventListener('DOMContentLoaded', function () {
            document.body.classList.add('modal-open');
        });
        <?php endif; ?>
    </script>
</section>
