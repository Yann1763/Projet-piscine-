<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'vendeur') {
    die("Accès refusé");
}

$offre_id = $_GET['id'] ?? null;
if (!$offre_id) die("Offre non spécifiée.");

// Charger l'offre + article + vendeur
$stmt = $pdo->prepare("
  SELECT o.*, u.prenom, u.nom, a.titre, a.vendeur_id
  FROM offres o
  JOIN utilisateurs u ON o.acheteur_id = u.id
  JOIN articles a ON o.article_id = a.id
  WHERE o.id = ?
");
$stmt->execute([$offre_id]);
$offre = $stmt->fetch();

if (!$offre || $offre['vendeur_id'] != $_SESSION['user']['id']) {
    die("Accès interdit à cette offre.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'accepter') {
        $pdo->prepare("UPDATE offres SET statut = 'acceptee' WHERE id = ?")->execute([$offre_id]);
        $msg = "Votre offre pour l'article « {$offre['titre']} » a été acceptée.";
    } elseif ($action === 'refuser') {
        $pdo->prepare("UPDATE offres SET statut = 'refusee' WHERE id = ?")->execute([$offre_id]);
        $msg = "Votre offre pour l'article « {$offre['titre']} » a été refusée.";
    } elseif ($action === 'contre') {
        $nouveau = $_POST['contre_offre'] ?? 0;
        $pdo->prepare("UPDATE offres SET statut = 'refusee' WHERE id = ?")->execute([$offre_id]);
        $pdo->prepare("INSERT INTO offres (acheteur_id, article_id, prix_propose) VALUES (?, ?, ?)")
            ->execute([$offre['acheteur_id'], $offre['article_id'], $nouveau]);
        $msg = "Contre-offre reçue pour l'article « {$offre['titre']} » à $nouveau €.";
    }

    $pdo->prepare("INSERT INTO notifications (utilisateur_id, message) VALUES (?, ?)")
        ->execute([$offre['acheteur_id'], $msg]);

    $_SESSION['notif'] = "Réponse à l'offre envoyée.";
    header("Location: offres_reçues.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Répondre à une offre</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h2>Offre reçue</h2>
  <p><strong>Acheteur :</strong> <?= htmlspecialchars($offre['prenom']) ?> <?= htmlspecialchars($offre['nom']) ?></p>
  <p><strong>Article :</strong> <?= htmlspecialchars($offre['titre']) ?></p>
  <p><strong>Montant proposé :</strong> <?= $offre['prix_propose'] ?> €</p>

  <form method="post" class="mt-4">
    <button name="action" value="accepter" class="btn btn-success me-2">Accepter</button>
    <button name="action" value="refuser" class="btn btn-danger me-2">Refuser</button>
    <div class="mt-3">
      <label>Proposer une contre-offre :</label>
      <input type="number" step="0.01" name="contre_offre" class="form-control mb-2" placeholder="Montant (€)" required>
      <button name="action" value="contre" class="btn btn-warning">Envoyer la contre-offre</button>
    </div>
  </form>
</body>
</html>
