<?php
require_once __DIR__ . '/../config/Database.php';

$database = new Database();
$conn = $database->connect();

$username = $_GET['username'] ?? '';

$query = "SELECT id FROM users WHERE username = :username";
$stmt = $conn->prepare($query);
$stmt->bindParam(":username", $username);
$stmt->execute();

echo json_encode(["available" => $stmt->rowCount() === 0]);
?>
