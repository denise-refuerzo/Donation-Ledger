<?php
require_once '../PHP/CRUD.php';

$crud = new CRUD();

$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? '';
$organization = $_GET['organization'] ?? '';
$search = $_GET['search'] ?? '';
$userType = $_GET['userType'] ?? '';  // New filter for user type

// Call the method to search and filter donations
$results = $crud->searchAndFilter($search, $category, $status, $organization, $userType);

header('Content-Type: application/json');
echo json_encode($results);
?>
