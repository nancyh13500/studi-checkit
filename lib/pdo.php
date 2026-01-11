<?php
// Détection automatique de l'environnement (Docker local ou production o2switch)
$isDocker = false;

// Vérifier si on est dans Docker (plusieurs méthodes)
if (file_exists('/.dockerenv')) {
    // Fichier présent dans les conteneurs Docker
    $isDocker = true;
} elseif (gethostname() === 'studi-checkit-web' || strpos(gethostname(), 'studi-checkit') !== false) {
    // Hostname du conteneur Docker
    $isDocker = true;
} elseif (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost:8081') !== false) {
    // Port Docker par défaut
    $isDocker = true;
}

if ($isDocker) {
    // Configuration Docker (développement local)
    $host = 'db';
    $db   = 'studi_checkit';
    $user = 'studi';
    $pass = 'studi';
} else {
    // Configuration o2switch (production)
    // IMPORTANT: Sur o2switch, les noms de BDD et utilisateurs sont préfixés par l'identifiant cPanel
    $host = 'localhost';
    $db   = 'dayo3937_checkit_db';  // Préfixe: identifiant cPanel + nom de la base
    $user = 'dayo3937_checkit_user'; // Préfixe: identifiant cPanel + nom de l'utilisateur
    $pass = 'Baptiste13500.';
}

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_TIMEOUT => 10  // Augmenté à 10 secondes
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    $env = $isDocker ? 'Docker (développement)' : 'o2switch (production)';
    die('Erreur de connexion à la base de données [' . $env . '] : ' . $e->getMessage() .
        '<br>Configuration utilisée : ' . htmlspecialchars("$host / $db / $user") .
        '<br>Vérifiez votre configuration de base de données.');
}
