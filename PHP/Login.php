<?php
session_start();

$admin_username = "admin";
$admin_password = "password123";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["username"]) && isset($_POST["password"])) {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        if ($username === $admin_username && $password === $admin_password) {
            $_SESSION["admin"] = $username;
            header("Location: ../HTML/Login.html?success=1");
            exit();
        } else {
            header("Location: ../HTML/Login.html?error=1"); 
            exit();
        }
    }
}
header("Location: ../HTML/Login.html");
exit();
?>
