<?php
require 'db.php';

$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$email = $_POST['email'];
$mdp = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
$type = $_POST['type_utilisateur'];

try {
    $sql = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, type_utilisateur)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nom, $prenom, $email, $mdp, $type]);
    header("Location: compte.php");
    exit;
} catch (PDOException $e) {
    echo "Erreur lors de l'inscription : " . $e->getMessage();
}
