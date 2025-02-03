<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../../public/login.html");
    exit();
}

$username = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OfficeGame - Weekly Bingo</title>
    <link rel="stylesheet" href="bingo.css">
    <script src="bingo.js" defer></script>
</head>
<body>
    <div class="container">
        <h1>Weekly Bingo</h1>
        <p>Logged in as: <strong id="username"><?php echo htmlspecialchars($username); ?></strong></p>
        <p>Game period: <strong id="game-start"></strong> - <strong id="game-end"></strong></p>

        <div id="bingo-board" class="bingo-grid"></div>

        <div class="score">
            <h3>Your Score: <span id="user-score">0</span></h3>
        </div>

        <!-- Log out button centered at the bottom -->
        <div class="logout-container">
            <a href="../../api/logout.php" class="btn btn-danger">Log Out</a>
        </div>
    </div>
</body>
</html>
