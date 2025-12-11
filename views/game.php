<?php
// DEBUG
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include($_SERVER['DOCUMENT_ROOT'] . "/Projet_php/scripts/sql-connect.php");

$sql = new SqlConnect();

// 📌 Le joueur actuel
$current = $_SESSION["role"];     // joueur1 ou joueur2
$enemy   = ($current === 'joueur1') ? 'joueur2' : 'joueur1';

// 📌 Récupérer les deux grilles
$req = $sql->db->prepare("SELECT * FROM $enemy ORDER BY idgrid ASC");
$req->execute();
$enemyGrid = $req->fetchAll(PDO::FETCH_ASSOC);

$req2 = $sql->db->prepare("SELECT * FROM $current ORDER BY idgrid ASC");
$req2->execute();
$myGrid = $req2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Game</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/Projet_php/styles_css.css">
</head>

<body>

<?php
// *************************************************
// ⭐ SI TU ES JOUEUR 1 → TA GRILLE EN HAUT
// ⭐ SI TU ES JOUEUR 2 → TA GRILLE EN HAUT
// *************************************************

echo "<h2 style='text-align:center;'>Votre grille ($current)</h2>";
echo "<div class='grid-container'>";

foreach ($myGrid as $case) {
    if ($case['boat'] > 0) $color = 'black'; else $color = 'lightgrey';
    if ($case['checked'] == 1) $color = ($case['boat'] > 0) ? 'red' : 'blue';

    echo "<div class='cell-btn' style='background-color:$color;'></div>";
}

echo "</div><br><br>";

// *************************************************
// ⭐ GRILLE DE L’ADVERSAIRE TOUJOURS EN BAS
// ⭐ ET ELLE EST CLIQUABLE
// *************************************************

echo "<h2 style='text-align:center;'>Grille adverse ($enemy)</h2>";
echo "<div class='grid-container'>";

foreach ($enemyGrid as $case) {

    $color = 'grey';
    if ($case['checked'] == 1) {
        $color = ($case['boat'] > 0) ? 'red' : 'blue';
    }

    echo '<form method="post" action="/Projet_php/scripts/click_case.php">';
    echo '<button class="cell-btn" 
                 type="submit" 
                 name="cell" 
                 value="'.$case['idgrid'].'" 
                 style="background-color:'.$color.';">
          </button>';
    echo '</form>';
}

echo "</div>";
?>

<br><br>

<div style="text-align:center;">
    <form method="post" action="/Projet_php/scripts/reset_total.php">
        <button type="submit" name="reset_total">❌ Fin de partie (RESET)</button>
    </form>
</div>

</body>
</html>
