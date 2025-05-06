<?php
require_once '../PHP/CRUD.php';

$crud = new CRUD();
$donations = $crud->getDailyDonations();

if (isset($donations['error'])) {
    echo "Error: " . $donations['error'];
    exit;
}

// Group donations by date
$grouped = [];
foreach ($donations as $d) {
    $date = date('Y-m-d', strtotime($d['timestamp']));
    $grouped[$date][] = $d;
}

// Sort by newest first
ksort($grouped);
$grouped = array_reverse($grouped);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Daily Donations</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .donation-group { margin-bottom: 2rem; }
    .pagination-controls { display: flex; justify-content: center; gap: 10px; margin-top: 20px; }
  </style>
</head>
<body class="bg-light text-dark">
<div class="container my-5">
  <h2 class="mb-4">Daily Donations</h2>

  <!-- Search Bar -->
  <div class="mb-4">
    <input type="text" id="dateSearch" class="form-control" placeholder="Search by date (e.g. January 10, 2025)">
  </div>

  <div id="donationContainer">
    <?php foreach ($grouped as $date => $donations): ?>
      <?php $formattedDate = date('F j, Y', strtotime($date)); ?>
      <div class="donation-group" data-date="<?= htmlspecialchars(strtolower($formattedDate)) ?>">
        <h5 class="mt-4"><?= htmlspecialchars($formattedDate) ?></h5>
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead class="table-dark">
              <tr>
                <th>Timestamp</th>
                <th>Category</th>
                <th>Organization</th>
                <th>Cash Amount</th>
                <th>Food Kind</th>
                <th>Food Quantity</th>
                <th>Item Name</th>
                <th>Item Quantity</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($donations as $donation): ?>
                <tr>
                  <td><?= htmlspecialchars($donation['timestamp']) ?></td>
                  <td><?= htmlspecialchars($donation['category'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($donation['organization'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($donation['cash_amount'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($donation['food_kind'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($donation['food_quantity'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($donation['item_name'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($donation['item_quantity'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($donation['status'] ?? '-') ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Pagination Controls -->
  <div class="pagination-controls">
    <button class="btn btn-outline-primary" id="prevPage">Previous</button>
    <span id="pageIndicator" class="align-self-center"></span>
    <button class="btn btn-outline-primary" id="nextPage">Next</button>
  </div>

  <!-- Back Button -->
  <button onclick="history.back()" class="btn btn-secondary mt-4">&larr; Back</button>
</div>

<!-- JS -->
<script>
const groups = document.querySelectorAll('.donation-group');
const groupsPerPage = 2;
let currentPage = 1;

function showPage(page) {
  const start = (page - 1) * groupsPerPage;
  const end = start + groupsPerPage;

  groups.forEach((group, index) => {
    group.style.display = (index >= start && index < end) ? '' : 'none';
  });

  document.getElementById('pageIndicator').textContent = `Page ${page} of ${Math.ceil(groups.length / groupsPerPage)}`;
  document.getElementById('prevPage').disabled = page === 1;
  document.getElementById('nextPage').disabled = end >= groups.length;
}

document.getElementById('prevPage').addEventListener('click', () => {
  if (currentPage > 1) {
    currentPage--;
    showPage(currentPage);
  }
});

document.getElementById('nextPage').addEventListener('click', () => {
  if ((currentPage * groupsPerPage) < groups.length) {
    currentPage++;
    showPage(currentPage);
  }
});

document.getElementById('dateSearch').addEventListener('input', function () {
  const query = this.value.toLowerCase();
  let visibleCount = 0;

  groups.forEach(group => {
    const date = group.getAttribute('data-date');
    const match = date.includes(query);
    group.style.display = match ? '' : 'none';
    if (match) visibleCount++;
  });

  // Hide pagination if searching
  document.querySelector('.pagination-controls').style.display = query ? 'none' : 'flex';
  if (!query) showPage(currentPage);
});

// Init first page
showPage(currentPage);
</script>

</body>
</html>
