<?php
if (session_status() === PHP_SESSION_NONE) session_start();

include($_SERVER['DOCUMENT_ROOT'] . "/Projet_php/scripts/sql-connect.php");
$sql = new SqlConnect();

if (!isset($_SESSION["role"])) {
    header("Location: /Projet_php/index.php");
    exit;
}

$current = $_SESSION["role"];
$enemy   = ($current === "joueur1") ? "joueur2" : "joueur1";

// Vérifier le tour en BDD
$state = $sql->db->query("SELECT current_turn FROM game_state WHERE id = 1")
                 ->fetch(PDO::FETCH_ASSOC);

if ($state['current_turn'] !== $current) {
    header("Location: /Projet_php/views/game.php");
    exit;
}

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

// Interdit de rejouer sur une case déjà tirée
if ($case["checked"] == 1) {
    header("Location: /Projet_php/views/game.php");
    exit;
}

// Marquer la case comme jouée
$sql->db->prepare("UPDATE $enemy SET checked = 1 WHERE idgrid = ?")
        ->execute([$cell]);

$hit = ($case["boat"] > 0);

// Tour suivant
if ($hit) {
    // Touché : même joueur rejoue
    $sql->db->prepare("UPDATE game_state SET current_turn = :t WHERE id = 1")
            ->execute([':t' => $current]);
} else {
    // Raté : à l'autre joueur
    $next = ($current === "joueur1") ? "joueur2" : "joueur1";
    $sql->db->prepare("UPDATE game_state SET current_turn = :t WHERE id = 1")
            ->execute([':t' => $next]);
}

// Vérifier si l'adversaire a encore des bateaux
$req = $sql->db->query("
    SELECT COUNT(*) 
    FROM $enemy 
    WHERE boat > 0 AND checked = 0
");
$remaining = $req->fetchColumn();

if ($remaining == 0) {
    // Enregistrer gagnant globalement
    $sql->db->prepare("UPDATE game_state SET winner = :w WHERE id = 1")
            ->execute([':w' => $current]);

    header("Location: /Projet_php/views/victory.php");
    exit;
}

header("Location: /Projet_php/views/game.php");
exit;
