<?php
session_start();
require_once 'dbConnection.php';
require_once 'session.php';
require_once 'CRUD.php';

$session = new Session();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["username"]) && isset($_POST["password"])) {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        try {
            $db = new Database();
            $conn = $db->getConnection();
            $crud = new CRUD($conn);

            $user = $crud->getAdminCredentials($username);

            if (!$user) {
                $user = $crud->getUserCredentials($username);
            }

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION["logged_in"] = true;
                $_SESSION["username"] = $username;
                $_SESSION["role"] = $user['role'];
                
                if ($user['role'] === 'user') {
                    $_SESSION["patron_id"] = $user['patrons_id'];
                } else if ($user['role'] === 'admin') {
                    $_SESSION["admin_id"] = $user['id']; 
                    unset($_SESSION["patron_id"]);
                }
                
                $session->regenerateId();

                if ($user['role'] === 'user') {
                    header("Location: ../CONNECTED/profile.php?patron_id=" . urlencode($user['patrons_id']));
                } else {
                    header("Location: ../PHP/index.php");
                }
                exit();
            } else {
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
