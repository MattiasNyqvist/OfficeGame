<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

require_once __DIR__ . '/../../config/Database.php';

$database = new Database();
$conn = $database->connect();

// Hämta aktiv spelperiod
$today = date('Y-m-d');
$query = "SELECT id FROM bingo_game_periods WHERE start_date <= :today AND end_date >= :today LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(":today", $today);
$stmt->execute();
$game_period = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game_period) {
    die(json_encode(["success" => false, "message" => "No active game period."]));
}

$game_period_id = $game_period['id'];

// Hämta spelplanen (9 uppgifter)
$query = "
    SELECT bgt.id AS game_task_id, bt.title, bt.description, bt.icon, bt.points, 
           COALESCE(but.completed, 0) AS completed
    FROM bingo_game_tasks bgt
    JOIN bingo_tasks bt ON bgt.task_id = bt.id
    LEFT JOIN bingo_user_tasks but ON but.game_task_id = bgt.id AND but.user_id = :user_id
    WHERE bgt.game_period_id = :game_period_id
";
$stmt = $conn->prepare($query);
$stmt->bindParam(":game_period_id", $game_period_id);
$stmt->bindParam(":user_id", $_SESSION["user_id"]);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["success" => true, "tasks" => $tasks]);
?>
