<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user'])) {
  header('Location: compte.php');
  exit;
}

$utilisateur_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT p.id AS panier_id, a.* FROM panier p JOIN articles a ON p.article_id = a.id WHERE p.utilisateur_id = ?");
$stmt->execute([$utilisateur_id]);
$articles = $stmt->fetchAll();
$total = array_sum(array_column($articles, 'prix'));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Panier - Agora Francia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="accueil.php">Agora Francia</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="accueil.php">Accueil</a></li>
        <li class="nav-item"><a class="nav-link" href="parcourir.php">Tout Parcourir</a></li>
        <li class="nav-item"><a class="nav-link" href="notifications.php">Notifications</a></li>
        <li class="nav-item"><a class="nav-link active" href="#">Panier</a></li>
        <?php if (isset($_SESSION['user'])): ?>
          <?php if ($_SESSION['user']['type'] === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="ajouter_article.php">Ajouter un article</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="logout.php">Se déconnecter</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="compte.php">Votre Compte</a></li>
        <?php endif; ?>
      </ul>
      <?php if (isset($_SESSION['user'])): ?>
        <span class="navbar-text text-white">
          Bonjour <strong><?= htmlspecialchars($_SESSION['user']['prenom']) ?></strong>
        </span>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="container mt-5">
  <h2>Votre panier</h2>
  <?php if (empty($articles)): ?>
    <div class="alert alert-info">Votre panier est vide.</div>
  <?php else: ?>
    <div class="list-group">
      <?php foreach ($articles as $a): ?>
        <div class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <strong><?= htmlspecialchars($a['titre']) ?></strong><br>
            <small><?= number_format($a['prix'], 2, ',', ' ') ?> €</small>
          </div>
          <form method="post" action="supprimer_panier.php" class="mb-0">
            <input type="hidden" name="panier_id" value="<?= $a['panier_id'] ?>">
            <button class="btn btn-sm btn-danger">Supprimer</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="text-end mt-4">
      <h4>Total : <span class="text-success"><?= number_format($total, 2, ',', ' ') ?> €</span></h4>
      <form action="paiement.php" method="post">
        <input type="hidden" name="total" value="<?= $total ?>">
        <button class="btn btn-primary">Passer au paiement</button>
      </form>
    </div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
