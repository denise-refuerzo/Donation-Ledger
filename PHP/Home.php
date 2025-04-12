<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Donation Ledger</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-secondary bg-opacity-10 text-dark">

  <header class="bg-dark text-white p-3 d-flex justify-content-between align-items-center">
    <h1 class="h4 m-0">Donation Ledger</h1>
    <a href="addDonation.php" class="btn btn-light text-dark">Donate</a>
  </header>

  <div class="container my-4">
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <input type="text" id="searchInput" class="form-control bg-light text-dark" placeholder="Search Patron Name...">
      </div>
      <div class="col-md-2">
        <select id="categoryFilter" class="form-select bg-light text-dark">
          <option value="">All Categories</option>
          <option value="Item">Item</option>
          <option value="Food">Food</option>
          <option value="Cash">Cash</option>
        </select>
      </div>
      <div class="col-md-2">
        <select id="organizationFilter" class="form-select bg-light text-dark">
          <option value="">All Organizations</option>
          <option value="Nursing Home">Nursing Home</option>
          <option value="Homeless Shelter">Homeless Shelter</option>
          <option value="Natural Disasters">Natural Disasters</option>
          <option value="Orphanage">Orphanage</option>
        </select>
      </div>
      <div class="col-md-2">
        <select id="statusFilter" class="form-select bg-light text-dark">
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="done">Done</option>
        </select>
      </div>
      <div class="col-md-2">
        <select id="userTypeFilter" class="form-select bg-light text-dark">
          <option value="">All Users</option>
          <option value="named">Named Users</option>
          <option value="anonymous">Anonymous Users</option>
        </select>
      </div>
    </div>

    <div class="table-responsive">
      <table id="donationTable" class="table table-striped table-hover table-bordered border-dark">
        <thead class="table-dark text-center">
          <tr>
            <th>Patron Name</th>
            <th>Category</th>
            <th>Organization</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody class="bg-light text-dark">
          <!-- AJAX-loaded rows will go here -->
        </tbody>
      </table>
    </div>
  </div>

  <script>
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const organizationFilter = document.getElementById('organizationFilter');
    const statusFilter = document.getElementById('statusFilter');
    const userTypeFilter = document.getElementById('userTypeFilter');
    const tableBody = document.querySelector('#donationTable tbody');

    function fetchDonations() {
      const params = new URLSearchParams({
        search: searchInput.value,
        category: categoryFilter.value,
        organization: organizationFilter.value,
        status: statusFilter.value,
        userType: userTypeFilter.value
      });

      fetch(`../SP/filters.php?${params}`)
        .then(response => response.json())
        .then(data => {
          tableBody.innerHTML = "";

          if (data.error) {
            tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">${data.error}</td></tr>`;
            return;
          }

          if (data.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">No results found.</td></tr>`;
          } else {
            data.forEach(row => {
              const isAnonymous = !row.name;
              const tr = document.createElement('tr');
              tr.innerHTML = `
                <td><a href="../PHP/profile.php?patron_id=${row.patrons_id}" class="text-decoration-none">${isAnonymous ? "Anonymous" : row.name}</a></td>
                <td>${row.category}</td>
                <td>${row.organization}</td>
                <td>${row.status}</td>
              `;
              tableBody.appendChild(tr);
            });
          }
        })
        .catch(err => {
          tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Error fetching data.</td></tr>`;
          console.error(err);
        });
    }

    [searchInput, categoryFilter, organizationFilter, statusFilter, userTypeFilter].forEach(el => {
      el.addEventListener('input', fetchDonations);
    });

    // Load initial data
    fetchDonations();
  </script>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
