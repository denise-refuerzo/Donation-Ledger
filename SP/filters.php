<?php
require_once '../PHP/CRUD.php';

$crud = new CRUD();
$category = $_POST['category'] ?? '';
$status = $_POST['status'] ?? '';
$organization = $_POST['organization'] ?? '';
$search = $_POST['search'] ?? '';
$userType = $_POST['userType'] ?? '';

// Convert all values to empty string if not set
$results = $crud->searchAndFilter($search, $category, $status, $organization, $userType);

header('Content-Type: application/json');
echo json_encode($results);

?>
