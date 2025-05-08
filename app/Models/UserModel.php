<?php

namespace App\Models;

use Config\Database;

class UserModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function registerClient($data)
    {
        // Start transaction
        $this->db->beginTransaction();

        try {
            // Insert into client table
            $stmt1 = $this->db->prepare("
                INSERT INTO client (clt_fname, clt_initial, clt_lname, clt_contact, clt_home_address, clt_email_address)
                VALUES (:fname, :initial, :lname, :contact, :address, :email)

            ");
            $stmt1->execute([
                ':fname' => $data['first_name'],
                ':initial' => $data['middle_initial'],
                ':lname' => $data['last_name'],
                ':contact' => $data['contact'],
                ':address' => $data['address'],
                ':email' => $data['email']
            ]);


            // Get the inserted client_code
            $clientId = $this->db->lastInsertId('client_clt_code_seq');

            // Insert into sys_user
            $stmt2 = $this->db->prepare("
                INSERT INTO sys_user (username, password, role, client_code)
                VALUES (:username, :password, 'client', :client_code)
            ");
            $stmt2->execute([
                ':username' => $data['email'],
                ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
                ':client_code' => $clientId
            ]);

            // Commit the transaction
            $this->db->commit();

        } catch (\PDOException $e) {
            $this->db->rollBack();
            die("Registration failed: " . $e->getMessage());
        }
    }

    public function findByEmail($email)
{
    $stmt = $this->db->prepare("
        SELECT 
            su.user_code AS id, 
            su.username AS email, 
            su.password,                 
            su.role, 
            su.client_code,
            CONCAT(c.clt_fname, ' ', c.clt_lname) AS client_name
        FROM sys_user su
        LEFT JOIN client c ON su.client_code = c.clt_code
        WHERE su.username = :email
    ");
    $stmt->execute(['email' => $email]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}



}
