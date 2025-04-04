<?php
class Database {
    private $host = "localhost";
    private $db_name = "dbgroup01"; 
    private $username = "root"; 
    private $password = ""; 
    public $conn;

    public function getConnection() {
        if ($this->conn === null) {
            try {
                $this->conn = new PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                    $this->username, $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $exception) {
                die(json_encode(["error" => "Connection error: " . $exception->getMessage()]));
            }
        }
        return $this->conn;
    }
}
?>
