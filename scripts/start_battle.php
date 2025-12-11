<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION["role"])) {
    header("Location: /Projet_php/index.php");
    exit;
}

include($_SERVER['DOCUMENT_ROOT']."/Projet_php/scripts/sql-connect.php");
$sql = new SqlConnect();

// Le joueur courant
$current = $_SESSION["role"]; // joueur1 ou joueur2

// Vérifier que TOUS les bateaux sont placés
// Total cases : 5 + 4 + 3 + 3 + 2 = 17
$req = $sql->db->query("SELECT COUNT(*) FROM $current WHERE boat > 0");
$placed = $req->fetchColumn();

if ($placed < 17) {
    // On refuse de commencer la partie
    $_SESSION["error_msg"] = "Vous devez placer tous vos bateaux avant de commencer.";
    header("Location: /Projet_php/views/players-selected.php");
    exit;
}

// Mode combat
$_SESSION["mode"] = "combat";

// TOUR INITIAL → Joueur 1 commence
$_SESSION["turn"] = "joueur1";

// Supprimer éventuel gagnant précédent
unset($_SESSION["winner"]);

// Lancer partie
header("Location: /Projet_php/views/game.php");
exit;
