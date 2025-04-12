<?php
require_once '../PHP/CRUD.php';

$crud = new CRUD();

$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? '';
$organization = $_GET['organization'] ?? '';
$search = $_GET['search'] ?? '';

// Convert all values to empty string if not set
$results = $crud->searchAndFilter($search, $category, $status, $organization);

header('Content-Type: application/json');
echo json_encode($results);

?>
