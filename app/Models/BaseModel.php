<?php

namespace App\Models;

use Config\Database;

class BaseModel
{
    protected $db;

    public function __construct()
    {
        // Use the singleton instance from Config\Database
        $this->db = Database::getInstance()->getConnection();
    }

}
