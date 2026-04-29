<?php
$trackingNumber = (string)($trackingNumber ?? '');
?>
<section class="admin-page sell-req-popup-page">
    <div class="card admin-page-card">
        <h1 class="heading" style="margin-top:0;">Request submitted</h1>
        <p class="tutor" style="margin-top:0;">Use this tracking number for on-site DLS verification.</p>

        <div class="admin-page-status">
            <strong>Tracking Number:</strong>
            <span id="tracking-number" class="contract-address"><?= htmlspecialchars($trackingNumber) ?></span>
        </div>

        <div class="admin-page-actions">
            <button type="button" id="copy-btn" class="inline-btn" onclick="copyTrackingNumber()">
                <i class="fa-solid fa-copy" aria-hidden="true"></i>
                Copy tracking number
            </button>
            <a class="inline-btn" target="_blank" rel="noopener"
               href="sellReqReceipt?tracking_number=<?= urlencode($trackingNumber) ?>">
                <i class="fa-solid fa-file-pdf" aria-hidden="true"></i>
                Download request slip (PDF)
            </a>
        </div>

        <p class="admin-page-help" style="margin-top:0.85rem;">
            Seller and buyer must attend the DLS office and present this tracking number to the notary, who will review and verify both parties.
        </p>
    </div>
</section>

<script>
function copyTrackingNumber() {
    const numberEl = document.getElementById('tracking-number');
    if (!numberEl) return;
    const text = numberEl.innerText;
    navigator.clipboard.writeText(text).then(() => {
        const btn = document.getElementById('copy-btn');
        if (!btn) return;
        btn.innerHTML = '<i class="fa-solid fa-check" aria-hidden="true"></i> Copied';
        window.setTimeout(() => {
            btn.innerHTML = '<i class="fa-solid fa-copy" aria-hidden="true"></i> Copy tracking number';
        }, 1800);
    });
}
</script>

