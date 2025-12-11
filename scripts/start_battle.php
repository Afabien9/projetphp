<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION["role"])) {
    header("Location: /Projet_php/index.php");
    exit;
}

include($_SERVER['DOCUMENT_ROOT']."/Projet_php/scripts/sql-connect.php");
$sql = new SqlConnect();

$current = $_SESSION["role"]; // joueur1 ou joueur2

// Vérifier que tous les bateaux de CE joueur sont placés
$req = $sql->db->query("SELECT COUNT(*) FROM $current WHERE boat > 0");
$placed = $req->fetchColumn();

if ($placed < 17) {
    $_SESSION["error_msg"] = "Vous devez placer tous vos bateaux avant de commencer.";
    header("Location: /Projet_php/views/players-selected.php");
    exit;
}

// Passer ce joueur en mode combat
$_SESSION["mode"] = "combat";

// Initialiser l'état global de la partie : J1 commence, pas de gagnant
$sql->db->query("UPDATE game_state SET current_turn = 'joueur1', winner = NULL WHERE id = 1");

// Lancer le jeu
header("Location: /Projet_php/views/game.php");
exit;
