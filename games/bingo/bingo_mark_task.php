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

// Get JSON data from request
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["task_id"]) || !isset($data["action"])) {
    echo json_encode(["success" => false, "message" => "Missing task ID or action"]);
    exit();
}

$task_id = $data["task_id"];
$action = $data["action"];
$user_id = $_SESSION["user_id"];

if ($action === "complete") {
    $query = "
        INSERT INTO bingo_user_tasks (user_id, game_task_id, completed, counter) 
        VALUES (:user_id, :task_id, 1, 1) 
        ON DUPLICATE KEY UPDATE completed = 1, counter = counter + 1;
    ";
} else {
    $query = "
        UPDATE bingo_user_tasks 
        SET completed = 0 
        WHERE user_id = :user_id AND game_task_id = :task_id;
    ";
}

$stmt = $conn->prepare($query);
$stmt->bindParam(":user_id", $user_id);
$stmt->bindParam(":task_id", $task_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Task updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Database error"]);
}
?>
