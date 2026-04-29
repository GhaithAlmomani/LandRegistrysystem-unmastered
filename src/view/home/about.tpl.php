<div class="about-page">
    <h1 class="heading">About the Department of Land and Survey</h1>
    <p class="about-lead">Jordan’s authority for immovable property rights, cadastral information, and trusted land-survey services—supporting sustainable development and transparent public access.</p>

    <ul class="about-pillars" aria-label="Organisation focus">
        <li class="about-pillar">
            <span class="about-pillar-icon" aria-hidden="true"><i class="fas fa-shield-halved"></i></span>
            <span class="about-pillar-text">
                <strong>Trusted registry</strong>
                <span>Secure documentation of ownership and encumbrances</span>
            </span>
        </li>
        <li class="about-pillar">
            <span class="about-pillar-icon" aria-hidden="true"><i class="fas fa-users"></i></span>
            <span class="about-pillar-text">
                <strong>Public service</strong>
                <span>Clear channels for citizens, investors, and partners</span>
            </span>
        </li>
        <li class="about-pillar">
            <span class="about-pillar-icon" aria-hidden="true"><i class="fas fa-chart-line"></i></span>
            <span class="about-pillar-text">
                <strong>National data</strong>
                <span>Geographic and registry information for planning and policy</span>
            </span>
        </li>
    </ul>

    <section class="about-intro" aria-labelledby="about-vision-heading">
        <div class="about-intro-grid">
            <div class="about-intro-media">
                <img src="<?= htmlspecialchars(url('Vids/About%20us.svg')) ?>" width="400" height="320" alt="Illustration representing land and survey services">
            </div>
            <div class="about-intro-body prose">
                <h2 class="h3" id="about-vision-heading">DLS vision</h2>
                <p>Distinguished real estate services and information that serve the purposes of sustainable development.</p>

                <h2 class="h3 about-mission-title">DLS mission</h2>
                <p>Establishing, documenting, and preserving the right to own immovable property and facilitating practicing this right, as well as providing the database necessary for the establishment of the geographic information system and continuing to improve and develop the quality of real estate services provided to service recipients with the participation of the public and private sectors nationally and internationally.</p>
            </div>
        </div>
    </section>

    <section class="about-chart-section" aria-labelledby="about-chart-heading">
        <h2 class="heading" id="about-chart-heading">Property registration statistics (2019–2023)</h2>
        <p class="about-chart-intro">Illustrative trend of annual registrations—actual figures are published in official DLS reports.</p>
        <div class="card about-chart-card">
            <canvas id="registrationChart" aria-label="Bar chart of annual property registrations"></canvas>
        </div>
    </section>

    <div class="page-actions about-page-actions">
        <a href="contact" class="btn">Contact us</a>
        <a href="home" class="btn btn-outline">Back to home</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('registrationChart');
    if (!canvas || typeof Chart === 'undefined') return;

    function theme() {
        const dark = document.body.classList.contains('dark');
        return {
            dark,
            brand: '#00A62A',
            brandSoft: dark ? 'rgba(0, 166, 42, 0.45)' : 'rgba(0, 166, 42, 0.55)',
            tick: dark ? '#8BAA8B' : '#504D48',
            grid: dark ? 'rgba(120, 200, 120, 0.08)' : 'rgba(30, 26, 20, 0.08)',
            titleColor: dark ? '#ECF5EC' : '#252219'
        };
    }

    let chart;
    function buildChart() {
        const t = theme();
        const ctx = canvas.getContext('2d');
        if (chart) chart.destroy();
        chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['2019', '2020', '2021', '2022', '2023'],
                datasets: [{
                    label: 'Properties registered',
                    data: [45000, 38000, 42000, 48000, 52000],
                    backgroundColor: t.brandSoft,
                    borderColor: t.brand,
                    borderWidth: 2,
                    borderRadius: 8,
                    hoverBackgroundColor: t.brand,
                    hoverBorderColor: t.dark ? '#5AC880' : '#007D1E'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.85,
                animation: {
                    duration: 900,
                    easing: 'easeOutQuart'
                },
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    title: {
                        display: true,
                        text: 'Annual property registrations (illustrative)',
                        color: t.titleColor,
                        font: { family: "'Plus Jakarta Sans', sans-serif", size: 15, weight: '600' },
                        padding: { bottom: 8 }
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: t.tick,
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 },
                            usePointStyle: true,
                            pointStyle: 'rectRounded'
                        }
                    },
                    tooltip: {
                        backgroundColor: t.dark ? 'rgba(28, 38, 27, 0.96)' : 'rgba(255, 255, 255, 0.96)',
                        titleColor: t.titleColor,
                        bodyColor: t.tick,
                        borderColor: t.dark ? 'rgba(120, 200, 120, 0.2)' : 'rgba(30, 26, 20, 0.12)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 10,
                        displayColors: true,
                        callbacks: {
                            label: function (ctx) {
                                const v = ctx.parsed.y;
                                return ' ' + (v == null ? '' : v.toLocaleString()) + ' registrations';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Registrations',
                            color: t.tick,
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 }
                        },
                        ticks: {
                            color: t.tick,
                            callback: function (value) {
                                if (value >= 1000) return (value / 1000) + 'k';
                                return value;
                            }
                        },
                        grid: { color: t.grid }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Year',
                            color: t.tick,
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 }
                        },
                        ticks: { color: t.tick },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    buildChart();

    const observer = new MutationObserver(function () {
        buildChart();
    });
    observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
});
</script>
