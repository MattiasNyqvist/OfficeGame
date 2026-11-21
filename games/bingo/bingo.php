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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="bingo.css">
    <script src="bingo.js" defer></script>
</head>
<body>
    <main class="bingo-page">
        <header class="topbar">
            <div class="brand">
                <span class="brand-mark" aria-hidden="true"><i class="bi bi-grid-3x3-gap-fill"></i></span>
                <span>OfficeGame</span>
            </div>
            <div class="user-menu">
                <span class="avatar" aria-hidden="true"><?php echo htmlspecialchars(strtoupper(substr($username, 0, 1))); ?></span>
                <span id="username"><?php echo htmlspecialchars($username); ?></span>
                <a href="../../api/logout.php" class="logout-link" title="Log out" aria-label="Log out"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </header>

        <section class="game-heading">
            <div>
                <p class="eyebrow">Weekly challenge</p>
                <h1>Team Bingo</h1>
            </div>
            <div class="score-card">
                <span><i class="bi bi-trophy-fill"></i> Your score</span>
                <strong><span id="user-score">0</span> pts</strong>
            </div>
        </section>

        <section class="period-card" aria-label="Game period">
            <span class="period-dot"></span>
            <div>
                <span>Current period</span>
                <strong><span id="game-start">—</span> <i>to</i> <span id="game-end">—</span></strong>
            </div>
        </section>

        <div id="bingo-board" class="bingo-grid" aria-live="polite">
            <div class="board-loading">Loading challenges…</div>
        </div>
    </main>
</body>
</html>
