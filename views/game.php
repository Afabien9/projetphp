<?php
// DEBUG
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sécurité : si aucun rôle, retour à l'accueil
if (!isset($_SESSION["role"])) {
    header("Location: /Projet_php/index.php");
    exit;
}

include($_SERVER['DOCUMENT_ROOT'] . "/Projet_php/scripts/sql-connect.php");

$sql = new SqlConnect();

// 📌 Rôle du joueur courant
$current = $_SESSION["role"]; // joueur1 ou joueur2
$enemy   = ($current === "joueur1") ? "joueur2" : "joueur1";

// 📌 Récupération des 2 grilles
$req = $sql->db->prepare("SELECT * FROM $current ORDER BY idgrid ASC");
$req->execute();
$myGrid = $req->fetchAll(PDO::FETCH_ASSOC);

$req2 = $sql->db->prepare("SELECT * FROM $enemy ORDER BY idgrid ASC");
$req2->execute();
$enemyGrid = $req2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bataille Navale - <?= htmlspecialchars($current) ?></title>
    <link rel="stylesheet" href="/Projet_php/styles_css.css">
</head>
<body>

<!-- ========================== -->
<!-- ⭐ 1. TA GRILLE (non cliquable) -->
<!-- ========================== -->

<h2 style="text-align:center;">Votre grille (<?= $current ?>)</h2>

<div class="grid-container">
<?php
foreach ($myGrid as $case) {
    
    // Affichage normal des bateaux du joueur
    if ($case['boat'] > 0) $color = 'black';
    else $color = 'lightgrey';

    if ($case['checked'] == 1) {
        $color = ($case['boat'] > 0) ? 'red' : 'blue';
    }

    echo "<div class='cell-btn' style='background-color:$color;'></div>";
}
?>
</div>

<br><br>

<!-- ========================== -->
<!-- ⭐ 2. GRILLE ADVERSE (CLIQUABLE) -->
<!-- ========================== -->

<h2 style="text-align:center;">Grille adverse (<?= $enemy ?>)</h2>

<div class="grid-container">
<?php
foreach ($enemyGrid as $case) {

    // ❗ IMPORTANT : on masque TOUJOURS les bateaux adverses non touchés
    $color = 'grey'; // par défaut, l'adversaire reste invisible

    if ($case['checked'] == 1) {
        // Un tir a déjà été effectué ici
        $color = ($case['boat'] > 0) ? 'red' : 'blue';
    }

    // ⛔ Si la case a déjà été jouée → on bloque le bouton
    $disabled = ($case['checked'] == 1) ? "disabled" : "";

    echo '<form method="post" action="/Projet_php/scripts/click_case.php" style="display:inline-block;">';
    echo '<button class="cell-btn"
                 type="submit"
                 name="cell"
                 value="'. $case['idgrid'] .'"
                 style="background-color:'.$color.';"
                 '.$disabled.'>
          </button>';
    echo '</form>';
}
?>
</div>

<br><br>

<div style="text-align:center;">
    <form method="post" action="/Projet_php/scripts/reset_total.php">
        <button type="submit" name="reset_total">❌ Fin de partie (RESET)</button>
    </form>
</div>

</body>
</html>
