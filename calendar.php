<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calendar</title>
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-7">
        <div id="calendar" class="container mt-4"></div>
      </div>
    </div>
  </div>
  
</body>
</html>
<?php
  require_once 'PHP/dbConnection.php';
  try {
    $db = new Database();
    $conn = $db->getConnection();
    $events=[];
    
    $stmt = $conn->prepare("CALL displaydonation()");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($data as $row){

      $events[]=[
          'title'=> $row['cashamount'],
          'start'=> $row['donationdate']
      ];
    }
    $eventsjson=json_encode($events);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>



<script>
  document.addEventListener('DOMContentLoaded', function() {

    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      themeSystem: 'bootstrap5',
      events:<?=$eventsjson?>
    });
    calendar.render();
  });
</script>
