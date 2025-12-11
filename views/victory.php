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
    <link rel="stylesheet" href="/Projet_php/styles_css.css">
</head>
<body style="text-align:center; padding-top:80px;">

<h1 style="color:green; font-size:48px;">🎉 Victoire ! 🎉</h1>

<p style="font-size:22px; margin-top:20px;">
    Le joueur <strong><?= htmlspecialchars($winner) ?></strong> a remporté la partie.
</p>

<?php if ($current === $winner): ?>
    <p style="font-size:18px;">Bravo, vous avez coulé tous les bateaux adverses.</p>
<?php else: ?>
    <p style="font-size:18px;">Vous regardez l'écran de victoire de l'autre joueur.</p>
<?php endif; ?>

<form method="post" action="/Projet_php/scripts/reset_total.php" style="margin-top:40px;">
    <button style="padding:15px 25px; font-size:20px;">🔁 Rejouer</button>
</form>

</body>
</html>
