<?php
require_once __DIR__ . '/../../config/Database.php';

$database = new Database();
$conn = $database->connect();

echo "<h3>Running Bingo Setup...</h3>";

// Get current week's start and end date
$start_of_week = date('Y-m-d', strtotime('monday this week'));
$end_of_week = date('Y-m-d', strtotime('sunday this week'));

echo "Game period: $start_of_week - $end_of_week<br>";

// Delete old game period and tasks before creating a new one
echo "Cleaning up old game data...<br>";
$delete_user_tasks = "DELETE FROM bingo_user_tasks";
$delete_game_tasks = "DELETE FROM bingo_game_tasks";
$delete_game_periods = "DELETE FROM bingo_game_periods";

$conn->exec($delete_user_tasks);
$conn->exec($delete_game_tasks);
$conn->exec($delete_game_periods);

echo "Old game data removed.<br>";

// Insert new game period
echo "Creating new game period...<br>";
$insert_query = "INSERT INTO bingo_game_periods (start_date, end_date) VALUES (:start_date, :end_date)";
$stmt = $conn->prepare($insert_query);
$stmt->bindParam(":start_date", $start_of_week);
$stmt->bindParam(":end_date", $end_of_week);
if ($stmt->execute()) {
    echo "Game period created successfully!<br>";
    $game_period_id = $conn->lastInsertId();
} else {
    echo "Error creating game period.<br>";
    exit();
}

// Select 9 random active tasks
$task_query = "SELECT id FROM bingo_tasks WHERE active = 1 ORDER BY RAND() LIMIT 9";
$stmt = $conn->query($task_query);
$tasks = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($tasks)) {
    echo "Error: No active tasks found!<br>";
    exit();
}

// Insert selected tasks into game board
foreach ($tasks as $task_id) {
    $insert_task_query = "INSERT INTO bingo_game_tasks (game_period_id, task_id) VALUES (:game_period_id, :task_id)";
    $stmt = $conn->prepare($insert_task_query);
    $stmt->bindParam(":game_period_id", $game_period_id);
    $stmt->bindParam(":task_id", $task_id);
    if ($stmt->execute()) {
        echo "Task $task_id added to game board.<br>";
    } else {
        echo "Error adding task $task_id.<br>";
    }
}

echo "<br>Setup complete!";
?>
