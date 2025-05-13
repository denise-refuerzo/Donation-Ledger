<?php
session_start();
require_once "../PHP/CRUD.php";
require_once "../PHP/session.php";

$session = new Session();

if (!$session->isLoggedIn()) {
    header("Location: ../PHP/login_view.php?error=login_required");
    exit;
}
if ($session->getRole() !== 'admin') {
    header("Location: ../PHP/unauthorized.php");
    exit;
}

if (isset($_GET['clear'])) {
    unset($_SESSION['viewed_patron_id']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['patron_id'])) {
    $patron_id = (int)$_GET['patron_id'];
    $_SESSION['viewed_patron_id'] = $patron_id;
} else {
    $patron_id = $_SESSION['viewed_patron_id'] ?? null;
}

$patronName = '';
if ($patron_id) {
    $crud = new CRUD();
    $patronInfo = $crud->getPatronInfo($patron_id);
    if (!empty($patronInfo) && isset($patronInfo[0]['name'])) {
        $patronName = $patronInfo[0]['name'];
    }
}

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categories = [];
    
    // Collect form data for each donation type
    if (isset($_POST['donate_item'])) {
        $categories[] = ['category' => 'Item', 'item_name' => $_POST['item_name'], 'item_qty' => $_POST['item_qty']];
    }

    if (isset($_POST['donate_food'])) {
        $categories[] = ['category' => 'Food', 'food_kind' => $_POST['food_kind'], 'food_qty' => $_POST['food_qty']];
    }

    if (isset($_POST['donate_cash'])) {
        $categories[] = ['category' => 'Cash', 'cash_amt' => $_POST['cash_amt']];
    }

    $organization = $_POST['organization'] ?? '';
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;
    $error = "";
    
    if (empty($categories)) {
        $error = "Please select at least one donation category.";
    }

    $crud = new CRUD();
    
    if (!$error) {
        // Determine if donating as admin or on behalf of patron
        $donating_as_patron = !empty($patron_id);
        $donation_id_list = [];
        
        foreach ($categories as $cat) {
            
            $result = $crud->addDonationWithID(
                $donating_as_patron ? $patron_id : null, 
                $cat['category'],
                $organization, 
                $anonymous,
                $cat['item_name'] ?? null, 
                $cat['item_qty'] ?? null,
                $cat['food_kind'] ?? null, 
                $cat['food_qty'] ?? null,
                $cat['cash_amt'] ?? null
            );
            
            if (is_array($result) && isset($result['error'])) {
                $error = $result['error'];
                break;
            }
            
            $donation_id_list[] = $result;
        }
        
        if (!$error) {
            if ($donating_as_patron) {
                $success = "Donation on behalf of " . htmlspecialchars($patronName) . " submitted successfully!";
            } else {
                $success = "Donation submitted successfully!";
            }
            $_POST = [];
        }
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
                <div class="mb-4 border-bottom pb-3">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="donateOnBehalfToggle">
                        <label class="form-check-label fw-bold" for="donateOnBehalfToggle">Donate on behalf of patron</label>
                    </div>
                    
                    <div id="patronSelectionSection" class="d-none">
                        <?php if (!empty($patronName)): ?>
                            <div class="alert alert-info d-flex align-items-center py-2" role="alert">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <div>
                                    Donating on behalf of <strong><?= htmlspecialchars($patronName) ?></strong>
                                </div>
                                <a href="?clear=1" class="btn btn-sm btn-outline-secondary ms-auto">Clear</a>
                            </div>
                        <?php else: ?>
                            <div class="input-group">
                                <input type="text" id="patronSearch" class="form-control" placeholder="Search by name or email">
                                <button type="button" class="btn btn-outline-secondary" id="searchPatronBtn">Search</button>
                            </div>
                            <div id="patronResults" class="mt-2"></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <input list="orgList" name="organization" class="form-control" placeholder="Select or enter an organization" required>
                    <datalist id="orgList"></datalist>
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="anonymousCheck" name="anonymous" value="1" checked>
                    <label class="form-check-label" for="anonymousCheck">Make donation anonymous</label>
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
            <a href="../PHP/index.php" class="text-decoration-none text-secondary">&larr; Back to Home</a>
        </div>
    </div>
    <script>
            document.addEventListener('DOMContentLoaded', function () {
            fetch('../SP/getOrganizations.php')
                .then(res => res.json())
                .then(data => {
                    const datalist = document.getElementById('orgList');
                    datalist.innerHTML = ''; 
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
    <script>

document.addEventListener('DOMContentLoaded', function() {
    const searchBtn = document.getElementById('searchPatronBtn');
    const searchInput = document.getElementById('patronSearch');
    const resultsDiv = document.getElementById('patronResults');
    
    if (searchBtn && searchInput && resultsDiv) {
        searchBtn.addEventListener('click', function() {
            const query = searchInput.value.trim();
            if (query.length < 2) {
                resultsDiv.innerHTML = '<div class="alert alert-warning">Please enter at least 2 characters</div>';
                return;
            }
            
            resultsDiv.innerHTML = '<div class="d-flex justify-content-center"><div class="spinner-border spinner-border-sm" role="status"></div></div>';
            
            fetch('../PHP/searchPatrons.php?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    
                    if (data.length === 0) {
                        resultsDiv.innerHTML = '<div class="alert alert-info">No patrons found</div>';
                        return;
                    }
                    
                    const list = document.createElement('div');
                    list.className = 'list-group mt-2';
                    
                    data.forEach(patron => {
                        const item = document.createElement('a');
                        item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                        item.href = `?patron_id=${patron.patrons_id}`;
                        
                        const nameDiv = document.createElement('div');
                        nameDiv.innerHTML = `<strong>${patron.name}</strong><br><small>${patron.email}</small>`;
                        
                        item.appendChild(nameDiv);
                        list.appendChild(item);
                    });
                    
                    resultsDiv.appendChild(list);
                })
                .catch(error => {
                    resultsDiv.innerHTML = '<div class="alert alert-danger">Error searching patrons</div>';
                    console.error('Error:', error);
                });
        });
        
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchBtn.click();
            }
        });
    }
});
</script>
    <script>

document.addEventListener('DOMContentLoaded', function() {
    const donateOnBehalfToggle = document.getElementById('donateOnBehalfToggle');
    const patronSelectionSection = document.getElementById('patronSelectionSection');
    
    if (donateOnBehalfToggle && patronSelectionSection) {
      
        if (<?= !empty($patronName) ? 'true' : 'false' ?>) {
            donateOnBehalfToggle.checked = true;
            patronSelectionSection.classList.remove('d-none');
        }
        
        donateOnBehalfToggle.addEventListener('change', function() {
            patronSelectionSection.classList.toggle('d-none', !this.checked);
            
            if (!this.checked && window.location.search.includes('patron_id')) {
                window.location.href = '?clear=1';
            }
        });
    }
});
</script>


</body>
</html>
