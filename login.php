<?php
session_start();
require 'db.php';

$email = $_POST['email'];
$mdp = $_POST['mot_de_passe'];

$sql = "SELECT * FROM utilisateurs WHERE email = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($mdp, $user['mot_de_passe'])) {
    $_SESSION['user'] = [
        'id' => $user['id'],
        'nom' => $user['nom'],
        'prenom' => $user['prenom'],
        'type' => $user['type_utilisateur']
    ];
    header("Location: accueil.php");
    exit;
} else {
    echo "Email ou mot de passe incorrect.";
}
