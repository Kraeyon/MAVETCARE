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
        $stmt = $this->db->prepare('
            UPDATE pet SET pet_name = ?, pet_type = ?, pet_breed = ?, pet_age = ?, pet_med_history = ?
            WHERE pet_code = ?
        ');
        return $stmt->execute([$pet_name, $pet_type, $pet_breed, $pet_age, $pet_med_history, $pet_code]);
    }

    public function deletePatient($pet_code) {
        $stmt = $this->db->prepare('DELETE FROM pet WHERE pet_code = ?');
        return $stmt->execute([$pet_code]);
    }
}

