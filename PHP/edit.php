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

$patron = $info[0]; // Get the patron info

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_patron'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact = $_POST['contact'] ?? '';

    $editResult = $crud->editPatron($_GET['patron_id'], $name, $email, $contact);
    if ($editResult === true) {
        header("Location: ../PHP/profile.php?patron_id=" . $_GET['patron_id'] . "&updated=1");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Error: " . $editResult['error'] . "</div>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Patron</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light text-dark">

  <div class="container my-5">
    <h2 class="mb-4">Edit Patron Information</h2>

    <form method="POST">
      <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($patron['name']) ?>" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($patron['email']) ?>" required>
      </div>
      <div class="mb-3">
        <label for="contact" class="form-label">Contact</label>
        <input type="text" class="form-control" id="contact" name="contact" value="<?= htmlspecialchars($patron['contact']) ?>" required>
      </div>
      <button type="submit" name="edit_patron" class="btn btn-primary">Save Changes</button>
    </form>

    <a href="profile.php?patron_id=<?= urlencode($_GET['patron_id']) ?>" class="btn btn-secondary mt-4">&larr; Back to Profile</a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
