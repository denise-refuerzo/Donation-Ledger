<?php
require_once '../PHP/CRUD.php';
$crud = new CRUD();
header('Content-Type: application/json');
echo json_encode($crud->getAllCategories());
?>