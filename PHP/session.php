<?php
class Session {
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public function getRole() {
        return isset($_SESSION['role']) ? $_SESSION['role'] : null;
    }

    public function getPatronId() {
        return isset($_SESSION['patron_id']) ? $_SESSION['patron_id'] : null;
    }

    public function logout() {
        session_unset();
        session_destroy();
    }
}
?>