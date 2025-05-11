<?php
namespace App\Models;

use PDO;

class PatientModel extends BaseModel{
    protected $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAllPatients() {
        $stmt = $this->db->query('SELECT * FROM pet');
        return $stmt->fetchAll();
    }

    public function addPatient($client_code, $pet_name, $pet_type, $pet_breed, $pet_age, $pet_med_history) {
        $stmt = $this->db->prepare('INSERT INTO pet (client_code, pet_name, pet_type, pet_breed, pet_age, pet_med_history) VALUES (?, ?, ?, ?, ?, ?)');
        return $stmt->execute([$client_code, $pet_name, $pet_type, $pet_breed, $pet_age, $pet_med_history]);
    }

    public function updatePatient($pet_code, $pet_name, $pet_type, $pet_breed, $pet_age, $pet_med_history) {
        $stmt = $this->db->prepare('UPDATE pet SET pet_name = ?, pet_type = ?, pet_breed = ?, pet_age = ?, pet_med_history = ? WHERE pet_code = ?');
        return $stmt->execute([$pet_name, $pet_type, $pet_breed, $pet_age, $pet_med_history, $pet_code]);
    }

    public function deletePatient($pet_code) {
        $stmt = $this->db->prepare('DELETE FROM pet WHERE pet_code = ?');
        return $stmt->execute([$pet_code]);
    }
}
