<?php
session_start();


if (!isset($_SESSION["role"])) {
    header("Location: /Projet_php/views/players-selected.php");
    exit;
}

// Tant qu'on n'est pas en combat -> placement
if (!isset($_SESSION["mode"]) || $_SESSION["mode"] !== "combat") {
    header("Location: /Projet_php/views/players-selected.php");
    exit;
}

// Mode combat -> jeu
header("Location: /Projet_php/views/game.php");
exit;
