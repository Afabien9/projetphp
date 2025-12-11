<?php
// DEBUG : afficher les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Toujours inclure sql-connect avec chemin ABSOLU
include($_SERVER['DOCUMENT_ROOT'] . "/Projet_php/scripts/sql-connect.php");

// Vérifier si on a reçu la cellule cliquée
if (!isset($_POST["cell"])) {
    echo "Aucune cellule reçue.";
    exit;
}

// Vérifier si la session contient un rôle
if (!isset($_SESSION["role"])) {
    echo "Erreur : aucun rôle dans la session.";
    exit;
}

$sql = new SqlConnect();

// Déterminer quelle grille mettre à jour
$player = ($_SESSION["role"] === 'joueur1') ? 'joueur2' : 'joueur1';

// Debug rapide
// echo "Mise à jour grille : $player<br>";

$query = "
    UPDATE $player
    SET checked = CASE WHEN checked = 0 THEN 1 ELSE 0 END
    WHERE idgrid = :cell
";

$req = $sql->db->prepare($query);
$req->execute(['cell' => $_POST["cell"]]);

// Retour au jeu
header("Location: /Projet_php/index.php");
exit;
?>
