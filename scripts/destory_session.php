<?php
  session_start();
  include('./save_state.php');

  $fichier = "../etat_joueurs.json";
  $etat = ["j1" => null, "j2" => null];
  save_state("../etat_joueurs.json", $etat);

  session_unset();
  session_destroy();