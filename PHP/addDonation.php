<?php
require_once "../PHP/dbConnection.php";

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $category = $_POST['category'] ?? '';
    $organization = $_POST['organization'] ?? '';

    try {
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("CALL add_full_donation(:p_name, :p_email, :p_contact, :p_category, :p_organization)");
        $stmt->bindParam(':p_name', $name);
        $stmt->bindParam(':p_email', $email);
        $stmt->bindParam(':p_contact', $contact);
        $stmt->bindParam(':p_category', $category);
        $stmt->bindParam(':p_organization', $organization);

        $stmt->execute();
        $success = "Donation added successfully!";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Donation</title>
    <link rel="stylesheet" href="../CSS/home.css">
    <style>
        .donation-form {
            display: flex;
            flex-direction: column;
            max-width: 400px;
            margin: 30px auto;
            gap: 10px;
        }

        .message {
            text-align: center;
            color: green;
        }

        .error {
            text-align: center;
            color: red;
        }

        header {
            text-align: center;
            padding: 1rem;
        }

        a {
            text-decoration: none;
            color: #007BFF;
            display: block;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Add Donation</h1>
    </header>

    <?php if ($success): ?>
        <p class="message"><?= htmlspecialchars($success) ?></p>
    <?php elseif ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" class="donation-form">
        <input type="text" name="name" placeholder="Patron Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="contact" placeholder="Contact" required>

        <select name="category" required>
            <option value="">Select Category</option>
            <option value="Item">Item</option>
            <option value="Food">Food</option>
            <option value="Cash">Cash</option>
        </select>

        <select name="organization" required>
            <option value="">Select Organization</option>
            <option value="Nursing Home">Nursing Home</option>
            <option value="Homeless Shelter">Homeless Shelter</option>
            <option value="Natural Disasters">Natural Disasters</option>
            <option value="Orphanage">Orphanage</option>
        </select>

        <button type="submit">Submit Donation</button>
    </form>

    <a href="../PHP/Home.php">← Back to Home</a>
</body>
</html>
