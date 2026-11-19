<?php
require_once __DIR__ . '/../config/Database.php';

header("Content-Type: application/json");

$database = new Database();
$conn = $database->connect();

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data["email"])) {
    echo json_encode(["success" => false, "message" => "Email is required"]);
    exit();
}

$email = trim($data["email"]);

// Check if email exists
$query = "SELECT id FROM users WHERE email = :email";
$stmt = $conn->prepare($query);
$stmt->bindParam(":email", $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["success" => false, "message" => "Email not found"]);
    exit();
}

// Generate a unique token
$token = bin2hex(random_bytes(32));
$expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

// Store token in database
$query = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (:user_id, :token, :expires)
          ON DUPLICATE KEY UPDATE token = :token, expires_at = :expires";
$stmt = $conn->prepare($query);
$stmt->bindParam(":user_id", $user["id"]);
$stmt->bindParam(":token", $token);
$stmt->bindParam(":expires", $expires);
$stmt->execute();

// Build base URL from environment or current request
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
$baseUrl = rtrim(getenv('APP_URL') ?: "$scheme://$host", '/');

$reset_link = "$baseUrl/public/reset-password.html?token=$token";

echo json_encode(["success" => true, "message" => "A reset link has been sent.", "reset_link" => $reset_link]);
?>
