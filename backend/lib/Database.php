<?php

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $host = "localhost";      
        $user = "root";
        $pass = "";
        $db   = "yolobard";       

        $this->conn = new mysqli($host, $user, $pass, $db);


        if ($this->conn->connect_error) {
            die("MySQL connection failed: " . $this->conn->connect_error);
        }

        $this->conn->set_charset("utf8mb4");
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>
