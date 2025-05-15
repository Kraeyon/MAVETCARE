<?php
namespace App\Models;

use PDO;

class AdminAppointmentModel extends BaseModel {
    protected $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAppointments($searchTerm = null, $sortBy = 'appt_datetime', $orderDirection = 'ASC') {
        $query = '
            SELECT a.*, c.clt_fname, c.clt_lname, p.pet_name, p.pet_type, p.pet_breed, s.service_name, s.service_fee
            FROM appointment a
            JOIN client c ON a.client_code = c.clt_code
            JOIN pet p ON a.pet_code = p.pet_code
            LEFT JOIN service s ON a.service_code = s.service_code
            WHERE 1=1';
        
        $params = [];
        
        // Add search condition if searchTerm is provided
        if (!empty($searchTerm)) {
            $query .= " AND (c.clt_fname LIKE ? OR c.clt_lname LIKE ? OR p.pet_name LIKE ? OR s.service_name LIKE ? OR a.status LIKE ? OR a.appt_code = ?)";
            $params = [
                "%$searchTerm%", 
                "%$searchTerm%", 
                "%$searchTerm%", 
                "%$searchTerm%", 
                "%$searchTerm%",
                $searchTerm // For exact match on ID
            ];
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
        $stmt = $this->db->prepare('
            INSERT INTO appointment (client_code, pet_code, service_code, appt_datetime, appointment_type, status, additional_notes)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ');
        return $stmt->execute([$client_code, $pet_code, $service_code, $appt_datetime, $appointment_type, $status, $additional_notes]);
    }

    public function updateAppointment($appt_code, $client_code, $pet_code, $service_code, $appt_datetime, $appointment_type, $status, $additional_notes) {
        $stmt = $this->db->prepare('
            UPDATE appointment 
            SET client_code = ?, pet_code = ?, service_code = ?, appt_datetime = ?, appointment_type = ?, status = ?, additional_notes = ?
            WHERE appt_code = ?
        ');
        return $stmt->execute([$client_code, $pet_code, $service_code, $appt_datetime, $appointment_type, $status, $additional_notes, $appt_code]);
    }

    public function deleteAppointment($appt_code) {
        $stmt = $this->db->prepare('DELETE FROM appointment WHERE appt_code = ?');
        return $stmt->execute([$appt_code]);
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
            JOIN service s ON a.service_code = s.service_code
            WHERE a.appt_code = ?
        ');
        $stmt->execute([$appt_code]);
        return $stmt->fetch();
    }
}
?>
