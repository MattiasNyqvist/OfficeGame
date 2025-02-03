<?php
require_once __DIR__ . '/../config/Database.php';

header("Content-Type: application/json");

$database = new Database();
$conn = $database->connect();

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data["token"]) || !isset($data["password"])) {
    echo json_encode(["success" => false, "message" => "Missing data"]);
    exit();
}

$token = trim($data["token"]);
$new_password = password_hash($data["password"], PASSWORD_DEFAULT);

// Check if token is valid
$query = "SELECT user_id FROM password_resets WHERE token = :token AND expires_at > NOW() LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(":token", $token);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["success" => false, "message" => "Invalid or expired token"]);
    exit();
}

// Update password
$query = "UPDATE users SET password = :password WHERE id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":password", $new_password);
$stmt->bindParam(":user_id", $user["user_id"]);
$stmt->execute();

echo json_encode(["success" => true, "message" => "Password reset successful"]);
?>
