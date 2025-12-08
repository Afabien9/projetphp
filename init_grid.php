<?php

// -----------------------------------------------------
// 1) GRILLE
// -----------------------------------------------------

$grid = [
    [3, 0, 0, 0, 0, 0, 0, 2, 2, 0],
    [3, 0, 0, 0, 0, 0, 0, 0, 0, 0],
    [3, 0, 0, 0, 0, 0, 0, 0, 0, 0],
    [0, 0, 0, 0, 0, 2, 2, 0, 0, 0],
    [0, 0, 0, 0, 0, 5, 0, 0, 0, 4],
    [0, 0, 0, 0, 0, 5, 0, 0, 0, 4],
    [0, 0, 0, 0, 0, 5, 0, 0, 0, 4],
    [3, 3, 3, 0, 0, 5, 0, 0, 0, 4],
    [0, 0, 0, 0, 0, 5, 0, 0, 0, 0],
    [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
    [0, 0, 0, 0, 5, 5, 5, 5, 5, 0],
    [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
];


// -----------------------------------------------------
// 2) FONCTION DE CONNEXION PDO
// -----------------------------------------------------
function connectDB() {

    $host = "localhost";
    $dbname = "battleship";
    $user  = "root";
    $pass  = "root"; // mot de passe MAMP

    try {

        // Connexion PDO
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);

        // Retourne la connexion si OK
        return $pdo;

    } catch (PDOException $e) {
        die("Erreur connexion : " . $e->getMessage());
    }
}



// -----------------------------------------------------
// 3) INSERTION D'UNE CASE
// -----------------------------------------------------

function insertCase($pdo, $row, $col, $boatId) {

    // Préparer la requête SQL avec les bons noms de colonnes
    $stmt = $pdo->prepare("INSERT INTO cases_bateaux (`row`, `col`, bateau_id) VALUES (?, ?, ?)");

    // Exécuter la requête
    $stmt->execute([
        $row,
        $col,
        $boatId
    ]);
}


// -----------------------------------------------------
// 4) PARCOURS COMPLET DE LA GRILLE
// -----------------------------------------------------

function initGrid($pdo, $grid) {

    $row = 0;
    while ($row < 12) {   // 12 lignes dans ta grille

        $col = 0;
        while ($col < 10) {  // 10 colonnes

            $value = $grid[$row][$col];  // Lire la valeur de la case

            if ($value != 0) {           // Si ce n'est pas une case vide
                insertCase($pdo, $row, $col, $value);   // On insère en base
            }

            $col++; // case suivante
        }

        $row++; // ligne suivante
    }
}




// -----------------------------------------------------
// 5) EXÉCUTION DU SCRIPT
// -----------------------------------------------------

$pdo = connectDB();      // connexion MySQL
initGrid($pdo, $grid);   // insertion des bateaux

echo "Insertion terminée !";

