<?php
require_once 'session.php';
$session = new Session();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Unauthorized Access</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="row">
      <div class="col-md-6 mx-auto">
        <div class="card border-danger">
          <div class="card-header bg-danger text-white">
            <h4>Unauthorized Access</h4>
          </div>
          <div class="card-body">
            <p class="card-text">You don't have permission to access this patron's profile.</p>
            <?php if ($session->isLoggedIn()): ?>
              <?php if ($session->getRole() === 'user'): ?>
                <a href="../CONNECTED/profile.php?patron_id=<?= $session->getPatronId() ?>" class="btn btn-primary">Go to Your Profile</a>
              <?php else: ?>
                <a href="../PHP/index.php" class="btn btn-primary">Go to Dashboard</a>
              <?php endif; ?>
            <?php else: ?>
              <a href="../PHP/login_view.php" class="btn btn-primary">Login</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>