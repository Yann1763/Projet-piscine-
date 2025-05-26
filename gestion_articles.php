<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'admin') {
    header("Location: compte.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gérer les articles</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="accueil.php">Agora Francia</a>
    <ul class="navbar-nav me-auto">
      <li class="nav-item"><a class="nav-link" href="accueil.php">Accueil</a></li>
      <li class="nav-item"><a class="nav-link active" href="#">Gérer les articles</a></li>
      <li class="nav-item"><a class="nav-link" href="ajouter_article.php">Ajouter un article</a></li>
      <li class="nav-item"><a class="nav-link" href="logout.php">Se déconnecter</a></li>
    </ul>
    <span class="navbar-text text-white">Bonjour <strong><?= htmlspecialchars($_SESSION['user']['prenom']) ?></strong></span>
  </div>
</nav>

<div class="container mt-5">
  <h2>Liste des articles</h2>
  <table class="table table-bordered mt-3">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Titre</th>
        <th>Type</th>
        <th>Prix</th>
        <th>Vendeur ID</th>
        <th>Date</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $stmt = $pdo->query("SELECT * FROM articles ORDER BY date_ajout DESC");
    while ($a = $stmt->fetch()):
    ?>
      <tr>
        <td><?= $a['id'] ?></td>
        <td><?= htmlspecialchars($a['titre']) ?></td>
        <td><?= $a['type_vente'] ?></td>
        <td><?= $a['prix'] ?> €</td>
        <td><?= $a['vendeur_id'] ?></td>
        <td><?= $a['date_ajout'] ?></td>
        <td>
          <form method="post" action="supprimer_article.php" onsubmit="return confirm('Supprimer cet article ?');">
            <input type="hidden" name="id" value="<?= $a['id'] ?>">
            <button class="btn btn-sm btn-danger">Supprimer</button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
