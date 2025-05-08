<?php
require_once '../PHP/CRUD.php';
$crud = new CRUD();
$eventsjson = $crud->calendarDonations();

if (isset($eventsjson['error'])) {
    echo "Error: " . $eventsjson['error'];
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Donation Calendar</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- FullCalendar -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

  <style>
    body {
      background-color: #f2f2f2;
      color: #333;
    }
    .card {
      background-color: #e0e0e0;
      border: none;
    }
    .card-header {
      background-color: #d6d6d6;
      font-weight: bold;
      text-align: center;
    }

    .btn-back {
      background-color: #6c757d;
      color: white;
    }
    .btn-back:hover {
      background-color: #5a6268;
    }

    /* FullCalendar - Remove All Blue and Apply Gray Theme */
    .fc .fc-button {
      background-color: #b0b0b0 !important;
      border: 1px solid #999 !important;
      color: #222 !important;
      box-shadow: none !important;
    }

    .fc .fc-button:hover,
    .fc .fc-button:focus,
    .fc .fc-button:active,
    .fc .fc-button-primary:not(:disabled):active,
    .fc .fc-button-primary:not(:disabled).fc-button-active {
      background-color: #999 !important;
      border-color: #888 !important;
      color: #fff !important;
      outline: none !important;
      box-shadow: none !important;
    }

    .fc .fc-toolbar-title {
      color: #333;
    }

    .fc .fc-daygrid-day-number {
      color: #444;
    }

    .fc-event {
      background-color: #7d7d7d !important;
      border: none !important;
      color: #fff !important;
    }

    .fc-daygrid-day.fc-day-today {
      background-color: #cfcfcf !important;
    }

    .fc .fc-scrollgrid,
    .fc .fc-scrollgrid-section,
    .fc .fc-col-header-cell {
      border-color: #ccc;
      background-color: #ddd;
    }

    .fc .fc-highlight {
      background-color: #bbb !important;
    }

    /* Remove link styles that might be blue */
    a {
      color: #555;
      text-decoration: none;
    }

    a:hover {
      color: #333;
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <div class="container py-4">
    <button onclick="history.back()" class="btn btn-back mb-4">&larr; Back</button>
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow">
          <div class="card-header">
            Donation Calendar
          </div>
          <div class="card-body">
            <div id="calendar"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var calendarEl = document.getElementById('calendar');
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        themeSystem: 'bootstrap5',
        events: <?= $eventsjson ?>
      });
      calendar.render();
    });
  </script>
</body>
</html>
