<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
  header('Location: compte.php');
  exit;
}

$panier_id = $_POST['panier_id'] ?? null;
if ($panier_id) {
  $stmt = $pdo->prepare("DELETE FROM panier WHERE id = ? AND utilisateur_id = ?");
  $stmt->execute([$panier_id, $_SESSION['user']['id']]);
}

header("Location: panier.php");
exit;
?>
