<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'admin') {
    header("Location: compte.php");
    exit;
}

if (isset($_POST['id'])) {
    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->execute([$_POST['id']]);
}

header("Location: gestion_articles.php");
exit;
