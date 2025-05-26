<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'vendeur') {
  die("Accès refusé");
}

$vendeur_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("
  SELECT o.id AS offre_id, o.prix_propose, o.statut, o.date_proposition,
         a.titre AS article_titre,
         u.prenom AS acheteur_prenom, u.nom AS acheteur_nom
  FROM offres o
  JOIN articles a ON o.article_id = a.id
  JOIN utilisateurs u ON o.acheteur_id = u.id
  WHERE a.vendeur_id = ?
  ORDER BY o.date_proposition DESC
");
$stmt->execute([$vendeur_id]);
$offres = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Offres reçues</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

  <h2>Offres reçues sur vos articles</h2>

  <?php if (isset($_SESSION['notif'])): ?>
    <div class="alert alert-success"><?= $_SESSION['notif'] ?></div>
    <?php unset($_SESSION['notif']); ?>
  <?php endif; ?>

  <?php if (empty($offres)): ?>
    <div class="alert alert-info mt-4">Aucune offre reçue pour l’instant.</div>
  <?php else: ?>
    <table class="table table-bordered mt-4">
      <thead>
        <tr>
          <th>Article</th>
          <th>Acheteur</th>
          <th>Montant proposé</th>
          <th>Date</th>
          <th>Statut</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($offres as $offre): ?>
          <tr>
            <td><?= htmlspecialchars($offre['article_titre']) ?></td>
            <td><?= htmlspecialchars($offre['acheteur_prenom'] . ' ' . $offre['acheteur_nom']) ?></td>
            <td><?= number_format($offre['prix_propose'], 2, ',', ' ') ?> €</td>
            <td><?= $offre['date_proposition'] ?></td>
            <td><?= ucfirst($offre['statut']) ?></td>
            <td>
              <?php if ($offre['statut'] === 'en_attente'): ?>
                <a href="repondre_offre.php?id=<?= $offre['offre_id'] ?>" class="btn btn-sm btn-primary">Répondre</a>
              <?php else: ?>
                <span class="text-muted">Traité</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

</body>
</html>
