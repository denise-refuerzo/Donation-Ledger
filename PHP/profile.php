<?php
require_once '../PHP/CRUD.php';

if (!isset($_GET['patron_id'])) {
    echo "No patron selected.";
    exit;
}

$crud = new CRUD();
$info = $crud->getPatronInfo($_GET['patron_id']);

if (isset($info['error'])) {
    echo "Database error: " . $info['error'];
    exit;
}

if (empty($info)) {
    echo "No information found for this patron.";
    exit;
}

$patron = $info[0]; // basic patron info

// Group donations by timestamp
$groupedDonations = [];
foreach ($info as $donation) {
    if (!empty($donation['timestamp'])) {
        $timestamp = $donation['timestamp'];
        if (!isset($groupedDonations[$timestamp])) {
            $groupedDonations[$timestamp] = [];
        }
        $groupedDonations[$timestamp][] = $donation;
    }
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_patron'])) {
    $deleteResult = $crud->deletePatron($_GET['patron_id']);
    if ($deleteResult === true) {
        header("Location: ../PHP/index.php?deleted=1");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Error: " . $deleteResult['error'] . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Patron Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light text-dark">

  <div class="container my-5">
    <h2 class="mb-4">Patron Profile</h2>

    <!-- Patron Info -->
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title"><?= htmlspecialchars($patron['name'] ?? 'Anonymous') ?></h5>
        <?php if (!empty($patron['name'])): ?>
          <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($patron['email']) ?></p>
          <p class="mb-0"><strong>Contact:</strong> <?= htmlspecialchars($patron['contact']) ?></p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Edit Patron Button -->
    <a href="edit.php?patron_id=<?= urlencode($_GET['patron_id']) ?>" class="btn btn-primary mb-4">Edit Patron</a>

    <!-- Delete Patron Button -->
    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this patron and all their donations and related data?');">
      <input type="hidden" name="delete_patron" value="1">
      <button type="submit" class="btn btn-danger mb-4">Delete Patron</button>
    </form>

    <!-- Donation History -->
    <?php if (!empty($groupedDonations)): ?>
      <h4>Donation History</h4>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>Date</th>
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
            <?php foreach ($groupedDonations as $timestamp => $donations): ?>
              <?php foreach ($donations as $donation): ?>
                <tr>
                  <td><?= htmlspecialchars($donation['timestamp'] ?? '-') ?></td>
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
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    <!-- Back Button -->
    <a href="../PHP/index.php" class="btn btn-secondary">&larr; Back</a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
