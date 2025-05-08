<?php
require_once "dbConnection.php";

class CRUD {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getCashDonations() {
        $stmt = $this->conn->prepare("CALL cashDonations()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalDonations() {
        $stmt = $this->conn->prepare("CALL totalDonations()");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCategorySummary() {
        $stmt = $this->conn->prepare("CALL categoryDonationsummary()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function addDonation($name, $email, $contact, $category, $organization, $anonymous, $item_name, $item_qty, $food_kind, $food_qty, $cash_amt, $patron_id = null) {
        try {
            $stmt = $this->conn->prepare("
                CALL addDonation(
                    :p_name, :p_email, :p_contact, :p_category, :p_organization, :p_anonymous, 
                    :p_item_name, :p_item_qty, :p_food_kind, :p_food_qty, :p_cash_amt, 
                    @donation_id, :p_id
                )
            ");
    
            // Bind as NULL if anonymous
            $name = $anonymous ? null : $name;
            $email = $anonymous ? null : $email;
            $contact = $anonymous ? null : $contact;
            $patron_id = $anonymous ? null : $patron_id;
    
            $stmt->bindParam(':p_name', $name);
            $stmt->bindParam(':p_email', $email);
            $stmt->bindParam(':p_contact', $contact);
            $stmt->bindParam(':p_category', $category);
            $stmt->bindParam(':p_organization', $organization);
            $stmt->bindParam(':p_anonymous', $anonymous, PDO::PARAM_BOOL);
            $stmt->bindParam(':p_item_name', $item_name);
            $stmt->bindParam(':p_item_qty', $item_qty);
            $stmt->bindParam(':p_food_kind', $food_kind);
            $stmt->bindParam(':p_food_qty', $food_qty);
            $stmt->bindParam(':p_cash_amt', $cash_amt);
            $stmt->bindParam(':p_id', $patron_id, PDO::PARAM_INT);
    
            $stmt->execute();
    
            // Get the OUT parameter
            $stmt = $this->conn->query("SELECT @donation_id AS donation_id");
            $donation_id = $stmt->fetchColumn();
    
            return true;
    
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }
    
    

    public function addDonationWithID($patron_id, $category, $organization, $anonymous, $item_name, $item_qty, $food_kind, $food_qty, $cash_amt) {
        try {
            // Assuming patron_id is passed correctly
            $stmt = $this->conn->prepare("CALL addDonation(NULL, NULL, NULL, :p_category, :p_organization, :p_anonymous, :p_item_name, :p_item_qty, :p_food_kind, :p_food_qty, :p_cash_amt, @donation_id, :p_id)");
    
            $stmt->bindParam(':p_category', $category);
            $stmt->bindParam(':p_organization', $organization);
            $stmt->bindParam(':p_anonymous', $anonymous, PDO::PARAM_BOOL);
            $stmt->bindParam(':p_item_name', $item_name);
            $stmt->bindParam(':p_item_qty', $item_qty);
            $stmt->bindParam(':p_food_kind', $food_kind);
            $stmt->bindParam(':p_food_qty', $food_qty);
            $stmt->bindParam(':p_cash_amt', $cash_amt);
            $stmt->bindParam(':p_id', $patron_id);
    
            $stmt->execute();
    
            $stmt = $this->conn->query("SELECT @donation_id AS donation_id");
            $donation_id = $stmt->fetchColumn();
    
            return $donation_id;
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }
    
    
    

    public function searchAndFilter($search, $category, $status, $organization, $userType) {
        try {     
            $stmt = $this->conn->prepare("CALL searchAndfilter(:search, :category, :status, :organization, :userType)");
            $stmt->bindParam(':search', $search);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':organization', $organization);
            $stmt->bindParam(':userType', $userType);
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

    public function RegisterPatron($name, $email, $contact, $password) {
        try {
            $stmt = $this->conn->prepare("CALL register(?, ?, ?, ?, @new_id)");
            $stmt->execute([$name, $email, $contact, $password]);
            $stmt->closeCursor();
    
            $result = $this->conn->query("SELECT @new_id AS id")->fetch(PDO::FETCH_ASSOC);
    
            if ($result && $result['id'] == -1) {
                return -1; // email exists
            }
    
            return $result['id'] ?? false;
        } catch (PDOException $e) {
            error_log("Error in RegisterPatron: " . $e->getMessage());
            return false;
        }
    }

    public function getAdminCredentials($username) {
        $stmt = $this->conn->prepare("CALL get_admin_credentials(:p_username)");
        $stmt->bindParam(':p_username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor(); // Important for next SP call
        return $user;
    }

    public function getUserCredentials($email) {
        $stmt = $this->conn->prepare("CALL get_user_credentials(:p_email)");
        $stmt->bindParam(':p_email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $user;
    }
     
    public function getDailyDonations() {
        try {
            $stmt = $this->conn->prepare("CALL GetDailyDonations()");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function calendarDonations() {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            $events = [];
    
            $stmt = $conn->prepare("CALL displaydonation()");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            foreach ($data as $row) {
                $events[] = [
                    'title' => '₱' . number_format($row['cashamount'], 2),
                    'start' => $row['donationdate']
                ];
            }
    
            return json_encode($events);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Get donations data grouped by date for the time chart
     * @return array Array of donation amounts by date
     */
    public function getDonationsOverTime() {
        try {
            $stmt = $this->conn->prepare("CALL getDonationsOverTime()");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
?>
