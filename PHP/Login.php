<?php
session_start();
require_once 'session.php'; // Include the session class
$session = new Session();

$admin_username = "admin";
$admin_password = "password123";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["username"]) && isset($_POST["password"])) {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        if ($username === $admin_username && $password === $admin_password) {
            $_SESSION["admin"] = $username;
            header("Location: ../HTML/login_view.php?success=1");
            exit();
        } else {
            header("Location: ../HTML/login_view.php?error=1"); 
            exit();
        }
    }
}
header("Location: ../HTML/login_view.php");
exit();
?>
