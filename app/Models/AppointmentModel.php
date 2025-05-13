<?php
namespace App\Models;

use PDO;

class AppointmentModel extends BaseModel {
    
    /**
     * Create a new appointment - uses the minimal approach that is known to work
     * 
     * @param array $data Appointment data
     * @return int|bool The appointment ID if successful, false otherwise
     */
    public function createAppointment($data) {
        try {
            // Set PDO to throw exceptions
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Check if we need to start a transaction (don't start a new one if there's already one active)
            $inTransaction = $this->db->inTransaction();
            if (!$inTransaction) {
                $this->db->beginTransaction();
                error_log("Starting new transaction in createAppointment");
            } else {
                error_log("Using existing transaction in createAppointment");
            }
            
            // Get client and pet details before insert to populate required fields
            $clientData = $this->getClientInfo($data[':client_code']);
            $petData = $this->getPetById($data[':pet_code']);
            
            // Prepare client contact number - ensure non-null with empty string default
            $contactNumber = (!empty($clientData['clt_contact'])) ? $clientData['clt_contact'] : '';
            
            // Ensure additional_notes is not null
            $additionalNotes = (!empty($data[':additional_notes'])) ? $data[':additional_notes'] : '';
            
            // Log the values for debugging
            error_log("Before INSERT - Contact Number: '" . $contactNumber . "'");
            error_log("Before INSERT - Additional Notes: '" . $additionalNotes . "'");
            
            // SIMPLEST POSSIBLE INSERT - focus on the core fields only
            $sql = "
                INSERT INTO appointment (
                    client_code, 
                    pet_code, 
                    preferred_date, 
                    preferred_time,
                    status,
                    additional_notes,
                    contact_number,
                    owner_name,
                    email,
                    appt_status
                ) VALUES (
                    ?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?
                )
            ";
            
            // Prepare owner name from client data
            $ownerName = '';
            if (!empty($clientData['clt_fname'])) {
                $ownerName .= trim($clientData['clt_fname']) . ' ';
            }
            if (!empty($clientData['clt_initial'])) {
                $ownerName .= trim($clientData['clt_initial']) . ' ';
            }
            if (!empty($clientData['clt_lname'])) {
                $ownerName .= trim($clientData['clt_lname']);
            }
            $ownerName = trim($ownerName);
            
            // Ensure email is not null
            $email = !empty($clientData['clt_email_address']) ? $clientData['clt_email_address'] : '';
            
            $values = [
                $data[':client_code'],
                $data[':pet_code'],
                $data[':preferred_date'],
                $data[':preferred_time'],
                $additionalNotes,
                $contactNumber,
                $ownerName,
                $email,
                'PENDING'  // Last value for appt_status
            ];
            
            // Debug the SQL and values
            error_log("SQL INSERT STATEMENT: " . $sql);
            error_log("VALUES COUNT: " . count($values) . " values to insert");
            error_log("VALUES: " . print_r($values, true));
            
            error_log("Executing appointment insert");
            
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute($values);
            
            // Debug success state
            error_log("INSERT SUCCESS: " . ($success ? "TRUE" : "FALSE"));
            
            if (!$success) {
                error_log("PDO ERROR INFO: " . print_r($stmt->errorInfo(), true));
                throw new \Exception("Failed to insert appointment record");
            }
            
            // Get the newly inserted appointment ID
            $newId = $this->db->query("SELECT MAX(appt_code) FROM appointment")->fetchColumn();
            error_log("NEW ID RETRIEVED: " . ($newId ? $newId : "No ID returned"));
            
            if (!$newId) {
                error_log("Failed to get new appointment ID");
                if (!$inTransaction) {
                    $this->db->commit(); // Still commit what we've done
                }
                return true; // Return true since we did insert a record
            }
            
            // Format appointment datetime
            $apptDatetime = date('Y-m-d H:i:s', strtotime($data[':preferred_date'] . ' ' . $data[':preferred_time']));
            
            // Debug log for troubleshooting
            error_log("Client contact: " . $contactNumber);
            error_log("Additional notes: " . $additionalNotes);
            
            // Update the appointment with additional fields
            $updateSql = "
                UPDATE appointment SET
                appt_datetime = ?,
                appt_type = ?,
                appt_status = ?,
                service_code = ?,
                pet_name = ?,
                pet_type = ?,
                additional_notes = ?,
                appointment_type = ?,
                owner_name = ?,
                contact_number = ?,
                email = ?
                WHERE appt_code = ?
            ";
            
            // Try to get a valid service code first
            $serviceCode = $data[':service_code'] ?? null;
            if (!$serviceCode) {
                // Try to find a service by name if provided
                if (!empty($data[':appointment_type'])) {
                    $serviceStmt = $this->db->prepare("SELECT service_code FROM service WHERE LOWER(service_name) LIKE ?");
                    $serviceStmt->execute(['%' . strtolower($data[':appointment_type']) . '%']);
                    $serviceCode = $serviceStmt->fetchColumn();
                }
                
                // If still not found, use our General Checkup service or the first service
                if (!$serviceCode) {
                    $serviceStmt = $this->db->query("SELECT service_code FROM service ORDER BY service_code LIMIT 1");
                    $serviceCode = $serviceStmt->fetchColumn();
                }
            }
            
            $updateValues = [
                $apptDatetime,
                strtoupper($data[':appointment_type'] ?? 'WALK-IN'),
                'PENDING', // Explicit value for appt_status
                $serviceCode, // Using our validated service code
                $petData['pet_name'] ?? '',
                $petData['pet_type'] ?? '',
                $additionalNotes,
                $data[':appointment_type'] ?? 'walk-in',
                $ownerName, // Add owner name
                $contactNumber, // Add contact number
                $email, // Add email
                $newId
            ];
            
            error_log("Executing UPDATE with values: " . print_r($updateValues, true));
            
            $updateStmt = $this->db->prepare($updateSql);
            $updateSuccess = $updateStmt->execute($updateValues);
            
            error_log("UPDATE SUCCESS: " . ($updateSuccess ? "TRUE" : "FALSE"));
            
            // Only commit if we started the transaction
            if (!$inTransaction) {
                $this->db->commit();
                error_log("Transaction committed in createAppointment");
            }
            
            return $newId;
            
        } catch (\Exception $e) {
            error_log("EXCEPTION in createAppointment: " . $e->getMessage());
            error_log("EXCEPTION trace: " . $e->getTraceAsString());
            
            // Only rollback if we started the transaction
            if (!$inTransaction && $this->db->inTransaction()) {
                try {
                    $this->db->rollBack();
                    error_log("Transaction rolled back in createAppointment");
                } catch (\Exception $rollbackEx) {
                    error_log("Error rolling back transaction: " . $rollbackEx->getMessage());
                }
            }
            
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
        try {
            // Make sure we have valid date and time formats
            $formattedDate = date('Y-m-d', strtotime($date));
            $formattedTime = date('H:i:s', strtotime($time));
            
            // Simple query to check by preferred_date and preferred_time
            $sql = "SELECT COUNT(*) FROM appointment WHERE preferred_date = ? AND preferred_time = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$formattedDate, $formattedTime]);
            $count = $stmt->fetchColumn();
            
            error_log("Time slot check for $formattedDate at $formattedTime: found $count appointments (max: $maxAppointments)");
            
            return $count < $maxAppointments;
        } catch (\Exception $e) {
            // Log but don't fail - return true to allow booking
            error_log("Error checking time slot: " . $e->getMessage());
            return true;
        }
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
            ");
            
            $stmt->execute([
                ':client_code' => $petData['client_code'],
                ':pet_name' => $petData['pet_name'],
                ':pet_type' => $petData['pet_type'],
                ':pet_breed' => $petData['pet_breed'],
                ':pet_age' => $petData['pet_age'],
                ':pet_med_history' => $petData['pet_med_history']
            ]);
            
            // Get the last insert ID (works with MySQL)
            return $this->db->lastInsertId();
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
                'appt_datetime', 'appt_type', 'appt_status',
                'additional_notes', 'preferred_date', 'preferred_time'
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