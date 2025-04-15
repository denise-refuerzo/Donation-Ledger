<?php
session_start();
require_once 'dbConnection.php';
require_once 'session.php';

$session = new Session();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["username"]) && isset($_POST["password"])) {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        try {
            $db = new Database();
            $conn = $db->getConnection();

            // Call the stored procedure to get the hashed password
            $stmt = $conn->prepare("CALL get_user_credentials(:p_username)");
            $stmt->bindParam(':p_username', $username);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION["logged_in"] = true;
                $_SESSION["username"] = $username;
                header("Location: ../PHP/index.php");
                exit();
            } else {
                // Invalid credentials
                header("Location: ../PHP/login_view.php?error=1");
                exit();
            }
        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    }
}
header("Location: ../PHP/login_view.php");
exit();
?>
