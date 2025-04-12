<?php
require_once '../PHP/session.php';
$session = new Session();   
if ($session->isLoggedIn()) {
    header("Location: logout.php"); // Redirect to home page if already logged in
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <link rel="stylesheet" href="../CSS/LOGIN.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="wrapper">
        <form action="../PHP/Login.php" method="POST">
            <h1>Login</h1>

            <div class="input-box">
                <input type="text" name="username" placeholder="Username" required />
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required />
            </div>

            <div class="remember-forgot">
                <label><input type="checkbox" /> Remember me </label>
                <a href="#">Forget Password?</a>
            </div>

            <button type="submit" class="button">Login</button>

        </form>
    </div>
    <script>
        // Check if login failed
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('error')) {
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: 'Invalid username or password!',
                confirmButtonColor: '#d33'
            });
        }

        // Check if login was successful
        if (urlParams.has('success')) {
            Swal.fire({
                icon: 'success',
                title: 'Login Successful!',
                text: 'Redirecting to Home Page...',
                timer: 2000, // Auto-close after 2 seconds
                showConfirmButton: false
            });

            // Redirect after delay
            setTimeout(() => {
                window.location.href = '../PHP/Home.php';
            }, 2000);
        }
    </script>
</body>
</html>
