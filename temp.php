<?php
require_once __DIR__ . '/config/Database.php';

// Start session to store success message
session_start();

// Establish database connection
$database = new Database();
$conn = $database->connect();

// Handle reset action
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reset"])) {
    try {
        // Delete all users
        $conn->exec("DELETE FROM users");
        $conn->exec("ALTER TABLE users AUTO_INCREMENT = 1");

        // Delete all user_tasks
        $conn->exec("DELETE FROM user_tasks");
        $conn->exec("ALTER TABLE user_tasks AUTO_INCREMENT = 1");

        // Set success message in session
        $_SESSION["reset_success"] = true;
        header("Location: temp.php"); // Prevent form resubmission
        exit;
    } catch (Exception $e) {
        echo "<script>alert('Error resetting database: " . addslashes($e->getMessage()) . "');</script>";
    }
}

// Function to fetch and display table data
function displayTable($conn, $tableName) {
    echo "<h3>$tableName</h3>";
    echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
    
    // Fetch column names
    $stmt = $conn->query("SHOW COLUMNS FROM $tableName");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Print table headers
    echo "<tr>";
    foreach ($columns as $col) {
        echo "<th>$col</th>";
    }
    echo "</tr>";

    // Fetch and display table data
    $stmt = $conn->query("SELECT * FROM $tableName");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        foreach ($columns as $col) {
            echo "<td>" . htmlspecialchars($row[$col]) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table><br>";
}

// Fetch and display all relevant tables
$tables = ["users", "user_tasks", "teams", "tasks"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temporary Development Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h1>Development Database Viewer</h1>
    <p>This page is only for development purposes. It displays database tables and allows resetting user data.</p>

    <?php
    // Show success alert only once
    if (isset($_SESSION["reset_success"])) {
        echo "<script>alert('Database reset successfully!');</script>";
        unset($_SESSION["reset_success"]); // Remove message after displaying
    }
    ?>

    <form method="post">
        <button type="submit" name="reset" class="btn btn-danger">Reset Database</button>
    </form>

    <hr>

    <?php
    foreach ($tables as $table) {
        displayTable($conn, $table);
    }
    ?>

</body>
</html>
