<?php
require_once '../PHP/Session.php';
$session = new Session();

if (!$session->isLoggedIn()) {
    header("Location: welcomePage.php"); // or wherever your login page is
    exit;
}
?>
