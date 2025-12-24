<?php

// Constantes de configuration
define('TOKENS_FILE', __DIR__ . '/tokens.json');

/**
 * Obtient le domaine du cookie selon l'environnement
 *
 * @return string|null Le domaine du cookie ou null pour localhost
 */
function getCookieDomain(): ?string
{
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
        // Si c'est localhost, ne pas définir de domaine
        return null;
    }

    // Sinon, utiliser le domaine personnalisé
    return '.checkit.local';
}

session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => getCookieDomain(),
    'httponly' => true
]);

session_start();

/**
 * Charge les tokens depuis le fichier JSON
 *
 * @return array Tableau des tokens
 */
function loadTokens(): array
{
    if (!file_exists(TOKENS_FILE)) {
        return [];
    }

    $content = file_get_contents(TOKENS_FILE);
    if ($content === false) {
        return [];
    }

    $tokens = json_decode($content, true);
    return is_array($tokens) ? $tokens : [];
}

/**
 * Sauvegarde les tokens dans le fichier JSON
 *
 * @param array $tokens Tableau des tokens à sauvegarder
 * @return bool True si la sauvegarde réussit, false sinon
 */
function saveTokens(array $tokens): bool
{
    // Nettoyer les tokens expirés avant de sauvegarder
    $tokens = array_filter($tokens, function ($tokenData) {
        return isset($tokenData['expires_at']) && $tokenData['expires_at'] > time();
    });

    $content = json_encode($tokens, JSON_PRETTY_PRINT);
    return file_put_contents(TOKENS_FILE, $content) !== false;
}

/**
 * Génère un token unique et sécurisé
 *
 * @return string Le token généré
 */
function generateToken(): string
{
    return bin2hex(random_bytes(32));
}

/**
 * Crée un token de connexion pour un utilisateur
 *
 * @param array $user Les données de l'utilisateur
 * @param int $duration Durée de validité du token en secondes (par défaut 30 jours)
 * @return string Le token créé
 */
function createRememberToken(array $user, int $duration = 2592000): string
{
    $token = generateToken();
    $tokens = loadTokens();

    $tokens[$token] = [
        'user_id' => $user['id'],
        'user_data' => $user,
        'created_at' => time(),
        'expires_at' => time() + $duration
    ];

    saveTokens($tokens);

    // Définir le cookie avec le token
    setcookie('remember_token', $token, time() + $duration, '/', getCookieDomain(), true, true);

    return $token;
}

/**
 * Vérifie et restaure la session à partir d'un token
 *
 * @return bool True si la session a été restaurée, false sinon
 */
function restoreSessionFromToken(): bool
{
    if (isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        $tokens = loadTokens();

        if (isset($tokens[$token])) {
            $tokenData = $tokens[$token];

            // Vérifier si le token n'est pas expiré
            if (isset($tokenData['expires_at']) && $tokenData['expires_at'] > time()) {
                // Restaurer la session
                $_SESSION['user'] = $tokenData['user_data'];
                return true;
            } else {
                // Token expiré, le supprimer
                deleteToken($token);
            }
        }
    }

    return false;
}

/**
 * Supprime un token
 *
 * @param string $token Le token à supprimer
 * @return bool True si la suppression réussit, false sinon
 */
function deleteToken(string $token): bool
{
    $tokens = loadTokens();

    if (isset($tokens[$token])) {
        unset($tokens[$token]);
        saveTokens($tokens);
    }

    // Supprimer le cookie
    setcookie('remember_token', '', time() - 3600, '/', getCookieDomain(), true, true);

    return true;
}

/**
 * Supprime tous les tokens d'un utilisateur
 *
 * @param int $userId L'ID de l'utilisateur
 * @return bool True si la suppression réussit, false sinon
 */
function deleteUserTokens(int $userId): bool
{
    $tokens = loadTokens();
    $modified = false;

    foreach ($tokens as $token => $tokenData) {
        if (isset($tokenData['user_id']) && $tokenData['user_id'] === $userId) {
            unset($tokens[$token]);
            $modified = true;
        }
    }

    if ($modified) {
        saveTokens($tokens);
    }

    return true;
}

/**
 * Nettoie les tokens expirés
 *
 * @return int Nombre de tokens supprimés
 */
function cleanExpiredTokens(): int
{
    $tokens = loadTokens();
    $count = 0;

    foreach ($tokens as $token => $tokenData) {
        if (isset($tokenData['expires_at']) && $tokenData['expires_at'] <= time()) {
            unset($tokens[$token]);
            $count++;
        }
    }

    if ($count > 0) {
        saveTokens($tokens);
    }

    return $count;
}

/**
 * Vérifie si l'utilisateur est connecté
 *
 * @return bool True si l'utilisateur est connecté, false sinon
 */
function isUserConnected(): bool
{
    // Si la session existe déjà, l'utilisateur est connecté
    if (isset($_SESSION['user'])) {
        return true;
    }

    // Sinon, essayer de restaurer la session depuis le token
    return restoreSessionFromToken();
}

// Nettoyer automatiquement les tokens expirés (avec une probabilité de 1% pour éviter de surcharger)
if (rand(1, 100) === 1) {
    cleanExpiredTokens();
}
