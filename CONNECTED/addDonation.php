<?php
session_start();
require_once "../PHP/CRUD.php";

$success = "";
$error = "";

// Ensure patron_id is set via GET and stored in session if not already done
if (isset($_GET['patron_id'])) {
    $_SESSION['patron_id'] = (int) $_GET['patron_id'];
}
$patron_id = $_SESSION['patron_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once "../PHP/CRUD.php";
    $crud = new CRUD();

    $organization = $_POST['organization'];
    $anonymous = isset($_POST['anonymous']);
    
    $donate_item = isset($_POST['donate_item']);
    $donate_food = isset($_POST['donate_food']);
    $donate_cash = isset($_POST['donate_cash']);

    // Handle categories and values
    try {
        $donation_id_list = [];

        if ($donate_item && !empty($_POST['item_name']) && !empty($_POST['item_qty'])) {
            $donation_id = $crud->addDonationWithID($patron_id, 'Item', $organization, $anonymous, $_POST['item_name'], $_POST['item_qty'], null, null, null);
            $donation_id_list[] = $donation_id;
        }

        if ($donate_food && !empty($_POST['food_kind']) && !empty($_POST['food_qty'])) {
            $donation_id = $crud->addDonationWithID($patron_id, 'Food', $organization, $anonymous, null, null, $_POST['food_kind'], $_POST['food_qty'], null);
            $donation_id_list[] = $donation_id;
        }

        if ($donate_cash && !empty($_POST['cash_amt'])) {
            $donation_id = $crud->addDonationWithID($patron_id, 'Cash', $organization, $anonymous, null, null, null, null, $_POST['cash_amt']);
            $donation_id_list[] = $donation_id;
        }

        if (!empty($donation_id_list)) {
            $success = "Donation(s) successfully added!";
        } else {
            $error = "No valid donation category selected.";
        }
    } catch (Exception $e) {
        $error = "Error processing donation: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Donation</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-secondary bg-opacity-10 text-dark">

    <header class="bg-dark text-white py-3 mb-4 text-center">
        <h1 class="h3 mb-0">Add Donation</h1>
    </header>

    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm mx-auto p-4 bg-light" style="max-width: 500px;">
            <form method="POST">

                <div class="mb-3">
                    <input list="orgList" name="organization" class="form-control" placeholder="Select or enter an organization" required>
                    <datalist id="orgList"></datalist>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="anonymous" class="form-check-input" id="anonymousCheck">
                    <label for="anonymousCheck" class="form-check-label">Donate Anonymously</label>
                </div>

                <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="itemCheck" name="donate_item">
                <label class="form-check-label" for="itemCheck">Donate Item</label>
            </div>
            <div class="mb-2 d-none" id="itemFields">
                <input type="text" class="form-control mb-1" name="item_name" placeholder="Item Name">
                <input type="number" class="form-control" name="item_qty" placeholder="Quantity">
            </div>

            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="foodCheck" name="donate_food">
                <label class="form-check-label" for="foodCheck">Donate Food</label>
            </div>
            <div class="mb-2 d-none" id="foodFields">
                <input type="text" class="form-control mb-1" name="food_kind" placeholder="Food Kind">
                <input type="number" class="form-control" name="food_qty" placeholder="Quantity">
            </div>

            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="cashCheck" name="donate_cash">
                <label class="form-check-label" for="cashCheck">Donate Cash</label>
            </div>
            <div class="mb-2 d-none" id="cashFields">
                <input type="number" step="0.01" class="form-control" name="cash_amt" placeholder="Amount">
            </div>

                <button type="submit" class="btn btn-dark w-100">Submit Donation</button>
            </form>
        </div>

        <div class="text-center mt-3">
            <a href="../CONNECTED/profile.php?patron_id=<?= urlencode($patron_id) ?>" class="text-decoration-none text-secondary">&larr; Back to Profile</a>
        </div>

    </div>
    <script>
            document.addEventListener('DOMContentLoaded', function () {
            fetch('../SP/getOrganizations.php')
                .then(res => res.json())
                .then(data => {
                    const datalist = document.getElementById('orgList');
                    datalist.innerHTML = ''; // Clear just in case
                    data.forEach(org => {
                        const option = document.createElement('option');
                        option.value = org.organization;
                        datalist.appendChild(option);
                    });
                })
                .catch(err => console.error('Failed to load organizations:', err));
            });
    </script>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toggleSection = (checkId, sectionId, requiredFields = []) => {
            const checkbox = document.getElementById(checkId);
            const section = document.getElementById(sectionId);

            checkbox.addEventListener('change', function () {
                const checked = this.checked;
                section.classList.toggle('d-none', !checked);

                requiredFields.forEach(fieldName => {
                    const field = document.querySelector(`[name="${fieldName}"]`);
                    if (field) {
                        field.required = checked;

                        // 🧽 Clear the field if unchecked
                        if (!checked) {
                            field.value = "";
                        }
                    }
                });
            });
        };

        toggleSection('itemCheck', 'itemFields', ['item_name', 'item_qty']);
        toggleSection('foodCheck', 'foodFields', ['food_kind', 'food_qty']);
        toggleSection('cashCheck', 'cashFields', ['cash_amt']);
    </script>


</body>
</html>
