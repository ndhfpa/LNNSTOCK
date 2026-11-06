<?php
// bagian bersama start

abstract class Model {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }
}

// bagian bersama end

