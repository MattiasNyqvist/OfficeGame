<?php
require_once __DIR__ . '/../config/Database.php';

$database = new Database();
$conn = $database->connect();

$query = "SELECT id, name FROM teams";
$stmt = $conn->prepare($query);
$stmt->execute();
$teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["teams" => $teams]);
?>
