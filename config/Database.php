<?php
class Database {
    private $conn;

    public function connect() {
        $this->conn = null;

        $envFile = __DIR__ . "/.env";
        $env = file_exists($envFile) ? parse_ini_file($envFile) : [];

        $host = getenv('DB_HOST') ?: ($env['DB_HOST'] ?? 'localhost');
        $port = getenv('DB_PORT') ?: ($env['DB_PORT'] ?? '3306');
        $db_name = getenv('DB_NAME') ?: ($env['DB_NAME'] ?? '');
        $username = getenv('DB_USER') ?: ($env['DB_USER'] ?? '');
        $password = getenv('DB_PASS') ?: ($env['DB_PASS'] ?? '');

        try {
            $this->conn = new PDO(
                "mysql:host=$host;port=$port;dbname=$db_name;charset=utf8mb4",
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
