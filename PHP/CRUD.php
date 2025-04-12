<?php
require_once "dbConnection.php";

class CRUD {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function addDonation($name, $email, $contact, $category, $organization, $anonymous) {
        try {
            $stmt = $this->conn->prepare("CALL add_full_donation(:p_name, :p_email, :p_contact, :p_category, :p_organization, :p_anonymous)");
            $stmt->bindParam(':p_name', $name);
            $stmt->bindParam(':p_email', $email);
            $stmt->bindParam(':p_contact', $contact);
            $stmt->bindParam(':p_category', $category);
            $stmt->bindParam(':p_organization', $organization);
            $stmt->bindParam(':p_anonymous', $anonymous, PDO::PARAM_BOOL);
            $stmt->execute();
            while ($stmt->nextRowset()) {;}
            return true;
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }


    public function searchAndFilter($search, $category, $status, $organization) {
        try {
            $stmt = $this->conn->prepare("CALL search_and_filter(:search, :category, :status, :organization)");
            $stmt->bindParam(':search', $search);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':organization', $organization);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            while ($stmt->nextRowset()) {;}
            return $result;
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }
    
}
?>
