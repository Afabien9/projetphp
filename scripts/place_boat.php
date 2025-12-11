<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION["role"])) {
    exit("NO ROLE");
}

$table = $_SESSION["role"]; // "joueur1" ou "joueur2"

// Récupérer le JSON envoyé par fetch
$data = json_decode(file_get_contents("php://input"), true);

include($_SERVER['DOCUMENT_ROOT']."/Projet_php/scripts/sql-connect.php");
$sql = new SqlConnect();

/* ---------------------------------------------------
   1) SUPPRESSION D’UN BATEAU (deleteShip)
----------------------------------------------------*/
if (isset($data["deleteShip"])) {
    $shipId = intval($data["deleteShip"]);

    $stmt = $sql->db->prepare("UPDATE $table SET boat = 0 WHERE boat = ?");
    $stmt->execute([$shipId]);

    echo "DELETED";
    exit;
}

/* ---------------------------------------------------
   2) PLACEMENT / DÉPLACEMENT D’UN BATEAU
      (shipId + positions[])
----------------------------------------------------*/
if (!isset($data["shipId"], $data["positions"]) || !is_array($data["positions"])) {
    exit("INVALID DATA");
}

$shipId    = intval($data["shipId"]);
$positions = array_map('intval', $data["positions"]);

/* ---------------------------------------------------
   2.1) Vérifier que les cases demandées existent
----------------------------------------------------*/
foreach ($positions as $id) {
    if ($id < 1 || $id > 100) {
        exit("INVALID POS");
    }
}

/* ---------------------------------------------------
   2.2) Vérifier qu’il n’y a PAS d’autre bateau 
        sur les cases demandées (collision directe)
----------------------------------------------------*/
$placeholders = implode(",", array_fill(0, count($positions), "?"));
$query = "SELECT COUNT(*)
          FROM $table
          WHERE idgrid IN ($placeholders)
          AND boat <> 0
          AND boat <> ?";

$check = $sql->db->prepare($query);
$params = $positions;
$params[] = $shipId;
$check->execute($params);

if ($check->fetchColumn() > 0) {
    echo "collision";
    exit;
}

/* ---------------------------------------------------
   2.3) Interdire QUE LES BATEAUX SE TOUCHENT
        (voisins horizontaux, verticaux, diagonaux)
----------------------------------------------------*/
foreach ($positions as $id) {
    $row = intdiv($id - 1, 10);
    $col = ($id - 1) % 10;

    $around = [
        [$row-1, $col-1], [$row-1, $col], [$row-1, $col+1],
        [$row,   $col-1],                 [$row,   $col+1],
        [$row+1, $col-1], [$row+1, $col], [$row+1, $col+1],
    ];

    foreach ($around as [$r, $c]) {
        if ($r < 0 || $r > 9 || $c < 0 || $c > 9) {
            continue; // hors grille
        }

        $nid = $r * 10 + $c + 1;

        // On ignore les cases du bateau lui-même
        if (in_array($nid, $positions, true)) {
            continue;
        }

        $st = $sql->db->prepare("SELECT boat FROM $table WHERE idgrid = ?");
        $st->execute([$nid]);
        $b = $st->fetchColumn();

        if ($b > 0 && $b != $shipId) {
            echo "adjacent_error";
            exit;
        }
    }
}

/* ---------------------------------------------------
   2.4) Effacer l’ancienne position du bateau
----------------------------------------------------*/
$clear = $sql->db->prepare("UPDATE $table SET boat = 0 WHERE boat = ?");
$clear->execute([$shipId]);

/* ---------------------------------------------------
   2.5) Enregistrer les nouvelles cases
----------------------------------------------------*/
$update = $sql->db->prepare("UPDATE $table SET boat = ? WHERE idgrid = ?");
foreach ($positions as $p) {
    $update->execute([$shipId, $p]);
}

echo "OK";
exit;
