<?php

/**
 * Hash un mot de passe avec l'algorithme bcrypt
 *
 * @param string $password Le mot de passe en texte brut
 * @return string Le mot de passe hashé
 */
function hashPassword(string $password): string
{
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Vérifie les identifiants de connexion d'un utilisateur
 *
 * @param PDO $pdo Instance de connexion à la base de données
 * @param string $email L'email de l'utilisateur
 * @param string $password Le mot de passe en texte brut
 * @return bool|array Retourne les données de l'utilisateur si la connexion réussit, false sinon
 */
function verifyUserLoginPassword(PDO $pdo, string $email, string $password): bool|array
{
    $query = $pdo->prepare("SELECT * FROM user WHERE email = :email");
    $query->bindValue(':email', $email, PDO::PARAM_STR);
    $query->execute();
    //fetch() nous permet de récupérer une seule ligne
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Vérification réussie
        return $user;
    }

    // email ou mdp incorrect: on retourne false
    return false;
}

/**
 * Vérifie si un email existe déjà dans la base de données
 *
 * @param PDO $pdo Instance de connexion à la base de données
 * @param string $email L'email à vérifier
 * @return bool Retourne true si l'email existe, false sinon
 */
function emailExists(PDO $pdo, string $email): bool
{
    $query = $pdo->prepare("SELECT id FROM user WHERE email = :email");
    $query->bindValue(':email', $email, PDO::PARAM_STR);
    $query->execute();
    return $query->fetch() !== false;
}

/**
 * Crée un nouvel utilisateur dans la base de données
 *
 * @param PDO $pdo Instance de connexion à la base de données
 * @param string $nickname Le pseudonyme de l'utilisateur
 * @param string $email L'email de l'utilisateur
 * @param string $password Le mot de passe en texte brut
 * @return bool|array Retourne les données de l'utilisateur créé si succès, false sinon
 */
function createUser(PDO $pdo, string $nickname, string $email, string $password): bool|array
{
    // Vérifier si l'email existe déjà
    if (emailExists($pdo, $email)) {
        return false;
    }

    // Hasher le mot de passe
    $hashedPassword = hashPassword($password);

    // Insérer l'utilisateur dans la base de données
    $query = $pdo->prepare("INSERT INTO user (nickname, email, password) VALUES (:nickname, :email, :password)");
    $query->bindValue(':nickname', $nickname, PDO::PARAM_STR);
    $query->bindValue(':email', $email, PDO::PARAM_STR);
    $query->bindValue(':password', $hashedPassword, PDO::PARAM_STR);

    if ($query->execute()) {
        // Récupérer l'utilisateur créé
        $userId = $pdo->lastInsertId();
        $query = $pdo->prepare("SELECT * FROM user WHERE id = :id");
        $query->bindValue(':id', $userId, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    return false;
}

/**
 * Génère un token de réinitialisation de mot de passe
 *
 * @return string Le token généré
 */
function generateResetToken(): string
{
    return bin2hex(random_bytes(32));
}

/**
 * Crée un token de réinitialisation pour un utilisateur
 *
 * @param PDO $pdo Instance de connexion à la base de données
 * @param string $email L'email de l'utilisateur
 * @return string|false Retourne le token si succès, false sinon
 */
function createPasswordResetToken(PDO $pdo, string $email): string|false
{
    // Vérifier si l'email existe
    if (!emailExists($pdo, $email)) {
        return false;
    }

    // Générer un token
    $token = generateResetToken();
    
    // Définir l'expiration (1 heure)
    $expires = date('Y-m-d H:i:s', time() + 3600);

    // Mettre à jour l'utilisateur avec le token
    $query = $pdo->prepare("UPDATE user SET reset_token = :token, reset_token_expires = :expires WHERE email = :email");
    $query->bindValue(':token', $token, PDO::PARAM_STR);
    $query->bindValue(':expires', $expires, PDO::PARAM_STR);
    $query->bindValue(':email', $email, PDO::PARAM_STR);
    
    if ($query->execute()) {
        return $token;
    }

    return false;
}

/**
 * Vérifie si un token de réinitialisation est valide
 *
 * @param PDO $pdo Instance de connexion à la base de données
 * @param string $token Le token à vérifier
 * @return bool|array Retourne les données de l'utilisateur si le token est valide, false sinon
 */
function verifyResetToken(PDO $pdo, string $token): bool|array
{
    $query = $pdo->prepare("SELECT * FROM user WHERE reset_token = :token AND reset_token_expires > NOW()");
    $query->bindValue(':token', $token, PDO::PARAM_STR);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        return $user;
    }

    return false;
}

/**
 * Réinitialise le mot de passe d'un utilisateur
 *
 * @param PDO $pdo Instance de connexion à la base de données
 * @param string $token Le token de réinitialisation
 * @param string $newPassword Le nouveau mot de passe
 * @return bool Retourne true si succès, false sinon
 */
function resetPassword(PDO $pdo, string $token, string $newPassword): bool
{
    // Vérifier le token
    $user = verifyResetToken($pdo, $token);
    if (!$user) {
        return false;
    }

    // Hasher le nouveau mot de passe
    $hashedPassword = hashPassword($newPassword);

    // Mettre à jour le mot de passe et supprimer le token
    $query = $pdo->prepare("UPDATE user SET password = :password, reset_token = NULL, reset_token_expires = NULL WHERE id = :id");
    $query->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
    $query->bindValue(':id', $user['id'], PDO::PARAM_INT);

    return $query->execute();
}

/**
 * Envoie un email de réinitialisation de mot de passe
 *
 * @param string $email L'email du destinataire
 * @param string $token Le token de réinitialisation
 * @return bool Retourne true si l'email est envoyé, false sinon
 */
function sendPasswordResetEmail(string $email, string $token): bool
{
    // Construire l'URL de réinitialisation
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $resetUrl = $protocol . '://' . $host . '/reset-password.php?token=' . urlencode($token);

    // Sujet de l'email
    $subject = "Réinitialisation de votre mot de passe - CheckIt";

    // Corps de l'email
    $message = "Bonjour,\n\n";
    $message .= "Vous avez demandé à réinitialiser votre mot de passe.\n\n";
    $message .= "Cliquez sur le lien suivant pour réinitialiser votre mot de passe :\n";
    $message .= $resetUrl . "\n\n";
    $message .= "Ce lien est valide pendant 1 heure.\n\n";
    $message .= "Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.\n\n";
    $message .= "Cordialement,\nL'équipe CheckIt";

    // En-têtes de l'email
    $headers = "From: noreply@checkit.local\r\n";
    $headers .= "Reply-To: noreply@checkit.local\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Envoyer l'email
    return mail($email, $subject, $message, $headers);
}
