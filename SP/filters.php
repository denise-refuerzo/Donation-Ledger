<?php
require_once '../PHP/CRUD.php';

$crud = new CRUD();

$category = $_GET['category'] ?? null;
$status = $_GET['status'] ?? null;
$organization = $_GET['organization'] ?? null;
$search = $_GET['search'] ?? null;

// Convert empty strings to actual NULL values
$category = ($category === '') ? null : $category;
$status = ($status === '') ? null : $status;
$organization = ($organization === '') ? null : $organization;

// Set the response header
header('Content-Type: application/json');

// If all are empty, show all donations
if (empty($search) && is_null($category) && is_null($status) && is_null($organization)) {
    $results = $crud->printAllDonations();
} elseif (!empty($search)) {
    $results = $crud->searchPatron($search);
} else {
    $results = $crud->filterDonations($category, $status, $organization);
}

echo json_encode($results);
?>
