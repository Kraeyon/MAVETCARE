<?php
namespace App\Models;

use PDO;

class PatientModel extends BaseModel {
    protected $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAllPatients() {
    $stmt = $this->db->query('
        SELECT p.*, CONCAT(c.clt_fname, \' \', c.clt_initial, \'. \', c.clt_lname) AS client_name
        FROM pet p
        JOIN client c ON p.client_code = c.clt_code
    ');
    return $stmt->fetchAll();
}


    public function getAllClients() {
        $stmt = $this->db->query('SELECT * FROM client');
        return $stmt->fetchAll();
    }

    public function addClient($fname, $lname, $initial, $contact, $email, $address) {
    $stmt = $this->db->prepare('
        INSERT INTO client (clt_fname, clt_lname, clt_initial, clt_contact, clt_email_address, clt_home_address)
        VALUES (?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([$fname, $lname, $initial, $contact, $email, $address]);
    return $this->db->lastInsertId();
}


    public function addPatient($client_code, $pet_name, $pet_type, $pet_breed, $pet_age, $pet_med_history) {
        $stmt = $this->db->prepare('
            INSERT INTO pet (client_code, pet_name, pet_type, pet_breed, pet_age, pet_med_history)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        return $stmt->execute([$client_code, $pet_name, $pet_type, $pet_breed, $pet_age, $pet_med_history]);
    }

    public function updatePatient($pet_code, $pet_name, $pet_type, $pet_breed, $pet_age, $pet_med_history) {
        try {
            // Log the values for debugging
            error_log("Updating pet in model: Code=$pet_code, Name=$pet_name, Type=$pet_type, Breed=$pet_breed, Age=$pet_age");
            
            $stmt = $this->db->prepare('
                UPDATE pet 
                SET pet_name = :name, 
                    pet_type = :type, 
                    pet_breed = :breed, 
                    pet_age = :age, 
                    pet_med_history = :history
                WHERE pet_code = :code
            ');
            
            $stmt->bindParam(':name', $pet_name, PDO::PARAM_STR);
            $stmt->bindParam(':type', $pet_type, PDO::PARAM_STR);
            $stmt->bindParam(':breed', $pet_breed, PDO::PARAM_STR);
            $stmt->bindParam(':age', $pet_age, PDO::PARAM_INT);
            $stmt->bindParam(':history', $pet_med_history, PDO::PARAM_STR);
            $stmt->bindParam(':code', $pet_code, PDO::PARAM_INT);
            
            $result = $stmt->execute();
            
            if (!$result) {
                error_log("SQL Error: " . implode(", ", $stmt->errorInfo()));
            }
            
            return $result;
        } catch (\PDOException $e) {
            error_log("PDO Exception in updatePatient: " . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            error_log("General Exception in updatePatient: " . $e->getMessage());
            return false;
        }
    }

    public function updatePatientAgeAndHistory($pet_code, $pet_age, $pet_med_history) {
        try {
            // Log the values for debugging
            error_log("Updating pet age and history in model: Code=$pet_code, Age=$pet_age");
            
            $stmt = $this->db->prepare('
                UPDATE pet 
                SET pet_age = :age, 
                    pet_med_history = :history
                WHERE pet_code = :code
            ');
            
            $stmt->bindParam(':age', $pet_age, PDO::PARAM_INT);
            $stmt->bindParam(':history', $pet_med_history, PDO::PARAM_STR);
            $stmt->bindParam(':code', $pet_code, PDO::PARAM_INT);
            
            $result = $stmt->execute();
            
            if (!$result) {
                error_log("SQL Error: " . implode(", ", $stmt->errorInfo()));
            }
            
            return $result;
        } catch (\PDOException $e) {
            error_log("PDO Exception in updatePatientAgeAndHistory: " . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            error_log("General Exception in updatePatientAgeAndHistory: " . $e->getMessage());
            return false;
        }
    }

    public function searchPatients($searchTerm) {
        try {
            $searchTerm = "%$searchTerm%";
            
            // For exact pet_code search (without wildcards)
            $exactTerm = trim($searchTerm, '%');
            $isNumeric = is_numeric($exactTerm);
            
            $sql = '
                SELECT p.*, CONCAT(c.clt_fname, \' \', c.clt_initial, \'. \', c.clt_lname) AS client_name
                FROM pet p
                JOIN client c ON p.client_code = c.clt_code
                WHERE p.pet_name LIKE :search 
                   OR p.pet_type LIKE :search 
                   OR p.pet_breed LIKE :search
                   OR CONCAT(c.clt_fname, \' \', c.clt_initial, \'. \', c.clt_lname) LIKE :search';
            
            // Only add pet_code condition if the search term is numeric
            if ($isNumeric) {
                $sql .= ' OR p.pet_code = :exact';
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
            
            if ($isNumeric) {
                $stmt->bindParam(':exact', $exactTerm, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            // Log the error
            error_log("PDO Exception in searchPatients: " . $e->getMessage());
            // Return empty array to avoid breaking the UI
            return [];
        } catch (\Exception $e) {
            // Log the error
            error_log("General Exception in searchPatients: " . $e->getMessage());
            // Return empty array to avoid breaking the UI
            return [];
        }
    }

    public function getSortedPatients($sortBy = 'pet_code', $sortOrder = 'ASC') {
        // Validate sort field to prevent SQL injection
        $allowedSortFields = [
            'pet_code', 'client_name', 'pet_name', 'pet_type', 
            'pet_breed', 'pet_age'
        ];
        
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'pet_code'; // Default sort field
        }
        
        // Validate sort order
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';
        
        // Special case for client_name as it's a derived field
        if ($sortBy === 'client_name') {
            $sql = '
                SELECT p.*, CONCAT(c.clt_fname, \' \', c.clt_initial, \'. \', c.clt_lname) AS client_name
                FROM pet p
                JOIN client c ON p.client_code = c.clt_code
                ORDER BY CONCAT(c.clt_fname, \' \', c.clt_initial, \'. \', c.clt_lname) ' . $sortOrder;
        } else {
            $sql = '
                SELECT p.*, CONCAT(c.clt_fname, \' \', c.clt_initial, \'. \', c.clt_lname) AS client_name
                FROM pet p
                JOIN client c ON p.client_code = c.clt_code
                ORDER BY p.' . $sortBy . ' ' . $sortOrder;
        }
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function deletePatient($pet_code) {
        $stmt = $this->db->prepare('DELETE FROM pet WHERE pet_code = ?');
        return $stmt->execute([$pet_code]);
    }

    public function isDuplicatePet($client_code, $pet_name, $pet_type) {
        $stmt = $this->db->prepare('
            SELECT COUNT(*) as count 
            FROM pet 
            WHERE client_code = ? AND LOWER(pet_name) = LOWER(?) AND LOWER(pet_type) = LOWER(?)
        ');
        $stmt->execute([$client_code, $pet_name, $pet_type]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    public function getClientCodeByEmail($email) {
        $stmt = $this->db->prepare('
            SELECT clt_code 
            FROM client 
            WHERE clt_email_address = ?
        ');
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        return $result ? $result['clt_code'] : null;
    }
}

