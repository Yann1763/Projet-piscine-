<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Votre Compte - Agora Francia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="accueil.php">Agora Francia</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="accueil.php">Accueil</a></li>
        <li class="nav-item"><a class="nav-link" href="parcourir.php">Tout Parcourir</a></li>
        <li class="nav-item"><a class="nav-link" href="notifications.php">Notifications</a></li>
        <li class="nav-item"><a class="nav-link" href="panier.php">Panier</a></li>

        <?php if (isset($_SESSION['user'])): ?>
          <?php if ($_SESSION['user']['type'] === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="ajouter_article.php">Ajouter un article</a></li>
          <?php elseif ($_SESSION['user']['type'] === 'vendeur'): ?>
            <li class="nav-item"><a class="nav-link" href="ajouter_article_negociation.php">Proposer un article (négociation)</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link active" href="#">Votre Compte</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Se déconnecter</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link active" href="#">Votre Compte</a></li>
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

<!-- CONTENU -->
<div class="container mt-5">
  <?php if (isset($_SESSION['user'])): ?>
    <!-- Utilisateur connecté -->
    <div class="alert alert-success">
      Bonjour <strong><?= htmlspecialchars($_SESSION['user']['prenom']) ?></strong> 👋<br>
      Type de compte : <strong><?= htmlspecialchars($_SESSION['user']['type']) ?></strong>
    </div>

    <?php if ($_SESSION['user']['type'] === 'admin'): ?>
      <p>En tant qu'admin, vous pouvez ajouter tous types d'articles à la vente.</p>
      <a href="ajouter_article.php" class="btn btn-danger">Ajouter un article (tous types)</a>
    <?php elseif ($_SESSION['user']['type'] === 'vendeur'): ?>
      <p>En tant que vendeur, vous pouvez seulement proposer des articles en <strong>vente par négociation</strong>.</p>
      <a href="ajouter_article_negociation.php" class="btn btn-warning">Proposer un article (négociation)</a>
    <?php else: ?>
      <p>Bienvenue ! Vous pouvez parcourir les articles, gérer votre panier et vos notifications.</p>
    <?php endif; ?>

    <a href="logout.php" class="btn btn-outline-secondary mt-3">Se déconnecter</a>

  <?php else: ?>
    <!-- Formulaire de Connexion -->
    <h2>Connexion</h2>
    <form action="login.php" method="post">
      <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
      <input type="password" name="mot_de_passe" class="form-control mb-3" placeholder="Mot de passe" required>
      <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>

    <hr class="my-5">

    <!-- Formulaire d'inscription -->
    <h2>Créer un compte</h2>
    <form action="register.php" method="post">
      <input type="text" name="prenom" class="form-control mb-2" placeholder="Prénom" required>
      <input type="text" name="nom" class="form-control mb-2" placeholder="Nom" required>
      <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
      <input type="password" name="mot_de_passe" class="form-control mb-3" placeholder="Mot de passe" required>
      <select name="type_utilisateur" class="form-control mb-3" required>
        <option value="acheteur">Acheteur</option>
        <option value="vendeur">Vendeur</option>
        <option value="admin">Admin</option>
      </select>
      <button type="submit" class="btn btn-success">Créer un compte</button>
    </form>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
