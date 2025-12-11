<?php
if (session_status() === PHP_SESSION_NONE) session_start();

include($_SERVER['DOCUMENT_ROOT'] . "/Projet_php/scripts/sql-connect.php");
$sql = new SqlConnect();

// Joueur courant
if (!isset($_SESSION["role"])) {
    header("Location: /Projet_php/index.php");
    exit;
}

$current = $_SESSION["role"];          // joueur1 / joueur2
$enemy   = ($current === "joueur1") ? "joueur2" : "joueur1";

// Vérifier que c’est le tour du joueur
if ($_SESSION["turn"] !== $current) {
    header("Location: /Projet_php/views/game.php");
    exit;
}

// Sécurité : case reçue ?
if (!isset($_POST["cell"])) {
    header("Location: /Projet_php/views/game.php");
    exit;
}

$cell = intval($_POST["cell"]);

// Récupérer la case ennemie
$req = $sql->db->prepare("SELECT boat, checked FROM $enemy WHERE idgrid = ?");
$req->execute([$cell]);
$case = $req->fetch(PDO::FETCH_ASSOC);

if (!$case) {
    header("Location: /Projet_php/views/game.php");
    exit;
}

// Interdit : déjà tiré
if ($case["checked"] == 1) {
    header("Location: /Projet_php/views/game.php");
    exit;
}

// Marquer la case comme "déjà jouée"
$sql->db->prepare("UPDATE $enemy SET checked = 1 WHERE idgrid = ?")->execute([$cell]);

$hit = ($case["boat"] > 0);   // touché ?

/*---------------------------------------
    1) LOGIQUE TOUR PAR TOUR
----------------------------------------*/

// Si TOUCHÉ → même joueur rejoue
if ($hit) {
    $_SESSION["turn"] = $current;
}
// Si RATÉ → à l’autre joueur
else {
    $_SESSION["turn"] = ($current === "joueur1") ? "joueur2" : "joueur1";
}


/*---------------------------------------
    2) DETECTION VICTOIRE
----------------------------------------*/

// Si l'adversaire n’a PLUS AUCUNE CASE bateau non touchée → victoire !
$req = $sql->db->query("
    SELECT COUNT(*) 
    FROM $enemy 
    WHERE boat > 0 AND checked = 0
");
$remaining = $req->fetchColumn();

if ($remaining == 0) {
    $_SESSION["winner"] = $current;
    header("Location: /Projet_php/views/victory.php");
    exit;
}


/*---------------------------------------
    3) RETOUR AU JEU
----------------------------------------*/

header("Location: /Projet_php/views/game.php");
exit;
