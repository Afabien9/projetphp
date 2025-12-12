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
<html>
<head>
<meta charset="UTF-8">
<title>Défaite</title>
<link rel="stylesheet" href="/Projet_php/CSS/defeat_style.css">
</head>
<body class="defeat-body">


<div class="defeat-container">
<div class="defeat-card">
<h1 class="defeat-title">💥 Défaite 💥</h1>


<p class="defeat-info">
Le joueur <strong><?= htmlspecialchars($winner) ?></strong> a remporté la partie.
</p>


<?php if ($current !== null && $current !== $winner): ?>
<p class="defeat-sub">Tous vos bateaux ont été coulés.</p>
<?php endif; ?>


<form method="post" action="/Projet_php/scripts/reset_total.php" class="defeat-form">
<button class="retry-btn">🔁 Rejouer</button>
</form>
</div>
</div>


</body>
</html>
