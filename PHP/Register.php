<?php
require_once '../PHP/CRUD.php';

$crud = new CRUD();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $password = $_POST['password'] ?? '';

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $editResult = $crud->RegisterPatron($name, $email, $contact, $hashedPassword);
    if ($editResult === true) {
        echo 'success';
        header('Location: ../PHP/index.php');
    } else {
        echo 'error';
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
        <h1 class="h3 mb-0">Registration</h1>
    </header>

    <div class="container">
        <div class="text-center mb-4">
            <!-- <h2 class="h5">Register</h2> -->
            <p class="text-muted">Please fill in the details below to register.</p>
        </div>

        <div class="card shadow-sm mx-auto p-4 bg-light" style="max-width: 500px;">
            <form method="POST">
                <div class="mb-3">
                    <input type="text" name="name" class="form-control" placeholder="Patron Name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <input type="text" name="contact" class="form-control" placeholder="Contact" value="<?= htmlspecialchars($_POST['contact'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" value="<?= htmlspecialchars($_POST['password'] ?? '') ?>">
                </div>

                <!-- <div class="mb-3">
                    <input list="orgList" name="organization" class="form-control" placeholder="Select or enter an organization" required>
                    <datalist id="orgList"></datalist>
                </div> -->
                <button type="submit" class="btn btn-dark w-100">Register</button>
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