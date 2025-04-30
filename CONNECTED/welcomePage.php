<?php
require_once '../PHP/CRUD.php';

$crud = new CRUD(); // No need to pass $pdo
$cashData = $crud->getCashDonations();
$total = $crud->getTotalDonations()['total'];
$categorySummary = $crud->getCategorySummary();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
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
        <!-- Fixed Position Account Icon Button -->
        <a onclick="history.back()" 
        class="btn btn-link text-dark text-decoration-none position-absolute top-0 end-0 m-3" 
        role="button" 
        title="Back to Account">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-fill me-1" viewBox="0 0 16 16">
                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
            </svg>
            Account
        </a>


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
    </script>
</body>
</html>
