<?php
include __DIR__ . '/../../config/db_connect.php';

function getAppointments($conn) {
    $query = "SELECT a.*, c.clt_fname, c.clt_lname, p.pet_name, p.pet_type, p.pet_breed, s.service_name, s.service_fee 
              FROM appointment a
              JOIN client c ON a.client_code = c.clt_code
              JOIN pet p ON a.pet_code = p.pet_code
              JOIN service s ON a.service_code = s.service_code
              ORDER BY a.appt_datetime";
    $result = pg_query($conn, $query);
    $appointments = [];
    while ($row = pg_fetch_assoc($result)) {
        $appointments[] = $row;
    }
    return $appointments;
}

function getServices($conn) {
    $query = "SELECT service_code, service_name, service_fee FROM service ORDER BY service_name";
    $result = pg_query($conn, $query);
    $services = [];
    while ($row = pg_fetch_assoc($result)) {
        $services[] = $row;
    }
    return $services;
}

function addAppointment($conn, $data) {
    extract($data);
    $appt_datetime = $appt_date . ' ' . $appt_time;
    $query = "INSERT INTO appointment (client_code, pet_code, service_code, appt_datetime, appt_type, appt_status, additional_notes) 
                VALUES ('$client_code', '$pet_code', '$service_code', '$appt_datetime', '$appt_type', '$appt_status', '$additional_notes')";
    return pg_query($conn, $query);
}

function updateAppointment($conn, $data) {
    extract($data);
    $appt_datetime = $appt_date . ' ' . $appt_time;
    $query = "UPDATE appointment 
                SET client_code='$client_code', pet_code='$pet_code', service_code='$service_code', 
                    appt_datetime='$appt_datetime', appt_type='$appt_type', appt_status='$appt_status', 
                    additional_notes='$additional_notes' 
                WHERE appt_code='$appt_code'";
    return pg_query($conn, $query);
}

function deleteAppointment($conn, $appt_code) {
    $query = "DELETE FROM appointment WHERE appt_code='$appt_code'";
    return pg_query($conn, $query);
}
?>
