<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: public/login.html");
    exit();
}

// Skicka inloggade användare till Bingo-spelets HTML-fil
header("Location: games/bingo/bingo.php");
exit();
?>
