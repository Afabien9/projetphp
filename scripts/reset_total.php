<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include($_SERVER['DOCUMENT_ROOT'] . "/Projet_php/scripts/sql-connect.php");
$sql = new SqlConnect();

// reset des grilles
$sql->db->query("UPDATE joueur1 SET checked = 0, boat = 0");
$sql->db->query("UPDATE joueur2 SET checked = 0, boat = 0");



// 🔥 destruction totale de la session
session_start();
$_SESSION = [];
session_destroy();

// 🔥 éviter la recréation automatique d'une session par index
setcookie(session_name(), '', time() - 3600, '/');

// redirect propre
header("Location: /Projet_php/index.php");
exit;

file_put_contents(__DIR__ . '/../etat_joueurs.json', json_encode([
    "j1" => null,
    "j2" => null
]));
