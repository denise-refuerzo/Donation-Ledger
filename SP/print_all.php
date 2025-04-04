<?php
require_once '../PHP/dbconnection.php';

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Call stored procedure
    $stmt = $conn->prepare("CALL print_all()");
    $stmt->execute();
    $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($donations)) {
        echo json_encode(["error" => "Stored procedure returned no data"]);
    } else {
        echo json_encode($donations);
    }

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

?>
