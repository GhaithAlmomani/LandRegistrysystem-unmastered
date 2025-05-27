<style>
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .hero-section {
        position: relative;
        overflow: hidden;
        height: 500px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .hero-bg {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-size: cover;
        background-position: center;
        transition: transform 1s cubic-bezier(.77,0,.18,1);
        z-index: 1;
    }

    #heroBgCurrent { z-index: 2; }
    #heroBgNext { z-index: 1; }

    .hero-content {
        position: relative;
        z-index: 3;
        text-align: center;
        width: 100%;
    }

    .hero-section h1 {
        font-size: 4rem;
        margin-bottom: 2rem;
        text-transform: capitalize;
    }

    .hero-section p {
        font-size: 1.8rem;
        margin-bottom: 3rem;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }

    .hero-buttons {
        display: flex;
        gap: 2rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .hero-btn {
        display: inline-block;
        padding: 1.2rem 3rem;
        font-size: 1.6rem;
        background: var(--main-color);
        color: white;
        border-radius: 0.5rem;
        text-transform: capitalize;
        transition: all 0.3s ease;
    }

    .hero-btn:hover {
        background: #004408;
        transform: translateY(-2px);
    }

    .hero-btn.secondary {
        background: transparent;
        border: 2px solid white;
    }

    .hero-btn.secondary:hover {
        background: white;
        color: var(--main-color);
    }

    .features-section {
        padding: 6rem 2rem;
        background: #f8f9fa;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(30rem, 1fr));
        gap: 3rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .feature-card {
        background: white;
        padding: 3rem;
        border-radius: 1rem;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .feature-card:hover {
        transform: translateY(-5px);
    }

    .feature-card i {
        font-size: 4rem;
        color: var(--main-color);
        margin-bottom: 2rem;
    }

    .feature-card h3 {
        font-size: 2rem;
        color: var(--black);
        margin-bottom: 1.5rem;
    }

    .feature-card p {
        font-size: 1.6rem;
        color: var(--light-color);
        line-height: 1.6;
    }

    .cta-section {
        padding: 6rem 2rem;
        text-align: center;
        background: var(--main-color);
        color: white;
    }

    .cta-section h2 {
        font-size: 3rem;
        margin-bottom: 2rem;
    }

    .cta-section p {
        font-size: 1.8rem;
        margin-bottom: 3rem;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }

    @media (max-width: 768px) {
        .hero-section {
            padding: 4rem 1rem;
        }

        .hero-section h1 {
            font-size: 3rem;
        }

        .hero-buttons {
            flex-direction: column;
            gap: 1rem;
        }

        .features-section {
            padding: 3rem 1rem;
        }
    }
</style>

<div class="hero-section" id="heroSection">
    <div class="hero-bg" id="heroBgCurrent"></div>
    <div class="hero-bg" id="heroBgNext"></div>
    <div class="container">
        <div class="hero-content">
            <h1>Welcome to DLS Portal</h1>
            <p>Your trusted platform for property registration and management. Experience seamless property transactions with our advanced digital services.</p>
            <div class="hero-buttons">
                <a href="login" class="hero-btn">Get Started</a>
                <a href="learn-more" class="hero-btn secondary">Learn More</a>
            </div>
        </div>
    </div>
</div>

<section class="features-section">
    <div class="features-grid">
        <div class="feature-card">
            <i class="fas fa-building"></i>
            <h3>Property Registration</h3>
            <p>Register your properties quickly and securely with our streamlined digital process.</p>
        </div>

        <div class="feature-card">
            <i class="fas fa-chart-line"></i>
            <h3>Real-time Tracking</h3>
            <p>Monitor your property transactions and applications in real-time with our advanced tracking system.</p>
        </div>

        <div class="feature-card">
            <i class="fas fa-shield-alt"></i>
            <h3>Secure Transactions</h3>
            <p>Your data is protected with state-of-the-art security measures and encryption.</p>
        </div>
    </div>
</section>

<section class="cta-section">
    <h2>Ready to Get Started?</h2>
    <p>Join thousands of satisfied users who have already streamlined their property management process with DLS Portal.</p>
    <a href="register" class="hero-btn secondary">Register Now</a>
</section>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script>
const heroImages = [
    "images/hero-bg 1.jpeg",
    "images/hero-bg 2.jpeg",
    "images/hero-bg 3.jpeg",
    "images/hero-bg 4.jpeg",
    "images/hero-bg 5.jpeg"
];
let currentHero = 0;
const heroBgCurrent = document.getElementById('heroBgCurrent');
const heroBgNext = document.getElementById('heroBgNext');

function setHeroBg(index, element) {
    element.style.backgroundImage = `linear-gradient(rgba(0,0,0,0.7),rgba(0,0,0,0.7)), url('${heroImages[index]}')`;
    element.style.transform = 'translateX(0)';
}

setHeroBg(currentHero, heroBgCurrent);
setHeroBg((currentHero + 1) % heroImages.length, heroBgNext);
heroBgNext.style.transform = 'translateX(100%)';

setInterval(() => {
    const nextHero = (currentHero + 1) % heroImages.length;
    setHeroBg(nextHero, heroBgNext);
    heroBgNext.style.transition = 'none';
    heroBgNext.style.transform = 'translateX(100%)';
    heroBgNext.offsetHeight; // force reflow
    heroBgNext.style.transition = 'transform 1s cubic-bezier(.77,0,.18,1)';
    heroBgCurrent.style.transition = 'transform 1s cubic-bezier(.77,0,.18,1)';
    heroBgNext.style.transform = 'translateX(0)';
    heroBgCurrent.style.transform = 'translateX(-100%)';
    setTimeout(() => {
        // Swap roles
        let temp = heroBgCurrent;
        heroBgCurrent = heroBgNext;
        heroBgNext = temp;
        currentHero = nextHero;
    }, 1000);
}, 5000);
</script>