<?php
$host = "localhost";   // serveur
$dbname = "gestionfrais";      // nom de la base
$user = "root";        // utilisateur (par défaut)
$password = "";        // mot de passe (vide sur Wamp)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);

    // mode erreur (important)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>