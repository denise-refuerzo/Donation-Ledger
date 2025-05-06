<?php
require_once '../PHP/CRUD.php';

$crud = new CRUD(); // No need to pass $pdo
$cashData = $crud->getCashDonations();
$total = $crud->getTotalDonations()['total'];
$categorySummary = $crud->getCategorySummary();


$donationCounts = array_map('intval', array_column($categorySummary, 'donation_count'));
$donationLabels = array_column($categorySummary, 'category');
$totalDonations = array_sum($donationCounts);
$donationPercentages = array_map(function($count) use ($totalDonations) {
    return round(($count / $totalDonations) * 100, 2);
}, $donationCounts);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <style>
        body {
            background-color: #ced4da;
            overflow-x: hidden;
        }
        .card {
            background-color: #dee2e6;
            border: none;
            word-wrap: break-word;
        }
        .header-btn { position: absolute; top: 20px; right: 30px; }
        .bg-dark {
            margin-top: 3rem;
        }
        #noticeBox {
            position: fixed;
            top: 15px;
            left: 30%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 600px;
            z-index: 1055;
        }


    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- Register Button -->
        <div class="d-flex justify-content-end mb-3">
            <a href="register.php" class="btn btn-dark">Register</a>
        </div>

        <div id="noticeBox" class="animate__animated animate__fadeInDown">
            <div class="alert alert-warning alert-dismissible text-center shadow" role="alert">
                <strong>Want to donate?</strong> Click the <a href="register.php" class="alert-link">Register</a> button to get started!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>



        <!-- Welcome Banner -->
        <div class="text-center mb-5">
            <h1 class="display-4 text-secondary">Welcome to Donation Ledger</h1>
            <p class="lead text-muted">Track and manage donations with ease.</p>
        </div>

        <!-- Statistics Section -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-4">
                <div class="card text-center p-4 shadow-sm">
                    <h5 class="text-muted">Total Donations Collected</h5>
                    <h2 class="text-dark"><?= $total ?></h2>
                </div>
            </div>
        </div>

        <!-- Graph Section -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card p-3 shadow-sm">
                    <h5 class="text-muted text-center mb-3">Cash Donations Over Time</h5>
                    <canvas id="cashChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="card p-3 shadow-sm">
                <h5 class="text-muted text-center mb-3">Donations by Category</h5>
                <canvas id="categoryBarChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- pie chart -->
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">
            <div class="card p-5 shadow-sm">
                <h5 class="text-muted text-center mb-3">Donation Category Distribution</h5>
                <canvas id="categoryPieChart" height="150"></canvas>
            </div>
        </div>
    </div>



    <!-- About Us Section -->
    <div class="bg-dark text-white py-4 mt-5 w-100">
        <div class="container px-4">
            <h4 class="text-center mb-3">About Us</h4>
            <p class="text-center small mb-2">
                Donation Ledger is a transparent platform to track and manage donations. Our mission is to make giving more accessible and accountable.
            </p>
            <p class="text-center small mb-1"><strong>Email:</strong> contact@donationledger.org</p>
            <p class="text-center small mb-1"><strong>Phone:</strong> +123 456 7890</p>
            <p class="text-center small"><strong>Address:</strong> 123 Donation St., Charity City, Country</p>
        </div>
    </div>


<!-- Add Bootstrap Bundle (includes Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const box = document.getElementById('noticeBox');
            box.classList.add('animate__animated', 'animate__fadeInDown');
        });
        // Line Chart: Individual Cash Donations
        const ctx = document.getElementById('cashChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($cashData, 'donation_date')) ?>,
                datasets: [{
                    label: 'Individual Cash Donations (₱)',
                    data: <?= json_encode(array_column($cashData, 'amount')) ?>,
                    borderColor: '#343a40',
                    backgroundColor: 'rgba(108,117,125,0.2)',
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Amount (₱)' }
                    },
                    x: {
                        title: { display: true, text: 'Date of Donation' },
                        ticks: {
                            autoSkip: true,
                            maxRotation: 45,
                            minRotation: 30
                        }
                    }
                }
            }
        });

        // Bar Chart: Summary by Category
        const ctxBar = document.getElementById('categoryBarChart').getContext('2d');
        const barChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($categorySummary, 'category')) ?>,
                datasets: [{
                    label: 'Total Donations',
                    data: <?= json_encode(array_map('intval', array_column($categorySummary, 'donation_count'))) ?>,  // Change 'total' to 'donation_count'
                    backgroundColor: ['#adb5bd', '#6c757d', '#343a40'],
                    borderColor: '#495057',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Number of Donations' }  // Change text to 'Number of Donations'
                    },
                    x: {
                        title: { display: true, text: 'Category' }
                    }
                }
            }
        });

        //for pie chart
       // Pie Chart: Category Distribution with Percentages
        const ctxPie = document.getElementById('categoryPieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: <?= json_encode($donationLabels) ?>,
                datasets: [{
                    data: <?= json_encode($donationCounts) ?>,
                    backgroundColor: ['#adb5bd', '#6c757d', '#343a40', '#f8f9fa', '#e9ecef'],  // Add more if needed
                    borderColor: '#fff',
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    datalabels: {
                        color: '#fff',
                        formatter: (value, context) => {
                            const total = context.chart._metasets[0].total;
                            const percent = ((value / total) * 100).toFixed(1);
                            return percent + '%';
                        },
                        font: {
                            weight: 'bold',
                            size: 14
                        }
                    },
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed;
                                const label = context.label;
                                const percentage = (value / <?= $totalDonations ?> * 100).toFixed(2);
                                return `${label}: ${value} donations (${percentage}%)`;
                            }
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

    

    </script>
</body>
</html>
