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
        if ($this->getRole() === 'admin') {
            return isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
        }
        return isset($_SESSION['patron_id']) ? $_SESSION['patron_id'] : null;
    }
    public function logout() {
        session_unset();
        session_destroy();
    }
    public function regenerateId() {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
}
?>