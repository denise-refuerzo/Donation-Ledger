<?php
require_once "CRUD.php";

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? null;
    $email = $_POST['email'] ?? null;
    $contact = $_POST['contact'] ?? null;
    $category = $_POST['category'] ?? '';
    $organization = $_POST['organization'] ?? '';
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;

    if ($anonymous) {
        $name = $email = $contact = null;
    } else {
        $name = trim($name) ?: null;
        $email = trim($email) ?: null;
        $contact = trim($contact) ?: null;
    }

    $crud = new CRUD();
    $result = $crud->addDonation($name, $email, $contact, $category, $organization, $anonymous);

    if ($result === true) {
        $success = "Donation added successfully!";
    } else {
        $error = $result;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Donation</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-secondary bg-opacity-10 text-dark">

    <header class="bg-dark text-white py-3 mb-4 text-center">
        <h1 class="h3 mb-0">Add Donation</h1>
    </header>

    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm mx-auto p-4 bg-light" style="max-width: 500px;">
            <form method="POST">
                <div class="mb-3">
                    <input type="text" name="name" class="form-control" placeholder="Patron Name">
                </div>

                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email">
                </div>

                <div class="mb-3">
                    <input type="text" name="contact" class="form-control" placeholder="Contact">
                </div>

                <div class="mb-3">
                    <select name="category" class="form-select" required>
                        <option value="">Select Category</option>
                        <option value="Item">Item</option>
                        <option value="Food">Food</option>
                        <option value="Cash">Cash</option>
                    </select>
                </div>

                <div class="mb-3">
                    <select name="organization" class="form-select" required>
                        <option value="">Select Organization</option>
                        <option value="Nursing Home">Nursing Home</option>
                        <option value="Homeless Shelter">Homeless Shelter</option>
                        <option value="Natural Disasters">Natural Disasters</option>
                        <option value="Orphanage">Orphanage</option>
                    </select>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="anonymous" class="form-check-input" id="anonymousCheck">
                    <label for="anonymousCheck" class="form-check-label">Donate Anonymously</label>
                </div>

                <button type="submit" class="btn btn-dark w-100">Submit Donation</button>
            </form>
        </div>

        <div class="text-center mt-3">
            <a href="../PHP/index.php" class="text-decoration-none text-secondary">&larr; Back to Home</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
