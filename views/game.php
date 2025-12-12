<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION["role"])) {
    header("Location: /Projet_php/index.php");
    exit;
}

if (!isset($_SESSION["mode"]) || $_SESSION["mode"] !== "combat") {
    header("Location: /Projet_php/views/players-selected.php");
    exit;
}

include($_SERVER['DOCUMENT_ROOT'] . "/Projet_php/scripts/sql-connect.php");
$sql = new SqlConnect();

$current = $_SESSION["role"];   // joueur1 ou joueur2
$enemy   = ($current === "joueur1") ? "joueur2" : "joueur1";

// Lire l'état global
$state = $sql->db->query("SELECT current_turn, winner FROM game_state WHERE id = 1")
                 ->fetch(PDO::FETCH_ASSOC);

$currentTurn = $state['current_turn'];
$winner      = $state['winner'];

// Gagnant défini ?
if ($winner !== null) {

    if ($winner === $current) {
        header("Location: /Projet_php/views/victory.php");
        exit;
    } else {
        header("Location: /Projet_php/views/defeat.php");
        exit;
    }
}

$myTurn = ($currentTurn === $current);

// Auto-refresh quand on attend
if (!$myTurn) {
    echo "<script>
            setTimeout(() => { location.reload(); }, 1500);
          </script>";
}

// Grilles
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
<title>Bataille Navale – <?= htmlspecialchars($current) ?></title>
<link rel="stylesheet" href="/Projet_php/CSS/game_style.css">
</head>
<body class="game-body">


<div class="turnBox <?= $myTurn ? "turnYou" : "turnWait" ?>">
<?= $myTurn ? "🔥 À VOUS DE JOUER !" : "⏳ En attente de l'autre joueur..." ?>
</div>


<h2 class="board-title">Votre grille (<?= $current ?>)</h2>
<div class="grid-container">
<?php foreach ($myGrid as $case):
$color = ($case['boat'] > 0) ? '#333' : 'lightgrey';
if ($case['checked'] == 1) $color = ($case['boat'] > 0) ? 'red' : 'blue';
?>
<div class="cell-btn" style="background-color: <?= $color ?>;"></div>
<?php endforeach; ?>
</div>


<br><br>


<h2 class="board-title">Grille adverse (<?= $enemy ?>)</h2>
<div class="grid-container">
<?php foreach ($enemyGrid as $case):
$color = 'grey';
if ($case['checked'] == 1) $color = ($case['boat'] > 0) ? 'red' : 'blue';
$disabled = ($case['checked'] == 1 || !$myTurn) ? "disabled" : "";
?>
<form method="post" action="/Projet_php/scripts/click_case.php" class="cell-form">
<button class="cell-btn enemy-btn" type="submit" name="cell" value="<?= $case['idgrid'] ?>" style="background-color: <?= $color ?>;" <?= $disabled ?>></button>
</form>
<?php endforeach; ?>
</div>


<br><br>


<div class="reset-box">
<form method="post" action="/Projet_php/scripts/reset_total.php">
<button type="submit" name="reset_total" class="reset-btn">❌ Fin de partie (RESET)</button>
</form>
</div>
