<section class="about">
    <div class="row">
        <div class="image">
            <img src="Vids/About us.svg" alt="">
        </div>
        <div class="content">
            <h3>DLS Vision</h3>
            <p>Distinguished Real estate services and information that serve the purposes of sustainable development.</p>
            <h3></h3>
            <h3>DLS Mission</h3>
            <p>Establishing, documenting, and preserving the right to own immovable property and facilitating practicing this right as well as providing the database necessary for the establishment of the Geographic Information System and continuing to improve and develop the quality of real estate services provided to service recipients with the participation of the public and private sectors nationally and internationally.</p>
        </div>
    </div>
</section>

<!-- Registration Statistics Chart: Place below main content, above footer -->
<div class="chart-container">
    <h3>Property Registration Statistics (2019-2023)</h3>
    <canvas id="registrationChart"></canvas>
</div>
<div class="back-home-btn-wrapper">
    <a href="home" class="inline-btn">Back to Home page</a>
</div>

<!-- Add Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('registrationChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['2019', '2020', '2021', '2022', '2023'],
            datasets: [{
                label: 'Number of Properties Registered',
                data: [45000, 38000, 42000, 48000, 52000],
                backgroundColor: 'rgba(20, 108, 67, 0.7)', // Main color
                borderColor: 'rgba(20, 108, 67, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Annual Property Registrations in Jordan',
                    color: '#146C43',
                    font: { size: 18 }
                },
                legend: {
                    display: true,
                    position: 'top',
                    labels: { color: '#146C43' }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Registrations',
                        color: '#146C43'
                    },
                    ticks: { color: '#146C43' },
                    grid: { color: '#e9ecef' }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Year',
                        color: '#146C43'
                    },
                    ticks: { color: '#146C43' },
                    grid: { color: '#e9ecef' }
                }
            }
        }
    });
});
</script>

<style>
.chart-container {
    margin: 16px auto 0 auto; /* Reduced top margin */
    padding: 30px 20px;
    background: #f8f9fa;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(20,108,67,0.08);
    max-width: 1000px;
    width: 95%;
}
.chart-container h3 {
    text-align: center;
    margin-bottom: 20px;
    color: #146C43;
    font-weight: 600;
}
canvas {
    max-width: 100%;
    height: 400px !important;
}
.back-home-btn-wrapper {
    display: flex;
    justify-content: center;
    margin: 30px 0 0 0;
}
/* Remove custom .inline-btn styles to revert to original */
</style>
