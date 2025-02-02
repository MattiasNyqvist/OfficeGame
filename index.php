<?php
session_start();

// Kolla om användaren är inloggad
if (!isset($_SESSION["user_id"])) {
    header("Location: /public/login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OfficeGame - Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h1>Welcome to OfficeGame!</h1>
    <p>You are logged in as <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong>.</p>

    <!-- Logout Button -->
    <a href="api/logout.php" class="btn btn-danger">Logout</a>
</body>
</html>
