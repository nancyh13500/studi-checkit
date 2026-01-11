<?php

/**
 * Script pour vérifier la configuration utilisée par l'application
 * À supprimer après diagnostic
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Vérification de la configuration de l'application</h2>";

// Inclure le fichier pdo.php comme le fait l'application
echo "<h3>Configuration depuis lib/pdo.php :</h3>";
try {
    require_once __DIR__ . "/lib/pdo.php";

    echo "<p style='color: green;'>✓ Fichier lib/pdo.php chargé avec succès</p>";

    // Afficher les variables (sans le mot de passe complet)
    echo "<ul>";
    echo "<li><strong>Hôte :</strong> " . htmlspecialchars($host ?? 'NON DÉFINI') . "</li>";
    echo "<li><strong>Base de données :</strong> " . htmlspecialchars($db ?? 'NON DÉFINI') . "</li>";
    echo "<li><strong>Utilisateur :</strong> " . htmlspecialchars($user ?? 'NON DÉFINI') . "</li>";
    echo "<li><strong>Mot de passe :</strong> " . (isset($pass) && !empty($pass) ? '*** (défini)' : 'VIDE ou NON DÉFINI') . "</li>";
    echo "</ul>";

    // Tester la connexion
    echo "<h3>Test de connexion avec la variable \$pdo :</h3>";
    if (isset($pdo) && $pdo instanceof PDO) {
        echo "<p style='color: green;'>✓ Variable \$pdo existe et est une instance PDO</p>";

        try {
            $stmt = $pdo->query("SELECT VERSION() as version");
            $result = $stmt->fetch();
            echo "<p style='color: green;'>✓ Connexion active - Version MySQL : " . htmlspecialchars($result['version']) . "</p>";

            // Lister les tables
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "<p>Tables trouvées : " . count($tables) . "</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>✗ Erreur lors de l'utilisation de \$pdo : " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Variable \$pdo n'existe pas ou n'est pas une instance PDO</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erreur lors du chargement de lib/pdo.php : " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Fichier :</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Ligne :</strong> " . $e->getLine() . "</p>";
}

// Afficher le contenu du fichier pdo.php (sans le mot de passe complet)
echo "<h3>Contenu du fichier lib/pdo.php :</h3>";
$pdoFile = __DIR__ . "/lib/pdo.php";
if (file_exists($pdoFile)) {
    $content = file_get_contents($pdoFile);
    // Masquer le mot de passe dans l'affichage
    $content = preg_replace('/\$pass\s*=\s*[\'"]([^\'"]+)[\'"];/', '$pass = \'***\';', $content);
    echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
    echo htmlspecialchars($content);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>✗ Le fichier lib/pdo.php n'existe pas à l'emplacement : " . htmlspecialchars($pdoFile) . "</p>";
}

echo "<hr>";
echo "<p><em>N'oubliez pas de supprimer ce fichier après diagnostic pour des raisons de sécurité.</em></p>";
