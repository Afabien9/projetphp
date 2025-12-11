<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/Projet_php/scripts/sql-connect.php");

if (!isset($_SESSION["role"]) || !isset($_POST["cell"])) {
    header("Location: /Projet_php/index.php");
    exit;
}

$current = $_SESSION["role"];
$enemy   = ($current === "joueur1") ? "joueur2" : "joueur1";
$cellId  = (int) $_POST["cell"];

$sql = new SqlConnect();

// On marque le tir sur la grille adverse
$req = $sql->db->prepare("UPDATE $enemy SET checked = 1 WHERE idgrid = :id");
$req->execute([':id' => $cellId]);

header("Location: /Projet_php/index.php");
exit;
