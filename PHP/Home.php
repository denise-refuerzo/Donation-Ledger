<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Ledger</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../CSS/home.css">
</head>
<body>
    <header>
        <h1>DL</h1>
        <button id="donate" onclick="location.href='../PHP/addDonation.php'">Donate</button>
    </header>
    
    <?php 
    require_once "../PHP/dbConnection.php"; 
    $database = new Database();
    $conn = $database->getConnection();
     ?>

<div class="filters">
    <select id="categoryFilter">
        <option value="">All Category</option>
        <option value="Item">Item</option>
        <option value="Food">Food</option>
        <option value="Cash">Cash</option>
    </select>
    
    <select id="organizationFilter">
        <option value="">All Organization</option>
        <option value="Nursing Home">Nursing Home</option>
        <option value="Homeless Shelter">Homeless Shelter</option>
        <option value="Natural Disasters">Natural Disasters</option>
        <option value="Orphanage">Orphanage</option>
    </select>
    
    <select id="statusFilter">
        <option value="">All Status</option>
        <option value="Pending">Pending</option>
        <option value="Done">Done</option>
    </select>
    
    <input type="text" id="searchPatron" placeholder="Search Patron">
</div>


    <table id="donationTable">
        <thead>
            <tr>
                <th>Patron</th>
                <th>Category</th>
                <th>Organization</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <!-- AJAX will load data here -->
        </tbody>
    </table>

    <script>
   function loadTable() {
    let category = $('#categoryFilter').val();
    let status = $('#statusFilter').val();
    let organization = $('#organizationFilter').val();
    let search = $('#searchPatron').val();

    $.ajax({
        url: '../SP/filters.php',
        type: 'GET',
        data: {
            category: category,
            status: status,
            organization: organization,
            search: search
        },
        success: function(response) {
    try {
        let data = JSON.parse(response);
        let tableBody = '';

        if (data.length === 0) {
            tableBody = '<tr><td colspan="4">No results found</td></tr>';
        } else {
            data.forEach(row => {
    const patronCell = row.anonymous == 1 || row.Patron_Name === "Anonymous"
    ? "Anonymous"
    : `<a href="../PHP/profile.php?patron_id=${row.Patrons_ID}&category=${category}&status=${status}&organization=${organization}">
           ${row.Patron_Name}
       </a>`;

    tableBody += `<tr>
        <td>${patronCell}</td>
        <td>${row.Category}</td>
        <td>${row.Organization}</td>
        <td>${row.Donation_Status}</td>
    </tr>`;
});

        }

        $('#donationTable tbody').html(tableBody);
    } catch (e) {
        console.error("Invalid JSON:", response);
        $('#donationTable tbody').html('<tr><td colspan="4">An error occurred</td></tr>');
    }
}

    });
}


$(document).ready(function() {
    loadTable(); // Load all data when page loads
    $('#categoryFilter, #organizationFilter, #statusFilter, #searchPatron').on('change keyup', loadTable);
});
</script>

</body>
</html>