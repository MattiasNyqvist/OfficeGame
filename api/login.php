<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/../config/Database.php';

$data = json_decode(file_get_contents("php://input"), true);

// Check if username and password are provided
if (!isset($data["username"], $data["password"])) {
    echo json_encode(["success" => false, "message" => "Please provide both username and password."]);
    exit;
}

$username = trim($data["username"]);
$password = $data["password"];

try {
    $database = new Database();
    $conn = $database->connect();

    // Fetch user by username
    $query = "SELECT id, username, password FROM users WHERE username = :username LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        // Start session and store user ID
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];

        echo json_encode(["success" => true, "message" => "Login successful."]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid username or password."]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error occurred."]);
}
?>
