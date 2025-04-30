<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Patron</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-secondary bg-opacity-10 text-dark">

<header class="bg-dark text-white py-3 mb-4 text-center">
    <h1 class="h3 mb-0">Registration</h1>
</header>

<div class="container">
    <div class="text-center mb-4">
        <p class="text-muted">Please fill in the details below to register.</p>
    </div>

    <div class="card shadow-sm mx-auto p-4 bg-light" style="max-width: 500px;">
        <form id="registrationForm">
            <div class="mb-3">
                <input type="text" name="name" class="form-control" placeholder="Patron Name" required>
            </div>
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="text" name="contact" class="form-control" placeholder="Contact" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-dark w-100">Register</button>
        </form>

        <div class="text-center mt-3">
            <p>Already have an account? 
                <a href="login.php" class="text-decoration-none">Log in here</a>
            </p>
        </div>
    </div>

    <div class="text-center mt-3">
        <a href="../PHP/welcomePage.php" class="text-decoration-none text-secondary">&larr; Back to Home</a>
    </div>
</div>

<script>
document.getElementById('registrationForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    try {
        const response = await fetch('../SP/registerHandler.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status === 'duplicate') {
            Swal.fire({
                icon: 'error',
                title: 'Email already in use',
                text: 'Please use a different email address.'
            });
        } else if (result.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Registration Successful!',
                text: 'Redirecting...',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = `../CONNECTED/profile.php?patron_id=${result.patron_id}`;
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Registration Failed',
                text: 'Something went wrong.'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Server Error',
            text: 'Please try again later.'
        });
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
