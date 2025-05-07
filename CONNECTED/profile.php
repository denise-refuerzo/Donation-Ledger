<?php
require_once '../PHP/CRUD.php';

if (!isset($_GET['patron_id'])) {
    echo "No patron selected.";
    exit;
}

$patron_id = intval($_GET['patron_id']);
$crud = new CRUD();
$info = $crud->getPatronInfo($patron_id);

if (isset($info['error'])) {
    echo "Database error: " . $info['error'];
    exit;
}

if (empty($info)) {
    echo "No information found for this patron.";
    exit;
}

$patron = $info[0]; // Basic patron info

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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    <a href="edit.php?patron_id=<?= urlencode($_GET['patron_id']) ?>" class="btn btn-primary mb-2">Edit Patron</a>

    <!-- Donate Button -->
    <a href="addDonation.php?patron_id=<?= urlencode($_GET['patron_id']) ?>" class="btn btn-success mb-2 ms-2">Donate</a>

    <!-- Delete Patron Button -->
    <form id="deletePatronForm" method="POST" class="d-inline">
      <input type="hidden" name="delete_patron" value="1">
      <button type="button" id="deletePatronBtn" class="btn btn-danger mb-2 ms-2">Delete Patron</button>
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

    <div class="d-flex justify-content-end mb-3">
      <a href="../PHP/logout.php" class="btn btn-outline-danger">Logout</a>
    </div>


    <!-- Back Button -->
    <a href="../CONNECTED/welcomePage.php" class="btn btn-secondary">&larr; Back</a>
  </div>

  <script>
    document.getElementById('deletePatronBtn').addEventListener('click', function () {
      Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the patron and all related donations.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(window.location.href, {
            method: 'POST',
            body: new FormData(document.getElementById('deletePatronForm'))
          })
          .then(response => response.text())
          .then(data => {
            Swal.fire({
              title: 'Deleted!',
              text: 'The patron has been deleted.',
              icon: 'success',
              timer: 2000,
              showConfirmButton: false
            }).then(() => {
              window.location.href = '../PHP/login_view.php'; // or welcomePage.php
            });
          })
          .catch(error => {
            Swal.fire('Error', 'Something went wrong.', 'error');
          });
        }
      });
    });

  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
