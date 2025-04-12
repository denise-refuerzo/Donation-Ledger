<?php
require_once '../PHP/CRUD.php';

$crud = new CRUD();
$data = $crud->printAllDonations();

header('Content-Type: application/json');
echo json_encode($data, JSON_PRETTY_PRINT);
?>
