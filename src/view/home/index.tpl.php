<div class="home-page">
    <section class="home-hero" id="heroSection" aria-labelledby="home-hero-title">
        <div class="home-hero-bg" id="heroBgCurrent" aria-hidden="true"></div>
        <div class="home-hero-bg" id="heroBgNext" aria-hidden="true"></div>

        <div class="home-hero-inner">
            <p class="home-hero-kicker">Jordan · Department of Lands & Survey</p>
            <h1 id="home-hero-title">Welcome to DLS Portal</h1>
            <p class="home-hero-lead">
                Your trusted platform for property registration and management. Complete transactions with clarity,
                security, and a digital experience aligned with national land records.
            </p>
            <div class="home-hero-actions">
                <a href="login" class="btn">Get started</a>
                <a href="learn-more" class="btn btn-outline">Learn more</a>
            </div>
        </div>

        <div class="home-hero-gallery">
            <p class="home-hero-caption" id="hero-gallery-label">Featured views</p>
            <div class="home-hero-gallery-row" role="group" aria-labelledby="hero-gallery-label" id="heroThumbRow"></div>
        </div>
    </section>

    <section class="home-features" aria-labelledby="home-features-title">
        <h2 class="heading" id="home-features-title">Why use this portal</h2>
        <div class="home-features-grid">
            <article class="home-feature-card">
                <div class="home-feature-icon" aria-hidden="true"><i class="fas fa-building"></i></div>
                <h3>Property registration</h3>
                <p>Register properties through a structured digital process with clear status updates.</p>
            </article>
            <article class="home-feature-card">
                <div class="home-feature-icon" aria-hidden="true"><i class="fas fa-chart-line"></i></div>
                <h3>Real-time tracking</h3>
                <p>Follow applications and transfers as they move through verification and approval.</p>
            </article>
            <article class="home-feature-card">
                <div class="home-feature-icon" aria-hidden="true"><i class="fas fa-shield-alt"></i></div>
                <h3>Secure transactions</h3>
                <p>Strong protections for your data and identity throughout each session.</p>
            </article>
        </div>
    </section>

    <section class="home-cta" aria-labelledby="home-cta-title">
        <h2 id="home-cta-title">Ready to begin?</h2>
        <p>Create an account to access registration tools, ownership records, and seller workflows in one place.</p>
        <a href="register" class="btn">Register now</a>
    </section>
</div>

<script>
(function () {
    const heroImages = [
        'images/hero-bg 1.jpeg',
        'images/hero-bg 2.jpeg',
        'images/hero-bg 3.jpeg',
        'images/hero-bg 4.jpeg',
        'images/hero-bg 5.jpeg'
    ];

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const slideMs = reduceMotion ? 0 : 1000;
    const intervalMs = reduceMotion ? 0 : 5000;

    let heroBgCurrent = document.getElementById('heroBgCurrent');
    let heroBgNext = document.getElementById('heroBgNext');
    const thumbRow = document.getElementById('heroThumbRow');
    if (!heroBgCurrent || !heroBgNext || !thumbRow) return;

    let currentHero = 0;
    let timerId = null;

    /* Photo only — green wash comes from .home-hero::after (one layer, easier to tune) */
    function heroOverlay(url) {
        return "url('" + encodeURI(url).replace(/'/g, '%27') + "')";
    }

    function setHeroBg(index, element) {
        element.style.backgroundImage = heroOverlay(heroImages[index]);
        element.style.transform = 'translateX(0)';
    }

    function setActiveThumb(index) {
        thumbRow.querySelectorAll('.home-hero-thumb').forEach(function (btn, i) {
            var on = i === index;
            btn.classList.toggle('is-active', on);
            btn.setAttribute('aria-pressed', on ? 'true' : 'false');
        });
    }

    function buildThumbs() {
        heroImages.forEach(function (src, i) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'home-hero-thumb' + (i === 0 ? ' is-active' : '');
            btn.setAttribute('aria-pressed', i === 0 ? 'true' : 'false');
            btn.setAttribute('aria-label', 'Show slide ' + (i + 1));
            btn.style.backgroundImage = "url('" + encodeURI(src).replace(/'/g, '%27') + "')";
            btn.addEventListener('click', function () {
                goToSlide(i);
            });
            thumbRow.appendChild(btn);
        });
    }

    function goToSlide(nextIndex) {
        if (nextIndex === currentHero || nextIndex < 0 || nextIndex >= heroImages.length) return;

        if (reduceMotion || slideMs === 0) {
            currentHero = nextIndex;
            setHeroBg(currentHero, heroBgCurrent);
            heroBgNext.style.transition = 'none';
            heroBgCurrent.style.transition = 'none';
            heroBgNext.style.transform = '';
            heroBgCurrent.style.transform = '';
            setActiveThumb(currentHero);
            return;
        }

        setHeroBg(nextIndex, heroBgNext);
        heroBgNext.style.transition = 'none';
        heroBgNext.style.transform = 'translateX(100%)';
        heroBgNext.offsetHeight;
        heroBgNext.style.transition = 'transform ' + slideMs / 1000 + 's cubic-bezier(0.77, 0, 0.18, 1)';
        heroBgCurrent.style.transition = 'transform ' + slideMs / 1000 + 's cubic-bezier(0.77, 0, 0.18, 1)';
        heroBgNext.style.transform = 'translateX(0)';
        heroBgCurrent.style.transform = 'translateX(-100%)';

        window.setTimeout(function () {
            var temp = heroBgCurrent;
            heroBgCurrent = heroBgNext;
            heroBgNext = temp;
            currentHero = nextIndex;
            heroBgCurrent.style.transition = 'none';
            heroBgNext.style.transition = 'none';
            heroBgCurrent.style.transform = 'translateX(0)';
            heroBgNext.style.transform = 'translateX(100%)';
            setHeroBg(currentHero, heroBgCurrent);
            setActiveThumb(currentHero);
        }, slideMs);
    }

    function schedule() {
        if (intervalMs <= 0) return;
        if (timerId) clearInterval(timerId);
        timerId = setInterval(function () {
            goToSlide((currentHero + 1) % heroImages.length);
        }, intervalMs);
    }

    buildThumbs();
    setHeroBg(0, heroBgCurrent);
    setHeroBg(1 % heroImages.length, heroBgNext);
    heroBgNext.style.transform = 'translateX(100%)';
    setActiveThumb(0);

    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            if (timerId) clearInterval(timerId);
            timerId = null;
        } else {
            schedule();
        }
    });

    schedule();
})();
</script>
