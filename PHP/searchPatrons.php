<?php
require_once "CRUD.php";
require_once "session.php";

$session = new Session();

if (!$session->isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

if ($session->getRole() !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($query) < 2) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

$crud = new CRUD();
$results = $crud->searchPatrons($query);

header('Content-Type: application/json');
echo json_encode($results);
?>