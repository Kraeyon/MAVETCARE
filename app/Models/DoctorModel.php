<?php

namespace App\Models;

class DoctorModel extends BaseModel
{
    // Get the list of doctors
    public function getDoctors() {
        $stmt = $this->db->prepare("SELECT staff_code, staff_name, staff_position, staff_schedule FROM veterinary_staff WHERE LOWER(staff_position) LIKE '%doctor%'");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    // Update the doctor's schedule in the database
    public function updateDoctorSchedule($staffCode, $newSchedule) {
        $stmt = $this->db->prepare("UPDATE veterinary_staff SET staff_schedule = :schedule WHERE staff_code = :staffCode");
        $stmt->bindParam(':schedule', $newSchedule, \PDO::PARAM_STR);
        $stmt->bindParam(':staffCode', $staffCode, \PDO::PARAM_INT);
        $stmt->execute();
    }
}

