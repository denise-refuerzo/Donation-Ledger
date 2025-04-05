<?php
require_once "../PHP/dbConnection.php";

$database = new Database();
$conn = $database->getConnection();

$category = isset($_GET['category']) && $_GET['category'] !== "" ? $_GET['category'] : null;
$status = isset($_GET['status']) && $_GET['status'] !== "" ? $_GET['status'] : null;
$organization = isset($_GET['organization']) && $_GET['organization'] !== "" ? $_GET['organization'] : null;
$search = isset($_GET['search']) && $_GET['search'] !== "" ? $_GET['search'] : null;

try {
    if ($search) {
        $searchTerm = '%' . $search . '%';
        $stmt = $conn->prepare("CALL search_patron(:search)");
        $stmt->bindParam(':search', $searchTerm);
    } elseif ($category || $status || $organization) {
        $stmt = $conn->prepare("CALL filter_donations(:category, :status, :organization)");
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':organization', $organization);
    } else {
        $stmt = $conn->prepare("CALL print_all()");
    }

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prevents "more than one result" errors:
    while ($stmt->nextRowset()) {;}

    echo json_encode($results);

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

?>
