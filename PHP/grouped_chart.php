<?php
require_once '../PHP/CRUD.php';
$crud = new CRUD();
$donations = $crud->getDailyDonations();

if (isset($donations['error'])) {
    echo "Error: " . $donations['error'];
    exit;
}

// Prepare data
$categories = [];
$organizations = [];

foreach ($donations as $d) {
    $category = $d['category'] ?? 'Unknown Category';
    $organization = $d['organization'] ?? 'Unknown Organization';

    if (!isset($categories[$category])) {
        $categories[$category] = [];
    }
    if (!isset($categories[$category][$organization])) {
        $categories[$category][$organization] = ['cash' => 0, 'food' => 0, 'item' => 0];
    }

    if (!empty($d['cash_amount']) && $d['cash_amount'] > 0) {
        $categories[$category][$organization]['cash']++;
    }

    if (!empty($d['food_quantity']) && $d['food_quantity'] > 0) {
        $categories[$category][$organization]['food']++;
    }

    if (!empty($d['item_quantity']) && $d['item_quantity'] > 0) {
        $categories[$category][$organization]['item']++;
    }

    if (!in_array($organization, $organizations)) {
        $organizations[] = $organization;
    }
}

// Sort categories and orgs
sort($organizations);
ksort($categories);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Donations by Organization & Category</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light text-dark">
<div class="container py-5">
  <h2 class="mb-4">Donations by Organization & Category</h2>


  <div class="mb-4">
    <label for="orgSelect" class="form-label">Select Organization:</label>
    <select id="orgSelect" class="form-select">
      <?php foreach ($organizations as $org): ?>
        <option value="<?= htmlspecialchars($org) ?>"><?= htmlspecialchars($org) ?></option>
      <?php endforeach; ?>
    </select>
  </div>


  <canvas id="donationChart" height="100"></canvas>


  <a href="index.php" class="btn btn-secondary mt-4">← Back</a>
</div>

<script>
const rawData = <?= json_encode($categories) ?>;
const ctx = document.getElementById('donationChart').getContext('2d');
let currentChart = null;

function renderChart(org) {
  const labels = Object.keys(rawData);
  const cashData = [], foodData = [], itemData = [];

  labels.forEach(cat => {
    const donation = rawData[cat][org] || { cash: 0, food: 0, item: 0 };
    cashData.push(donation.cash);
    foodData.push(donation.food);
    itemData.push(donation.item);
  });

  if (currentChart) currentChart.destroy();

  currentChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Cash Donations (count)',
          data: cashData,
          backgroundColor: '#198754'
        },
        {
          label: 'Food Donations (count)',
          data: foodData,
          backgroundColor: '#ffc107'
        },
        {
          label: 'Item Donations (count)',
          data: itemData,
          backgroundColor: '#0d6efd'
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        tooltip: {
          callbacks: {
            label: function(context) {
              return `${context.dataset.label}: ${context.raw} time(s)`;
            }
          }
        },
        title: {
          display: true,
          text: 'Donation Type Counts by Category'
        },
        legend: {
          position: 'top'
        }
      },
      scales: {
        x: { stacked: false },
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Number of Donations'
          }
        }
      }
    }
  });
}

const select = document.getElementById('orgSelect');
select.addEventListener('change', () => {
  renderChart(select.value);
});


renderChart(select.value);
</script>
</body>
</html>
