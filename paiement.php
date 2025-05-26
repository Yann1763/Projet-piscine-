<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user'])) {
  header('Location: compte.php');
  exit;
}
$total = $_POST['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Paiement</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
  <h2>Paiement - Total à régler : <?= htmlspecialchars($total) ?> €</h2>

  <form method="post" action="valider_paiement.php">
    <h4>Adresse de livraison</h4>
    <input class="form-control mb-2" name="nom" placeholder="Nom" required>
    <input class="form-control mb-2" name="prenom" placeholder="Prénom" required>
    <input class="form-control mb-2" name="adresse1" placeholder="Adresse ligne 1" required>
    <input class="form-control mb-2" name="adresse2" placeholder="Adresse ligne 2">
    <input class="form-control mb-2" name="ville" placeholder="Ville" required>
    <input class="form-control mb-2" name="cp" placeholder="Code Postal" required>
    <input class="form-control mb-2" name="pays" placeholder="Pays" required>
    <input class="form-control mb-4" name="tel" placeholder="Téléphone" required>

    <h4>Informations de carte</h4>
    <select class="form-control mb-2" name="type_carte">
      <option>Visa</option>
      <option>MasterCard</option>
      <option>American Express</option>
      <option>PayPal</option>
    </select>
    <input class="form-control mb-2" name="numero" placeholder="Numéro de carte" required>
    <input class="form-control mb-2" name="nom_carte" placeholder="Nom sur la carte" required>
    <input class="form-control mb-2" name="expiration" placeholder="Date d'expiration (MM/AA)" required>
    <input class="form-control mb-4" name="code" placeholder="Code de sécurité" required>

    <input type="hidden" name="total" value="<?= $total ?>">
    <button class="btn btn-success">Valider le paiement</button>
  </form>
</body>
</html>