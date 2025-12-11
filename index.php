<?php
session_start();

// Rôle non choisi → écran sélection
if (!isset($_SESSION["role"])) {
    include(__DIR__ . "/views/players-selected.php");
    exit;
}

// En mode placement → rester sur placement
if (!isset($_SESSION["mode"]) || $_SESSION["mode"] !== "combat") {
    include(__DIR__ . "/views/players-selected.php");
    exit;
}

// En mode combat → lancer game.php
include(__DIR__ . "/views/game.php");
exit;
