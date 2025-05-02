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

            $user = null;

            // Check in admin table
            $stmt = $conn->prepare("CALL get_admin_credentials(:p_username)");
            $stmt->bindParam(':p_username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                // Check in user table
                $stmt = $conn->prepare("CALL get_user_credentials(:p_email)");
                $stmt->bindParam(':p_email', $username);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION["logged_in"] = true;
                $_SESSION["username"] = $username;
                $_SESSION["role"] = $user['role'];

                // Only set patron_id for users
                if ($user['role'] === 'user') {
                    $_SESSION["patron_id"] = $user['patrons_id'];
                }

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: ../PHP/index.php");
                } else {
                    header("Location: ../PHP/index.php");
                }
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
