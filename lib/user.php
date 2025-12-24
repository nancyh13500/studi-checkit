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
