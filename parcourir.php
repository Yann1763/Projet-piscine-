<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tout Parcourir - Agora Francia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="accueil.php">Agora Francia</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="accueil.php">Accueil</a></li>
        <li class="nav-item"><a class="nav-link active" href="#">Tout Parcourir</a></li>
        <li class="nav-item"><a class="nav-link" href="notifications.php">Notifications</a></li>
        <li class="nav-item"><a class="nav-link" href="panier.php">Panier</a></li>
        <?php if (isset($_SESSION['user'])): ?>
          <?php if ($_SESSION['user']['type'] === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="ajouter_article.php">Ajouter un article</a></li>
          <?php elseif ($_SESSION['user']['type'] === 'vendeur'): ?>
            <li class="nav-item"><a class="nav-link" href="ajouter_article_negociation.php">Proposer un article</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="logout.php">Se déconnecter</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="compte.php">Votre Compte</a></li>
        <?php endif; ?>
      </ul>
      <?php if (isset($_SESSION['user'])): ?>
        <span class="navbar-text text-white">Bonjour <strong><?= htmlspecialchars($_SESSION['user']['prenom']) ?></strong></span>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- CONTENU PRINCIPAL -->
<div class="container my-5">
  <h2 class="mb-4 text-center">Tous les articles</h2>

  <?php if (isset($_SESSION['notif'])): ?>
    <div class="alert alert-success text-center"><?= $_SESSION['notif'] ?></div>
    <?php unset($_SESSION['notif']); ?>
  <?php endif; ?>

  <!-- ACHAT IMMÉDIAT -->
  <h4 class="text-primary">Achat immédiat</h4>
  <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
    <?php
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE type_vente = 'achat_immediat' ORDER BY date_ajout DESC");
    $stmt->execute();
    foreach ($stmt as $article): ?>
      <div class="col">
        <div class="card h-100">
          <img src="<?= htmlspecialchars($article['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($article['titre']) ?>">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($article['titre']) ?></h5>
            <p class="card-text"><?= htmlspecialchars($article['description']) ?></p>
            <p class="text-success fw-bold"><?= $article['prix'] ?> €</p>
            <a href="ajouter_panier.php?id=<?= $article['id'] ?>" class="btn btn-success w-100">Acheter maintenant</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- NÉGOCIATION -->
  <h4 class="text-warning">Négociation vendeur-client</h4>
  <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
    <?php
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE type_vente = 'negociation' ORDER BY date_ajout DESC");
    $stmt->execute();
    foreach ($stmt as $article): ?>
      <div class="col">
        <div class="card h-100 border-warning">
          <img src="<?= htmlspecialchars($article['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($article['titre']) ?>">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($article['titre']) ?></h5>
            <p class="card-text"><?= htmlspecialchars($article['description']) ?></p>
            <p class="text-muted fw-bold">Prix de départ : <?= $article['prix'] ?> €</p>
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['type'] === 'acheteur'): ?>
              <a href="ajouter_panier.php?id=<?= $article['id'] ?>" class="btn btn-success w-100 mb-2">Acheter maintenant</a>
              <a href="proposer_offre.php?id=<?= $article['id'] ?>" class="btn btn-outline-warning w-100">Proposer une offre</a>
            <?php else: ?>
              <span class="text-muted">Connexion acheteur requise</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- ENCHÈRES -->
  <h4 class="text-danger">Meilleure offre (enchères)</h4>
  <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
    <?php
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE type_vente = 'enchere' ORDER BY date_ajout DESC");
    $stmt->execute();
    foreach ($stmt as $article):
      $maxStmt = $pdo->prepare("SELECT MAX(montant) FROM encheres WHERE article_id = ?");
      $maxStmt->execute([$article['id']]);
      $enchere_actuelle = $maxStmt->fetchColumn() ?: $article['prix'];
    ?>
      <div class="col">
        <div class="card h-100 border-danger">
          <img src="<?= htmlspecialchars($article['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($article['titre']) ?>">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($article['titre']) ?></h5>
            <p class="card-text"><?= htmlspecialchars($article['description']) ?></p>
            <p class="text-muted fw-bold">Enchère actuelle : <?= $enchere_actuelle ?> €</p>
            <?php if (!empty($article['date_limite'])): ?>
              <p><strong>Fin :</strong> <?= date("d/m/Y H:i", strtotime($article['date_limite'])) ?></p>
            <?php endif; ?>
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['type'] === 'acheteur'): ?>
              <a href="encherir.php?id=<?= $article['id'] ?>" class="btn btn-danger w-100">Enchérir</a>
            <?php else: ?>
              <span class="text-muted">Connexion acheteur requise</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- FOOTER -->
<footer class="bg-dark text-white text-center py-4 mt-5">
  <p>Contact : contact@agorafrancia.fr | +33 1 63 93 77 89 | 75150 Paris</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
