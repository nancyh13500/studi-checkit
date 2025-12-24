<?php
// Les fonctions pour les listes
// Note: utilisez la variable $pdo définie dans lib/pdo.php

function getListsByUserId(PDO $pdo, int $userId, ?int $categoryId = null): array
{
    if ($categoryId) {
        $query = $pdo->prepare("
            SELECT l.*, c.name as category_name, c.icon as category_icon
            FROM list l
            JOIN category c ON l.category_id = c.id
            WHERE l.user_id = :user_id AND l.category_id = :category_id
        ");
        $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $query->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
    } else {
        $query = $pdo->prepare("
            SELECT l.*, c.name as category_name, c.icon as category_icon
            FROM list l
            JOIN category c ON l.category_id = c.id
            WHERE l.user_id = :user_id
        ");
        $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    }
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function getListItems(PDO $pdo, int $listId): array
{
    $query = $pdo->prepare("SELECT * FROM item WHERE list_id = :list_id ORDER BY id");
    $query->bindValue(':list_id', $listId, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Récupère une liste par son ID
 *
 * @param PDO $pdo Instance de connexion à la base de données
 * @param int $listId L'ID de la liste
 * @return array|false Retourne les données de la liste si trouvée, false sinon
 */
function getListById(PDO $pdo, int $listId): array|false
{
    $query = $pdo->prepare("SELECT * FROM list WHERE id = :id");
    $query->bindValue(':id', $listId, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

/**
 * Crée une nouvelle liste dans la base de données
 *
 * @param PDO $pdo Instance de connexion à la base de données
 * @param string $title Le titre de la liste
 * @param int $userId L'ID de l'utilisateur
 * @param int $categoryId L'ID de la catégorie
 * @return bool|array Retourne les données de la liste créée si succès, false sinon
 */
function createList(PDO $pdo, string $title, int $userId, int $categoryId): bool|array
{
    $query = $pdo->prepare("INSERT INTO list (title, user_id, category_id) VALUES (:title, :user_id, :category_id)");
    $query->bindValue(':title', $title, PDO::PARAM_STR);
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->bindValue(':category_id', $categoryId, PDO::PARAM_INT);

    if ($query->execute()) {
        // Récupérer la liste créée
        $listId = $pdo->lastInsertId();
        return getListById($pdo, $listId);
    }

    return false;
}
