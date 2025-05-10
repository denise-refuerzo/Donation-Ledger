<?php
session_start();
require_once 'session.php';

$session = new Session();
$isLoggedIn = $session->isLoggedIn();

// Restrict access to admins only
if (!$isLoggedIn) {
    header("Location: login_view.php");
    exit();
}

if ($session->getRole() === 'user') {
    // Redirect users to their profile page
    $patronId = $session->getPatronId();
    header("Location: ../CONNECTED/profile.php?patron_id=" . urlencode($patronId));
    exit();
}

if ($session->getRole() !== 'admin') {
    // Redirect non-admins to an error page or login
    header("Location: login_view.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Donation Ledger</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-secondary bg-opacity-10 text-dark">

<header class="bg-dark text-white p-3 d-flex justify-content-between align-items-center">
    <h1 class="h4 m-0">
      <a href="../CONNECTED/welcomePage.php" class="text-white text-decoration-none">Donation Ledger</a>
    </h1>
    <header class="bg-dark text-white p-3">
  <div class="container-fluid d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
    <div class="d-flex flex-wrap gap-2">
      <a href="addDonation.php" class="btn btn-light text-dark">Donate</a>
      <a href="dailydonation.php" class="btn btn-light text-dark">Daily Donations</a>
      <a href="grouped_chart.php" class="btn btn-light text-dark">Grouped Chart</a>
      <a href="calendar.php" class="btn btn-light text-dark">View Calendar</a>
      <a href="donationsTimeChart.php" class="btn btn-light text-dark">Donations Over Time</a>
      <a href="logout.php" class="btn btn-danger text-white">Logout</a>
    </div>
  </div>
</header>

</header>



  <div class="container my-4">
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <input type="text" id="searchInput" class="form-control bg-light text-dark" placeholder="Search Patron Name...">
      </div>
      <div class="col-md-2">
        <select id="categoryFilter" class="form-select bg-light text-dark" style="max-height: 150px; overflow-y: auto;">
          <option value="">All Categories</option>
        </select>
      </div>
      <div class="col-md-2">
        <select id="organizationFilter" class="form-select bg-light text-dark" style="max-height: 150px; overflow-y: auto;">
          <option value="">All Organizations</option>
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
      <table id="donationTable" class="table table-striped table-hover table-bordered border-dark align-middle">
        <thead class="table-dark text-center">
          <tr class="fs-5 py-3">
            <th>Patron Name</th>
            <th>Category</th>
            <th>Organization</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody class="bg-light text-dark" >
          <!-- AJAX-loaded rows go here -->
        </tbody>
      </table>
    </div>

    <!-- Bootstrap Pagination -->
    <nav>
      <ul class="pagination justify-content-center mt-3" id="pagination"></ul>
    </nav>
  </div>

  <!-- JavaScript -->
  <script>
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const organizationFilter = document.getElementById('organizationFilter');
    const statusFilter = document.getElementById('statusFilter');
    const userTypeFilter = document.getElementById('userTypeFilter');
    const tableBody = document.querySelector('#donationTable tbody');
    const pagination = document.getElementById('pagination');

    let allData = [];
    const rowsPerPage = 10;
    let currentPage = 1;

    function loadCategories() {
      return fetch('../SP/getCategories.php')
        .then(response => response.json())
        .then(data => {
          categoryFilter.innerHTML = '<option value="">All Categories</option>';
          data.forEach(cat => {
            categoryFilter.innerHTML += `<option value="${cat.category}">${cat.category}</option>`;
          });
        });
    }

    function loadOrganizations() {
      return fetch('../SP/getOrganizations.php')
        .then(response => response.json())
        .then(data => {
          organizationFilter.innerHTML = '<option value="">All Organizations</option>';
          data.forEach(org => {
            organizationFilter.innerHTML += `<option value="${org.organization}">${org.organization}</option>`;
          });
        });
    }

    Promise.all([loadCategories(), loadOrganizations()]).then(fetchDonations);

    function fetchDonations() {
      const formData = new FormData();
      formData.append("search", searchInput.value);
      formData.append("category", categoryFilter.value);
      formData.append("organization", organizationFilter.value);
      formData.append("status", statusFilter.value);
      formData.append("userType", userTypeFilter.value);

      fetch('../SP/filters.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.error) {
            tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">${data.error}</td></tr>`;
            return;
          }

          allData = data || [];
          currentPage = 1;
          displayPage(currentPage);
          renderPagination();
        })
        .catch(err => {
          tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Error fetching data.</td></tr>`;
          console.error(err);
        });
    }

    function displayPage(page) {
      tableBody.innerHTML = "";
      const start = (page - 1) * rowsPerPage;
      const end = start + rowsPerPage;
      const paginatedData = allData.slice(start, end);

      if (paginatedData.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">No results found.</td></tr>`;
        return;
      }

      paginatedData.forEach(row => {
        const isAnonymous = !row.name;
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${
            isAnonymous
              ? '<span class="text-dark">Anonymous</span>'
              : `<a href="../PHP/profile.php?patron_id=${row.patrons_id}" class="text-decoration-none text-dark">${row.name}</a>`
          }</td>
          <td>${row.category}</td>
          <td>${row.organization}</td>
          <td>${row.status}</td>
        `;
        tableBody.appendChild(tr);
      });
    }

    function renderPagination() {
      const pageCount = Math.ceil(allData.length / rowsPerPage);
      pagination.innerHTML = "";

      for (let i = 1; i <= pageCount; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === currentPage ? 'active' : ''}`;
        li.innerHTML = `<button class="page-link bg-dark text-white border-secondary">${i}</button>`;
        li.addEventListener('click', () => {
          currentPage = i;
          displayPage(currentPage);
          renderPagination();
        });
        pagination.appendChild(li);
      }
    }

    [searchInput, categoryFilter, organizationFilter, statusFilter, userTypeFilter].forEach(el => {
      el.addEventListener('input', fetchDonations);
    });
  </script>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>