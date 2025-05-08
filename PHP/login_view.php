<?php
require_once '../PHP/session.php';
$session = new Session();   
if ($session->isLoggedIn()) {
    header("Location: ../PHP/index.php"); // Redirect to home page if already logged in
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Log in</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-secondary bg-opacity-15">

  <div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="card p-4 shadow-lg bg-dark text-white" style="max-width: 400px; width: 100%;">
      <h2 class="text-center mb-4">Login</h2>

      <?php if (isset($_GET['deleted']) && $_GET['deleted'] == '1'): ?>
      <div class="alert alert-success alert-dismissible fade show mb-4">
        Account successfully deleted.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php endif; ?>

      <form action="../PHP/Login.php" method="POST">
        <div class="mb-3">
          <input type="text" class="form-control bg-light text-dark" name="username" placeholder="Username" required />
        </div>

        <div class="mb-3">
          <input type="password" class="form-control bg-light text-dark" name="password" placeholder="Password" required />
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="rememberMe">
            <label class="form-check-label text-white" for="rememberMe">Remember me</label>
          </div>
          <a href="#" class="small text-light text-decoration-none">Forget Password?</a>
        </div>
        <div class="text-center mt-3">
            <p>Don't have an account? 
                <a href="Register.php" class="text-decoration-none">Click here</a>
            </p>
        </div>

        <button type="submit" class="btn btn-light text-dark fw-bold w-100">Login</button>
      </form>
    </div>
  </div>

  <script>
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error')) {
      Swal.fire({
        icon: 'error',
        title: 'Login Failed',
        text: 'Invalid username or password!',
        confirmButtonColor: '#d33'
      });
    }

    if (urlParams.has('success')) {
      Swal.fire({
        icon: 'success',
        title: 'Login Successful!',
        text: 'Redirecting to Home Page...',
        timer: 2000,
        showConfirmButton: false
      });

      setTimeout(() => {
        window.location.href = '../PHP/index.php';
      }, 2000);
    }
  </script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
