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
            $stmt = $this->conn->prepare("CALL searchAndfilter(:search, :category, :status, :organization)");
            $stmt->bindParam(':search', $search);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':organization', $organization);
            $stmt->execute();
            
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            while ($stmt->nextRowset()) {;} // prevent "out of sync" issues
            return $data;
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }
    

    public function getAllCategories() {
        try {
            $stmt = $this->conn->prepare("CALL getAllcategories()");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            while ($stmt->nextRowset()) {;}
            return $data;
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }
    
    public function getAllOrganizations() {
        try {
            $stmt = $this->conn->prepare("CALL getAllorganizations()");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            while ($stmt->nextRowset()) {;}
            return $data;
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }
    
    
    

  
    public function getPatronInfo($patron_id) {
        try {
            $stmt = $this->conn->prepare("CALL GetPatronInfo(:patron_id)");
            $stmt->bindParam(':patron_id', $patron_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            while ($stmt->nextRowset()) {;}
            return $result;
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function deletePatron($patron_id) {
        try {
            $stmt = $this->conn->prepare("CALL deletePatron(:p_patron_id)");
            $stmt->bindParam(':p_patron_id', $patron_id, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }
    
    public function editPatron($patron_id, $name, $email, $contact) {
        try {
           
            $stmt = $this->conn->prepare("CALL editPatron(:p_patron_id, :p_name, :p_email, :p_contact)");
            $stmt->bindParam(':p_patron_id', $patron_id, PDO::PARAM_INT);
            $stmt->bindParam(':p_name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':p_email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':p_contact', $contact, PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }
    
    
}
?>
