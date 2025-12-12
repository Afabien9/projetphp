<?php
if (session_status() === PHP_SESSION_NONE) session_start();

include($_SERVER['DOCUMENT_ROOT'] . "/Projet_php/scripts/sql-connect.php");
$sql = new SqlConnect();

$state = $sql->db->query("SELECT winner FROM game_state WHERE id = 1")
                 ->fetch(PDO::FETCH_ASSOC);

$winner = $state['winner'] ?? null;

if ($winner === null) {
    header("Location: /Projet_php/index.php");
    exit;
}

$current = $_SESSION["role"] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Victoire</title>
    <link rel="stylesheet" href="/Projet_php/CSS/victory_style.css">
</head>
<body class="victory-body">

<div class="victory-container">
    <div class="victory-card">
        <h1 class="victory-title">🎉 Victoire ! 🎉</h1>

        <p class="victory-info">
            Le joueur <strong><?= htmlspecialchars($winner) ?></strong> a remporté la partie.
        </p>

        <?php if ($current === $winner): ?>
            <p class="victory-sub">Bravo, vous avez coulé tous les bateaux adverses.</p>
        <?php else: ?>
            <p class="victory-sub">Vous regardez l'écran de victoire de l'autre joueur.</p>
        <?php endif; ?>

        <form method="post" action="/Projet_php/scripts/reset_total.php" class="victory-form">
            <button class="retry-btn">🔁 Rejouer</button>
        </form>
    </div>
</div>

</body>
</html>
