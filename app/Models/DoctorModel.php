<?php

namespace App\Models;

class DoctorModel extends BaseModel
{
    public function getAllDoctors()
    {
        $query = "SELECT staff_name, staff_position, staff_contact, staff_email_address, staff_schedule FROM veterinary_staff";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(); // Returns an array of rows
        } catch (\PDOException $e) {
            throw new \Exception("Database query error: " . $e->getMessage());
        }
    }
}
