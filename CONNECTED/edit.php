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
  $patron_id = intval($_GET['patron_id']);
  $name = $_POST['name'] ?? null;
  $email = $_POST['email'] ?? null;
  $contact = $_POST['contact'] ?? null;

  $update = $crud->editPatron($patron_id, $name, $email, $contact);
  if ($update !== true) {
      echo json_encode(['error' => $update['error']]);
      exit;
  }

  // Success - just return a 200 response
  echo 'success';
  exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Patron</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light text-dark">

  <div class="container my-5">
    <h2 class="mb-4">Edit Patron Information</h2>

    <form id="editPatronForm" method="POST">
    <input type="hidden" name="edit_patron" value="1">
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
      <button type="button" id="savePatronBtn" class="btn btn-primary">Save Changes</button>
  </form>

    <a href="profile.php?patron_id=<?= urlencode($_GET['patron_id']) ?>" class="btn btn-secondary mt-4">&larr; Back to Profile</a>
  </div>
  <script>
    document.getElementById("savePatronBtn").addEventListener("click", function () {
      const form = document.getElementById("editPatronForm");
      const formData = new FormData(form);

      fetch(window.location.href, {
        method: "POST",
        body: formData
      })
      .then(response => response.text())
      .then(result => {
        if (result.trim() === "success") {
          Swal.fire({
            title: 'Saved!',
            text: 'Changes have been saved successfully.',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
          }).then(() => {
            const urlParams = new URLSearchParams(window.location.search);
            const patronId = urlParams.get('patron_id');
            window.location.href = `profile.php?patron_id=${encodeURIComponent(patronId)}`;
          });
        } else {
          console.error('Server response:', result);
          Swal.fire({
            title: 'Error',
            text: 'Failed to save changes. Server returned an error.',
            icon: 'error'
          });
        }
      })
      .catch(error => {
        console.error('Fetch error:', error);
        Swal.fire({
          title: 'Error',
          text: 'An error occurred while sending the data.',
          icon: 'error'
        });
      });
    });
    </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
