<?php
if (session_status() === PHP_SESSION_NONE) session_start();

include($_SERVER['DOCUMENT_ROOT'] . "/Projet_php/scripts/sql-connect.php");
$sql = new SqlConnect();

// Réinitialiser les grilles
$sql->db->query("UPDATE joueur1 SET boat = 0, checked = 0");
$sql->db->query("UPDATE joueur2 SET boat = 0, checked = 0");

// Réinitialiser l'état global
$sql->db->query("UPDATE game_state SET current_turn = 'joueur1', winner = NULL WHERE id = 1");

// Réinitialiser la session
$_SESSION = [];
session_destroy();

// Retour à l'accueil
header("Location: /Projet_php/index.php");
exit;
