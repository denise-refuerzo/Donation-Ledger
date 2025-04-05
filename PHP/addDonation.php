<?php
require_once "../PHP/dbConnection.php";

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? null;
    $email = $_POST['email'] ?? null;
    $contact = $_POST['contact'] ?? null;
    $category = $_POST['category'] ?? '';
    $organization = $_POST['organization'] ?? '';
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;

    // ✅ Sanitize inputs and handle anonymous
    if ($anonymous) {
        $name = null;
        $email = null;
        $contact = null;
    } else {
        $name = trim($name) ?: null;
        $email = trim($email) ?: null;
        $contact = trim($contact) ?: null;
    }

    try {
        $db = new Database();
        $conn = $db->getConnection();

        // ✅ Call updated stored procedure
        $stmt = $conn->prepare("CALL add_full_donation(:p_name, :p_email, :p_contact, :p_category, :p_organization, :p_anonymous)");
        $stmt->bindParam(':p_name', $name);
        $stmt->bindParam(':p_email', $email);
        $stmt->bindParam(':p_contact', $contact);
        $stmt->bindParam(':p_category', $category);
        $stmt->bindParam(':p_organization', $organization);
        $stmt->bindParam(':p_anonymous', $anonymous, PDO::PARAM_BOOL);

        $stmt->execute();

        // ✅ Clear additional result sets to prevent errors
        while ($stmt->nextRowset()) {;}

        $success = "Donation added successfully!";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!-- ✅ HTML remains mostly unchanged -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Donation</title>
    <link rel="stylesheet" href="../CSS/home.css">
    <style>
        .donation-form { display: flex; flex-direction: column; max-width: 400px; margin: 30px auto; gap: 10px; }
        .message, .error { text-align: center; }
        .message { color: green; }
        .error { color: red; }
        header { text-align: center; padding: 1rem; }
        a { text-decoration: none; color: #007BFF; display: block; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <header><h1>Add Donation</h1></header>

    <?php if ($success): ?><p class="message"><?= htmlspecialchars($success) ?></p><?php endif; ?>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <form method="POST" class="donation-form">
        <input type="text" name="name" placeholder="Patron Name">
        <input type="email" name="email" placeholder="Email">
        <input type="text" name="contact" placeholder="Contact">

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

        <label><input type="checkbox" name="anonymous"> Donate Anonymously</label>

        <button type="submit">Submit Donation</button>
    </form>

    <a href="../PHP/Home.php">&larr; Back to Home</a>
</body>
</html>
