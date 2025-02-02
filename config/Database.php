<?php
class Database {
    private $conn;

    public function connect() {
        $this->conn = null;

        // Load .env file
        $env = parse_ini_file(__DIR__ . "/.env");

        $host = $env['DB_HOST'];
        $db_name = $env['DB_NAME'];
        $username = $env['DB_USER'];
        $password = $env['DB_PASS'];

        try {
            $this->conn = new PDO(
                "mysql:host=$host;dbname=$db_name;charset=utf8",
                $username,
                $password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database Connection Error");
        }
        return $this->conn;
    }
}
?>
