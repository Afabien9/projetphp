<?php
// DEBUG
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sécurité : si aucun rôle ⇒ retour à l'accueil
if (!isset($_SESSION["role"])) {
    header("Location: /Projet_php/index.php");
    exit;
}

// Sécurité : si pas en mode combat ⇒ retour au placement
if (!isset($_SESSION["mode"]) || $_SESSION["mode"] !== "combat") {
    header("Location: /Projet_php/views/players-selected.php");
    exit;
}

// Connexion DB
include($_SERVER['DOCUMENT_ROOT'] . "/Projet_php/scripts/sql-connect.php");
$sql = new SqlConnect();

// Rôle du joueur
$current = $_SESSION["role"];   // joueur1 ou joueur2
$enemy   = ($current === "joueur1") ? "joueur2" : "joueur1";

$myTurn = ($_SESSION["turn"] === $current);

// Rafraîchissement automatique si on attend l'autre joueur
if (!$myTurn) {
    echo "<script>
            setTimeout(() => {
                location.reload();
            }, 2000);
          </script>";
}


// Récupération de la grille joueur
$req = $sql->db->prepare("SELECT * FROM $current ORDER BY idgrid ASC");
$req->execute();
$myGrid = $req->fetchAll(PDO::FETCH_ASSOC);

// Récupération de la grille ennemie
$req2 = $sql->db->prepare("SELECT * FROM $enemy ORDER BY idgrid ASC");
$req2->execute();
$enemyGrid = $req2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bataille Navale – <?= htmlspecialchars($current) ?></title>
    <link rel="stylesheet" href="/Projet_php/styles_css.css">
    <style>
        .turnBox {
            text-align:center;
            font-size:22px;
            padding:10px;
            margin-bottom:20px;
            font-weight:bold;
        }
        .turnYou { color: green; }
        .turnWait { color: red; }
    </style>
</head>
<body>

<!-- 🎯 AFFICHAGE DU TOUR -->
<div class="turnBox <?= $myTurn ? "turnYou" : "turnWait" ?>">
    <?= $myTurn ? "🔥 À VOUS DE JOUER !" : "⏳ En attente de l'autre joueur..." ?>
</div>



<!--  1. TA GRILLE (non cliquable) -->


<h2 style="text-align:center;">Votre grille (<?= $current ?>)</h2>

<div class="grid-container">
<?php
foreach ($myGrid as $case) {

    if ($case['boat'] > 0) $color = '#333';     // bateau
    else $color = 'lightgrey';                  // eau

    if ($case['checked'] == 1) {
        $color = ($case['boat'] > 0) ? 'red' : 'blue';
    }

    echo "<div class='cell-btn' style='background-color:$color;'></div>";
}
?>
</div>

<br><br>


<!-- 2. GRILLE ADVERSE (CLIQUABLE) -->


<h2 style="text-align:center;">Grille adverse (<?= $enemy ?>)</h2>

<div class="grid-container">
<?php
foreach ($enemyGrid as $case) {

    // Par défaut on masque les bateaux ennemis
    $color = 'grey';

    if ($case['checked'] == 1) {
        $color = ($case['boat'] > 0) ? 'red' : 'blue';
    }

    // Désactiver si la case a déjà été jouée OU si ce n'est pas ton tour
    $disabled = "";
    if ($case['checked'] == 1 || !$myTurn) {
        $disabled = "disabled";
    }

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

<!-- RESET TOTAL -->
<div style="text-align:center;">
    <form method="post" action="/Projet_php/scripts/reset_total.php">
        <button type="submit" name="reset_total">❌ Fin de partie (RESET)</button>
    </form>
</div>

</body>
</html>
