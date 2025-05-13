<?php
namespace App\Models;

use PDO;

class AppointmentModel extends BaseModel {
    
    /**
     * Create a new appointment
     * 
     * @param array $data Appointment data
     * @return int|bool The appointment ID if successful, false otherwise
     */
    public function createAppointment($data) {
        try {
            // Additional debugging information
            error_log("DATABASE CONNECTION STATUS: " . ($this->db ? "Connected" : "Not connected"));
            
            $stmt = $this->db->prepare("
                INSERT INTO appointment (
                    client_code, pet_code, service_code, appt_datetime,
                    appt_type, appt_status
                ) VALUES (
                    :client_code, :pet_code, :service_code, :appt_datetime,
                    :appt_type, :appt_status
                )
                RETURNING appt_code
            ");
            
            // Prepare the data with the correct field names
            $appt_data = [
                ':client_code' => $data[':client_code'],
                ':pet_code' => $data[':pet_code'],
                ':service_code' => 1, // Default service code if not provided
                ':appt_datetime' => $data[':preferred_date'] . ' ' . $data[':preferred_time'],
                ':appt_type' => strtoupper($data[':appointment_type']),
                ':appt_status' => 'PENDING'
            ];
            
            error_log("Prepared Statement: " . ($stmt ? "Success" : "Failed"));
            error_log("Executing with data: " . print_r($appt_data, true));
            
            $stmt->execute($appt_data);
            $result = $stmt->fetchColumn();
            
            error_log("Query execution result: " . ($result ? "Success with ID: $result" : "No result returned"));
            
            return $result;
        } catch (\PDOException $e) {
            error_log("PDOException in createAppointment: " . $e->getMessage());
            error_log("SQL error code: " . ($e->getCode() ?? "unknown"));
            error_log("SQL error info: " . print_r($e->errorInfo ?? [], true));
            return false;
        } catch (\Exception $e) {
            error_log("General Exception in createAppointment: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if a time slot is available
     * 
     * @param string $date The preferred date
     * @param string $time The preferred time
     * @param int $maxAppointments Maximum allowed appointments per time slot
     * @return bool True if slot is available, false otherwise
     */
    public function isTimeSlotAvailable($date, $time, $maxAppointments = 3) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM appointment 
            WHERE preferred_date = :date AND preferred_time = :time
        ");
        
        $stmt->execute([':date' => $date, ':time' => $time]);
        $count = $stmt->fetchColumn();
        
        return $count < $maxAppointments;
    }
    
    /**
     * Add a new pet and return its ID
     * 
     * @param array $petData Pet data
     * @return int|bool The pet ID if successful, false otherwise
     */
    public function addPet($petData) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO pet (
                    client_code, pet_name, pet_type, pet_breed, pet_age, pet_med_history
                ) VALUES (
                    :client_code, :pet_name, :pet_type, :pet_breed, :pet_age, :pet_med_history
                )
                RETURNING pet_code
            ");
            
            $stmt->execute([
                ':client_code' => $petData['client_code'],
                ':pet_name' => $petData['pet_name'],
                ':pet_type' => $petData['pet_type'],
                ':pet_breed' => $petData['pet_breed'],
                ':pet_age' => $petData['pet_age'],
                ':pet_med_history' => $petData['pet_med_history']
            ]);
            
            return $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Error adding pet: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if a pet with same name and type already exists for a client
     * 
     * @param int $clientCode Client ID
     * @param string $petName Pet name
     * @param string $petType Pet type
     * @return bool True if duplicate exists, false otherwise
     */
    public function isDuplicatePet($clientCode, $petName, $petType) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM pet 
            WHERE client_code = :client_code 
            AND LOWER(pet_name) = LOWER(:pet_name) 
            AND LOWER(pet_type) = LOWER(:pet_type)
        ");
        
        $stmt->execute([
            ':client_code' => $clientCode,
            ':pet_name' => $petName,
            ':pet_type' => $petType
        ]);
        
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    /**
     * Get pet details by ID
     * 
     * @param int $petCode Pet ID
     * @return array|bool Pet data or false if not found
     */
    public function getPetById($petCode) {
        $stmt = $this->db->prepare("
            SELECT * FROM pet WHERE pet_code = :pet_code
        ");
        
        $stmt->execute([':pet_code' => $petCode]);
        return $stmt->fetch();
    }
    
    /**
     * Get all appointments for a client
     * 
     * @param int $clientCode Client ID
     * @return array List of appointments
     */
    public function getClientAppointments($clientCode) {
        $stmt = $this->db->prepare("
            SELECT * FROM appointment
            WHERE client_code = :client_code
            ORDER BY preferred_date DESC, preferred_time DESC
        ");
        
        $stmt->execute([':client_code' => $clientCode]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get all appointments for a specific date
     * 
     * @param string $date Date to check
     * @return array List of appointments
     */
    public function getAppointmentsByDate($date) {
        $stmt = $this->db->prepare("
            SELECT * FROM appointment
            WHERE preferred_date = :date
            ORDER BY preferred_time ASC
        ");
        
        $stmt->execute([':date' => $date]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get client information
     * 
     * @param int $clientCode Client ID
     * @return array|bool Client data or false if not found
     */
    public function getClientInfo($clientCode) {
        $stmt = $this->db->prepare("
            SELECT * FROM client WHERE clt_code = :client_code
        ");
        
        $stmt->execute([':client_code' => $clientCode]);
        return $stmt->fetch();
    }
    
    /**
     * Check if the appointment table structure is valid
     * 
     * @return bool True if valid, false otherwise
     */
    public function checkAppointmentTableStructure() {
        try {
            $sql = "SELECT column_name FROM information_schema.columns 
                    WHERE table_name = 'appointment'
                    ORDER BY column_name";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            
            error_log("Appointment table columns: " . print_r($columns, true));
            
            // Check if necessary columns exist based on the actual schema
            $required_columns = [
                'appt_code', 'client_code', 'pet_code', 'service_code',
                'staff_code', 'appt_datetime', 'appt_type', 'appt_status'
            ];
            
            $missing_columns = [];
            foreach ($required_columns as $required) {
                if (!in_array($required, $columns)) {
                    $missing_columns[] = $required;
                }
            }
            
            if (!empty($missing_columns)) {
                error_log("Missing required columns in appointment table: " . implode(', ', $missing_columns));
                return false;
            }
            
            return true;
        } catch (\PDOException $e) {
            error_log("Error checking appointment table structure: " . $e->getMessage());
            return false;
        }
    }
} 