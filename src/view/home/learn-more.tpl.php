<div class="learn-more-page">
    <h1 class="heading">Guidance &amp; video tutorials</h1>
    <p class="learn-more-lead">Short walkthroughs on e-services, property sales, documentation, and common questions—aligned with Department of Land and Survey processes.</p>

    <div class="learn-more-toolbar">
        <form class="learn-more-search" action="<?= htmlspecialchars(url('learn-more')) ?>" method="get" role="search" aria-label="Search tutorials">
            <label class="visually-hidden" for="tutorial-search">Search tutorials</label>
            <input type="search" id="tutorial-search" name="q" class="box" placeholder="Search tutorials…" maxlength="100" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" autocomplete="off">
            <button type="submit" class="btn learn-more-search-btn" aria-label="Submit search"><i class="fas fa-search" aria-hidden="true"></i></button>
        </form>
    </div>

    <div class="learn-more-tabs-wrap">
        <div class="learn-more-tabs" role="tablist" aria-label="Tutorial categories">
            <button type="button" class="learn-more-tab active" role="tab" aria-selected="true" data-filter="all">All</button>
            <button type="button" class="learn-more-tab" role="tab" aria-selected="false" data-filter="sales">Property sales</button>
            <button type="button" class="learn-more-tab" role="tab" aria-selected="false" data-filter="egov">E-government</button>
            <button type="button" class="learn-more-tab" role="tab" aria-selected="false" data-filter="docs">Documentation</button>
            <button type="button" class="learn-more-tab" role="tab" aria-selected="false" data-filter="faq">FAQs</button>
        </div>
    </div>

    <section class="learn-more-section" aria-labelledby="featured-heading">
        <h2 class="heading" id="featured-heading">Featured tutorials</h2>
        <p class="learn-more-section-intro">Curated entries for getting started with digital services.</p>
        <div class="learn-more-grid">
            <article class="learn-more-card" data-category="sales">
                <a class="learn-more-card-thumb" href="<?= htmlspecialchars(url('watch-video')) ?>" aria-hidden="true" tabindex="-1">
                    <span class="learn-more-card-play" aria-hidden="true"><i class="fas fa-play"></i></span>
                </a>
                <div class="learn-more-card-body">
                    <h3 class="learn-more-card-title"><a href="<?= htmlspecialchars(url('watch-video')) ?>">How to apply for e-sell property</a></h3>
                    <p class="learn-more-card-sub">Step-by-step guide</p>
                    <div class="learn-more-meta">
                        <span><i class="fas fa-eye" aria-hidden="true"></i> 1,208 views</span>
                        <span><i class="far fa-clock" aria-hidden="true"></i> 15:30</span>
                        <span><i class="far fa-calendar" aria-hidden="true"></i> Updated 2 days ago</span>
                    </div>
                    <a href="<?= htmlspecialchars(url('watch-video')) ?>" class="inline-btn">Watch tutorial</a>
                </div>
            </article>

            <article class="learn-more-card" data-category="egov">
                <a class="learn-more-card-thumb" href="<?= htmlspecialchars(url('watch-video2')) ?>" aria-hidden="true" tabindex="-1">
                    <span class="learn-more-card-play" aria-hidden="true"><i class="fas fa-play"></i></span>
                </a>
                <div class="learn-more-card-body">
                    <h3 class="learn-more-card-title"><a href="<?= htmlspecialchars(url('watch-video2')) ?>">E-government services</a></h3>
                    <p class="learn-more-card-sub">Complete overview</p>
                    <div class="learn-more-meta">
                        <span><i class="fas fa-eye" aria-hidden="true"></i> 1,088 views</span>
                        <span><i class="far fa-clock" aria-hidden="true"></i> 12:45</span>
                        <span><i class="far fa-calendar" aria-hidden="true"></i> Updated 1 week ago</span>
                    </div>
                    <a href="<?= htmlspecialchars(url('watch-video2')) ?>" class="inline-btn">Watch tutorial</a>
                </div>
            </article>
        </div>
    </section>

    <section class="learn-more-section" aria-labelledby="popular-heading">
        <h2 class="heading" id="popular-heading">Popular tutorials</h2>
        <p class="learn-more-section-intro">Frequently opened guides from citizens and staff.</p>
        <div class="learn-more-grid">
            <article class="learn-more-card" data-category="docs">
                <a class="learn-more-card-thumb" href="<?= htmlspecialchars(url('watch-video')) ?>" aria-hidden="true" tabindex="-1">
                    <span class="learn-more-card-play" aria-hidden="true"><i class="fas fa-play"></i></span>
                </a>
                <div class="learn-more-card-body">
                    <h3 class="learn-more-card-title"><a href="<?= htmlspecialchars(url('watch-video')) ?>">Property documentation guide</a></h3>
                    <p class="learn-more-card-sub">Required documents</p>
                    <div class="learn-more-meta">
                        <span><i class="fas fa-eye" aria-hidden="true"></i> 956 views</span>
                        <span><i class="far fa-clock" aria-hidden="true"></i> 18:20</span>
                        <span><i class="far fa-calendar" aria-hidden="true"></i> Updated 3 days ago</span>
                    </div>
                    <a href="<?= htmlspecialchars(url('watch-video')) ?>" class="inline-btn">Watch tutorial</a>
                </div>
            </article>

            <article class="learn-more-card" data-category="egov">
                <a class="learn-more-card-thumb" href="<?= htmlspecialchars(url('watch-video2')) ?>" aria-hidden="true" tabindex="-1">
                    <span class="learn-more-card-play" aria-hidden="true"><i class="fas fa-play"></i></span>
                </a>
                <div class="learn-more-card-body">
                    <h3 class="learn-more-card-title"><a href="<?= htmlspecialchars(url('watch-video2')) ?>">Digital signature process</a></h3>
                    <p class="learn-more-card-sub">Security &amp; identity</p>
                    <div class="learn-more-meta">
                        <span><i class="fas fa-eye" aria-hidden="true"></i> 845 views</span>
                        <span><i class="far fa-clock" aria-hidden="true"></i> 10:15</span>
                        <span><i class="far fa-calendar" aria-hidden="true"></i> Updated 5 days ago</span>
                    </div>
                    <a href="<?= htmlspecialchars(url('watch-video2')) ?>" class="inline-btn">Watch tutorial</a>
                </div>
            </article>

            <article class="learn-more-card" data-category="sales">
                <a class="learn-more-card-thumb" href="<?= htmlspecialchars(url('watch-video')) ?>" aria-hidden="true" tabindex="-1">
                    <span class="learn-more-card-play" aria-hidden="true"><i class="fas fa-play"></i></span>
                </a>
                <div class="learn-more-card-body">
                    <h3 class="learn-more-card-title"><a href="<?= htmlspecialchars(url('watch-video')) ?>">Transfer workflow overview</a></h3>
                    <p class="learn-more-card-sub">From request to registration</p>
                    <div class="learn-more-meta">
                        <span><i class="fas fa-eye" aria-hidden="true"></i> 712 views</span>
                        <span><i class="far fa-clock" aria-hidden="true"></i> 22:04</span>
                        <span><i class="far fa-calendar" aria-hidden="true"></i> Updated 1 week ago</span>
                    </div>
                    <a href="<?= htmlspecialchars(url('watch-video')) ?>" class="inline-btn">Watch tutorial</a>
                </div>
            </article>

            <article class="learn-more-card" data-category="faq">
                <a class="learn-more-card-thumb" href="<?= htmlspecialchars(url('watch-video2')) ?>" aria-hidden="true" tabindex="-1">
                    <span class="learn-more-card-play" aria-hidden="true"><i class="fas fa-play"></i></span>
                </a>
                <div class="learn-more-card-body">
                    <h3 class="learn-more-card-title"><a href="<?= htmlspecialchars(url('watch-video2')) ?>">Common questions (FAQ)</a></h3>
                    <p class="learn-more-card-sub">Wait times, fees, and channels</p>
                    <div class="learn-more-meta">
                        <span><i class="fas fa-eye" aria-hidden="true"></i> 2,401 views</span>
                        <span><i class="far fa-clock" aria-hidden="true"></i> 8:50</span>
                        <span><i class="far fa-calendar" aria-hidden="true"></i> Updated 4 days ago</span>
                    </div>
                    <a href="<?= htmlspecialchars(url('watch-video2')) ?>" class="inline-btn">Watch tutorial</a>
                </div>
            </article>
        </div>
    </section>

    <p class="learn-more-empty" id="learn-more-empty" hidden>No tutorials in this category yet.</p>

    <div class="page-actions learn-more-actions">
        <a href="<?= htmlspecialchars(url('contact')) ?>" class="btn">Contact us</a>
        <a href="<?= htmlspecialchars(url('about')) ?>" class="btn btn-outline">About DLS</a>
        <a href="<?= htmlspecialchars(url('')) ?>" class="btn btn-outline">Back to home</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var tabs = document.querySelectorAll('.learn-more-tab');
    var cards = document.querySelectorAll('.learn-more-card');
    var emptyMsg = document.getElementById('learn-more-empty');
    if (!tabs.length || !cards.length) return;

    function applyFilter(filter) {
        var visible = 0;
        cards.forEach(function (card) {
            var cats = (card.getAttribute('data-category') || '').trim().split(/\s+/);
            var show = filter === 'all' || cats.indexOf(filter) !== -1;
            card.hidden = !show;
            if (show) visible++;
        });
        if (emptyMsg) emptyMsg.hidden = visible !== 0;
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var filter = tab.getAttribute('data-filter') || 'all';
            tabs.forEach(function (t) {
                t.classList.toggle('active', t === tab);
                t.setAttribute('aria-selected', t === tab ? 'true' : 'false');
            });
            applyFilter(filter);
        });
    });
});
</script>
