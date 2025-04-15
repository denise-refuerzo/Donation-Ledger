<?php
require_once '../PHP/CRUD.php';

$crud = new CRUD();
$search = $_POST['search'] ?? '';
$category = $_POST['category'] ?? '';
$status = $_POST['status'] ?? '';
$organization = $_POST['organization'] ?? '';

$data = $crud->searchAndFilter($search, $category, $status, $organization);
echo json_encode($data);

?>
