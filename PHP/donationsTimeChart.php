<?php
// filepath: c:\xampp\htdocs\Donation-Ledger\PHP\donations_time_chart.php
require_once 'CRUD.php';

$crud = new CRUD();
$donationsOverTime = $crud->getDonationsOverTime();

// Check for errors
if (isset($donationsOverTime['error'])) {
    $error = $donationsOverTime['error'];
    $donationsOverTime = [];
}

// Process the data for the chart
$dates = array_column($donationsOverTime, 'donation_date');
$amounts = array_column($donationsOverTime, 'total_amount');

// Calculate total and average
$totalAmount = !empty($amounts) ? array_sum($amounts) : 0;
$averageAmount = !empty($amounts) ? $totalAmount / count($amounts) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donations Over Time</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #ced4da;
            overflow-x: hidden;
        }
        .card {
            background-color: #dee2e6;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #343a40;
        }
        .chart-toggle {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <!-- Back Button -->
        <a onclick="history.back()" 
           class="btn btn-link text-dark text-decoration-none position-absolute top-0 start-0 m-3" 
           role="button" 
           title="Go Back">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-left me-1" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            Back
        </a>

        <!-- Page Header -->
        <div class="text-center mb-3 animate__animated animate__fadeIn">
            <h1 class="display-5 text-secondary">Donations Over Time</h1>
        </div>
        
        <!-- Chart Type Toggle -->
        <div class="chart-toggle mb-3">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-secondary active" id="lineChartBtn">Line Chart</button>
                <button type="button" class="btn btn-secondary" id="barChartBtn">Bar Chart</button>
            </div>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center animate__animated animate__fadeIn">
                Error fetching donation data: <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <!-- Chart -->
        <div class="card p-3 mb-3 animate__animated animate__fadeIn">
            <div style="height: 400px;"> <!-- Fixed height container -->
                <canvas id="donationsChart"></canvas>
            </div>
        </div>
        
        <!-- Consolidated Stats Cards -->
        <div class="row justify-content-center g-3">
            <div class="col-md-3">
                <div class="card text-center p-3 animate__animated animate__fadeIn">
                    <h5 class="text-muted small mb-1">Total Donations</h5>
                    <h3 class="text-dark mb-0">₱<?= number_format($totalAmount, 2) ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3 animate__animated animate__fadeIn">
                    <h5 class="text-muted small mb-1">Average Daily</h5>
                    <h3 class="text-dark mb-0">₱<?= number_format($averageAmount, 2) ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3 animate__animated animate__fadeIn">
                    <h5 class="text-muted small mb-1">Highest Daily</h5>
                    <h3 class="text-dark mb-0">₱<?= !empty($amounts) ? number_format(max($amounts), 2) : '0.00' ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3 animate__animated animate__fadeIn">
                    <h5 class="text-muted small mb-1">Days with Donations</h5>
                    <h3 class="text-dark mb-0"><?= count($dates) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Chart configuration
        let chartType = 'line';
        let chart;
        
        // Data from PHP
        const dates = <?= json_encode($dates) ?>;
        const amounts = <?= json_encode(array_map('floatval', $amounts)) ?>;
        
        // Create the chart
        function createChart() {
            const ctx = document.getElementById('donationsChart').getContext('2d');
            
            // Destroy existing chart if it exists
            if (chart) {
                chart.destroy();
            }
            
            // Create new chart
            chart = new Chart(ctx, {
                type: chartType,
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Daily Cash Donations (₱)',
                        data: amounts,
                        borderColor: '#343a40',
                        backgroundColor: chartType === 'line' ? 'rgba(108,117,125,0.2)' : 'rgba(108,117,125,0.7)',
                        borderWidth: 2,
                        tension: 0.3,
                        pointRadius: chartType === 'line' ? 4 : 0,
                        pointHoverRadius: chartType === 'line' ? 6 : 0,
                        fill: chartType === 'line',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('en-PH', { 
                                            style: 'currency', 
                                            currency: 'PHP' 
                                        }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Amount (₱)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Date of Donation'
                            },
                            ticks: {
                                autoSkip: true,
                                maxRotation: 45,
                                minRotation: 30
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    }
                }
            });
        }
        
        // Initial chart creation
        document.addEventListener('DOMContentLoaded', function() {
            createChart();
            
            // Toggle between chart types
            document.getElementById('lineChartBtn').addEventListener('click', function() {
                this.classList.add('active');
                document.getElementById('barChartBtn').classList.remove('active');
                chartType = 'line';
                createChart();
            });
            
            document.getElementById('barChartBtn').addEventListener('click', function() {
                this.classList.add('active');
                document.getElementById('lineChartBtn').classList.remove('active');
                chartType = 'bar';
                createChart();
            });
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>