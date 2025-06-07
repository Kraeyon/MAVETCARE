<?php
namespace App\Models;

use PDO;
use App\Utils\StatusHelper;

class AdminAppointmentModel extends BaseModel {
    protected $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAppointments($searchTerm = null, $sortBy = 'appt_datetime', $orderDirection = 'ASC') {
        try {
            $query = '
                SELECT a.*, c.clt_fname, c.clt_lname, c.clt_code, p.pet_name, p.pet_type, p.pet_breed, s.service_name, s.service_fee
                FROM appointment a
                JOIN client c ON a.client_code = c.clt_code
                JOIN pet p ON a.pet_code = p.pet_code
                LEFT JOIN service s ON a.service_code = s.service_code
                WHERE 1=1';
            
            $params = [];
            
            // Add search condition if searchTerm is provided
            if (!empty($searchTerm)) {
                $searchTermLike = "%$searchTerm%";
                $query .= " AND (
                            c.clt_fname LIKE :search_fname 
                            OR c.clt_lname LIKE :search_lname 
                            OR CONCAT(c.clt_fname, ' ', c.clt_lname) LIKE :search_fullname
                            OR p.pet_name LIKE :search_pet 
                            OR p.pet_breed LIKE :search_breed
                            OR CONCAT(p.pet_name, ' (', p.pet_breed, ')') LIKE :search_pet_with_breed
                            OR p.pet_type LIKE :search_pet_type
                            OR s.service_name LIKE :search_service 
                            OR UPPER(a.status) LIKE UPPER(:search_status)";
                
                $params[':search_fname'] = $searchTermLike;
                $params[':search_lname'] = $searchTermLike;
                $params[':search_fullname'] = $searchTermLike;
                $params[':search_pet'] = $searchTermLike;
                $params[':search_breed'] = $searchTermLike;
                $params[':search_pet_with_breed'] = $searchTermLike;
                $params[':search_pet_type'] = $searchTermLike;
                $params[':search_service'] = $searchTermLike;
                $params[':search_status'] = $searchTermLike;
                
                // Add exact ID matches only if searchTerm is numeric
                if (is_numeric($searchTerm)) {
                    $query .= " OR a.appt_code = :search_appt_id";
                    $query .= " OR c.clt_code = :search_client_id";
                    $query .= " OR p.pet_code = :search_pet_id";
                    
                    $params[':search_appt_id'] = $searchTerm;
                    $params[':search_client_id'] = $searchTerm;
                    $params[':search_pet_id'] = $searchTerm;
                }
                
                $query .= ")";
            }
            
            // Add sorting
            $validColumns = [
                'appt_code' => 'a.appt_code',
                'client_name' => 'CONCAT(c.clt_fname, \' \', c.clt_lname)',
                'pet_name' => 'p.pet_name',
                'service_name' => 's.service_name',
                'appt_datetime' => 'a.appt_datetime',
                'appointment_type' => 'a.appointment_type',
                'status' => 'a.status'
            ];
            
            $sortColumn = isset($validColumns[$sortBy]) ? $validColumns[$sortBy] : 'a.appt_datetime';
            $orderDir = ($orderDirection === 'DESC') ? 'DESC' : 'ASC';
            
            $query .= " ORDER BY $sortColumn $orderDir";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            // Log the error
            error_log("PDO Exception in getAppointments: " . $e->getMessage());
            // Return empty array to avoid breaking the UI
            return [];
        } catch (\Exception $e) {
            // Log the error
            error_log("General Exception in getAppointments: " . $e->getMessage());
            // Return empty array to avoid breaking the UI
            return [];
        }
    }

    public function searchAppointments($searchTerm) {
        return $this->getAppointments($searchTerm);
    }

    public function findAppointmentById($id) {
        if (!is_numeric($id)) {
            return null;
        }
        
        $stmt = $this->db->prepare('
            SELECT a.*, c.clt_fname, c.clt_lname, p.pet_name, p.pet_type, p.pet_breed, s.service_name, s.service_fee
            FROM appointment a
            JOIN client c ON a.client_code = c.clt_code
            JOIN pet p ON a.pet_code = p.pet_code
            LEFT JOIN service s ON a.service_code = s.service_code
            WHERE a.appt_code = ?
        ');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getServices() {
        $stmt = $this->db->query('SELECT service_code, service_name, service_fee FROM service ORDER BY service_name');
        return $stmt->fetchAll();
    }

    public function addAppointment($client_code, $pet_code, $service_code, $appt_datetime, $appointment_type, $status, $additional_notes) {
        // Ensure status is properly formatted for database
        $status = StatusHelper::getDbStatus($status);
        
        $stmt = $this->db->prepare('
            INSERT INTO appointment (client_code, pet_code, service_code, appt_datetime, appointment_type, status, additional_notes)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ');
        return $stmt->execute([$client_code, $pet_code, $service_code, $appt_datetime, $appointment_type, $status, $additional_notes]);
    }

    public function updateAppointment($appt_code, $client_code, $pet_code, $service_code, $appt_datetime, $appointment_type, $status, $additional_notes) {
        // Ensure status is properly formatted for database
        $status = StatusHelper::getDbStatus($status);
        
        $stmt = $this->db->prepare('
            UPDATE appointment 
            SET client_code = ?, pet_code = ?, service_code = ?, appt_datetime = ?, appointment_type = ?, status = ?, additional_notes = ?
            WHERE appt_code = ?
        ');
        return $stmt->execute([$client_code, $pet_code, $service_code, $appt_datetime, $appointment_type, $status, $additional_notes, $appt_code]);
    }

    /**
     * Archive an appointment instead of deleting it
     * 
     * @param int $appt_code Appointment ID
     * @return bool Success status
     */
    public function archiveAppointment($appt_code) {
        try {
            $stmt = $this->db->prepare('UPDATE appointment SET status = ? WHERE appt_code = ?');
            return $stmt->execute(['ARCHIVED', $appt_code]);
        } catch (\PDOException $e) {
            error_log("Error archiving appointment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * @deprecated Use archiveAppointment() instead
     */
    public function deleteAppointment($appt_code) {
        // Redirect to archive method instead of deleting
        return $this->archiveAppointment($appt_code);
    }

    public function getClients() {
        $stmt = $this->db->query('SELECT clt_code, clt_fname, clt_lname FROM client ORDER BY clt_fname, clt_lname');
        return $stmt->fetchAll();
    }

    public function getPetsByClient($client_code) {
        $stmt = $this->db->prepare('SELECT pet_code, pet_name FROM pet WHERE client_code = ?');
        $stmt->execute([$client_code]);
        return $stmt->fetchAll();
    }

    public function getAppointmentById($appt_code) {
        $stmt = $this->db->prepare('
            SELECT a.*, c.clt_fname, c.clt_lname, p.pet_name, p.pet_type, p.pet_breed, s.service_name, s.service_fee
            FROM appointment a
            JOIN client c ON a.client_code = c.clt_code
            JOIN pet p ON a.pet_code = p.pet_code
            LEFT JOIN service s ON a.service_code = s.service_code
            WHERE a.appt_code = ?
        ');
        $stmt->execute([$appt_code]);
        return $stmt->fetch();
    }
    
    /**
     * Get appointments filtered by status
     * 
     * @param string $status Status to filter by
     * @return array List of appointments with the specified status
     */
    public function getAppointmentsByStatus($status) {
        // Convert to proper database format
        $dbStatus = StatusHelper::getDbStatus($status);
        error_log("Getting appointments with status: $dbStatus");
        
        $stmt = $this->db->prepare('
            SELECT a.*, c.clt_fname, c.clt_lname, p.pet_name, p.pet_type, p.pet_breed, s.service_name, s.service_fee
            FROM appointment a
            JOIN client c ON a.client_code = c.clt_code
            JOIN pet p ON a.pet_code = p.pet_code
            LEFT JOIN service s ON a.service_code = s.service_code
            WHERE a.status = ?
            ORDER BY a.appt_datetime ASC
        ');
        $stmt->execute([$dbStatus]);
        return $stmt->fetchAll();
    }

    /**
     * Get archived appointments
     * 
     * @return array List of archived appointments
     */
    public function getArchivedAppointments() {
        $stmt = $this->db->prepare('
            SELECT a.*, c.clt_fname, c.clt_lname, p.pet_name, p.pet_type, p.pet_breed, s.service_name, s.service_fee
            FROM appointment a
            JOIN client c ON a.client_code = c.clt_code
            JOIN pet p ON a.pet_code = p.pet_code
            LEFT JOIN service s ON a.service_code = s.service_code
            WHERE UPPER(a.status) = ?
            ORDER BY a.preferred_date DESC, a.preferred_time DESC
        ');
        $stmt->execute(['ARCHIVED']);
        return $stmt->fetchAll();
    }

    /**
     * Restore an archived appointment
     * 
     * @param int $appt_code Appointment ID
     * @return bool Success status
     */
    public function restoreAppointment($appt_code) {
        try {
            $stmt = $this->db->prepare('UPDATE appointment SET status = ? WHERE appt_code = ? AND status = ?');
            return $stmt->execute(['PENDING', $appt_code, 'ARCHIVED']);
        } catch (\PDOException $e) {
            error_log("Error restoring appointment: " . $e->getMessage());
            return false;
        }
    }

    public function getServicesForDropdown() {
        try {
            $stmt = $this->db->query('SELECT service_code, service_name, service_fee FROM service WHERE status = \'ACTIVE\' OR status IS NULL ORDER BY service_name');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error fetching services: " . $e->getMessage());
            return [];
        }
    }
}
?>
