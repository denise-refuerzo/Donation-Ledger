<?php
require_once 'session.php';
$session = new Session();
$session->logout();
header("Location: login_view.php");
exit;
?>
