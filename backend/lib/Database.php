<?php

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        // UPDATE THESE VALUES FOR YOUR MYSQL SERVER
        $host = "localhost";      // or 127.0.0.1
        $user = "root";
        $pass = "";
        $db   = "yolobard";       // same database name

        // Create MySQLi connection
        $this->conn = new mysqli($host, $user, $pass, $db);

        // Check connection
        if ($this->conn->connect_error) {
            die("MySQL connection failed: " . $this->conn->connect_error);
        }

        // Ensure UTF-8 encoding (best practice)
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
