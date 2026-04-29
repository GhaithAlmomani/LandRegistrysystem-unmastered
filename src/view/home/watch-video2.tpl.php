<div class="watch-video-page">
    <a href="<?= htmlspecialchars(url('learn-more')) ?>" class="watch-video-back"><i class="fas fa-arrow-left" aria-hidden="true"></i> Back to tutorials</a>

    <div class="watch-video-grid">
        <div class="watch-video-player-card">
            <div class="watch-video-aspect">
                <video
                    src="<?= htmlspecialchars(url('Vids/E-Sell .mp4')) ?>"
                    controls
                    poster="<?= htmlspecialchars(url('Vids/thumbnail1.jpg')) ?>"
                    id="watch-video2-main"
                    playsinline
                    preload="metadata">
                    <a href="<?= htmlspecialchars(url('Vids/E-Sell .mp4')) ?>">Download video (MP4)</a>
                </video>
            </div>
        </div>

        <aside class="watch-video-details" aria-labelledby="watch-video2-title">
            <h1 class="watch-video-title" id="watch-video2-title">E-sell service request application tutorial</h1>

            <ul class="watch-video-stats">
                <li class="watch-video-stat"><i class="fas fa-calendar" aria-hidden="true"></i> 2 Oct 2023</li>
                <li class="watch-video-stat"><i class="fas fa-heart" aria-hidden="true"></i> 44 likes</li>
            </ul>

            <div class="watch-video-publisher">
                <img src="<?= htmlspecialchars(url('Vids/logo.jpg')) ?>" width="48" height="48" alt="Department of Land and Survey logo">
                <div class="watch-video-publisher-text">
                    <p class="watch-video-publisher-name">Department of Land and Survey</p>
                    <span>Publisher</span>
                </div>
            </div>

            <p class="watch-video-description">This video shows how to send a request to sell a property.</p>

            <div class="watch-video-actions">
                <a href="<?= htmlspecialchars(url('watch-video')) ?>" class="btn btn-outline">E-services overview</a>
                <a href="<?= htmlspecialchars(url('learn-more')) ?>" class="inline-btn">All tutorials</a>
            </div>
        </aside>
    </div>
</div>
