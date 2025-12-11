<?php
  include('./scripts/save_state.php');

  if (isset($_POST["joueur1"])) {
    if ($etat["j1"] === null) {
      $etat["j1"] = session_id();
      $_SESSION["role"] = "joueur1";
      save_state("./etat_joueurs.json", $etat);
    }
  }

  if (isset($_POST["joueur2"])) {
    if ($etat["j2"] === null) {
      $etat["j2"] = session_id();
      $_SESSION["role"] = "joueur2";
      save_state("./etat_joueurs.json", $etat);
    }
  }

  $role = $_SESSION["role"] ?? "Aucun rôle";
?>

<!DOCTYPE html>
<html>
  <head>
      <meta charset="UTF-8">
      <title>Joueur 1 / Joueur 2</title>
  </head>
  <body>
    <h1>Connexion aux rôles</h1>
    <h2>Votre rôle actuel : <strong><?= $role ?></strong></h2>
    <p>
      Joueur 1 : <?= $etat["j1"] ? "🟢 Occupé" : "🔴 Libre" ?><br>
      Joueur 2 : <?= $etat["j2"] ? "🟢 Occupé" : "🔴 Libre" ?>
    </p>

    <form method="post">
      <button type="submit" name="joueur1"
          <?= $etat["j1"] !== null ? "disabled" : "" ?>>
          🎮 Devenir Joueur 1
      </button>
      <button type="submit" name="joueur2"
          <?= $etat["j2"] !== null ? "disabled" : "" ?>>
          🎮 Devenir Joueur 2
      </button>
    </form>
  </body>
</html>