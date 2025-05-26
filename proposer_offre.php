<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'acheteur') {
    header("Location: compte.php");
    exit;
}

$article_id = $_GET['id'] ?? null;
if (!$article_id) {
    die("Article non spécifié.");
}

// récupération info vendeur
$req = $pdo->prepare("SELECT vendeur_id FROM articles WHERE id = ?");
$req->execute([$article_id]);
$article = $req->fetch();
if (!$article) {
    die("Article introuvable.");
}
$vendeur_id = $article['vendeur_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prix = $_POST['prix'];
    $stmt = $pdo->prepare("INSERT INTO offres (acheteur_id, article_id, prix_propose) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user']['id'], $article_id, $prix]);

    // créer une notification pour le vendeur
    $notif = $pdo->prepare("INSERT INTO notifications (utilisateur_id, message) VALUES (?, ?)");
    $notif->execute([$vendeur_id, "Nouvelle offre sur l'un de vos articles."]);

    $_SESSION['notif'] = "Offre envoyée avec succès !";
    header("Location: parcourir.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Proposer une offre</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h2>Proposer une offre</h2>
  <form method="post">
    <label for="prix">Prix proposé (€)</label>
    <input type="number" step="0.01" name="prix" class="form-control mb-3" required>
    <button type="submit" class="btn btn-warning">Envoyer l'offre</button>
  </form>
</body>
</html>
