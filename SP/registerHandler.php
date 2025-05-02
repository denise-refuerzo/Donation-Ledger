<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../PHP/CRUD.php';
$crud = new CRUD();

header('Content-Type: application/json');
ob_clean();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $password = $_POST['password'] ?? '';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $patronId = $crud->RegisterPatron($name, $email, $contact, $hashedPassword);

    if ($patronId === -1) {
        echo json_encode(['status' => 'duplicate']);
    } elseif ($patronId) {
        echo json_encode(['status' => 'success', 'patron_id' => $patronId]);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}

echo json_encode(['status' => 'invalid']);
?>