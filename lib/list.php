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

/**
 * Met à jour une liste existante
 *
 * @param PDO $pdo Instance de connexion à la base de données
 * @param int $listId L'ID de la liste à modifier
 * @param string $title Le nouveau titre de la liste
 * @param int $categoryId L'ID de la nouvelle catégorie
 * @return bool Retourne true si la mise à jour a réussi, false sinon
 */
function updateList(PDO $pdo, int $listId, string $title, int $categoryId): bool
{
    $query = $pdo->prepare("UPDATE list SET title = :title, category_id = :category_id WHERE id = :id");
    $query->bindValue(':title', $title, PDO::PARAM_STR);
    $query->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
    $query->bindValue(':id', $listId, PDO::PARAM_INT);
    return $query->execute();
}

/**
 * Supprime une liste et tous ses items associés
 *
 * @param PDO $pdo Instance de connexion à la base de données
 * @param int $listId L'ID de la liste à supprimer
 * @return bool Retourne true si la suppression a réussi, false sinon
 */
function deleteList(PDO $pdo, int $listId): bool
{
    try {
        $pdo->beginTransaction();

        // Supprimer d'abord tous les items de la liste
        $deleteItems = $pdo->prepare("DELETE FROM item WHERE list_id = :list_id");
        $deleteItems->bindValue(':list_id', $listId, PDO::PARAM_INT);
        $deleteItems->execute();

        // Ensuite supprimer la liste
        $deleteList = $pdo->prepare("DELETE FROM list WHERE id = :id");
        $deleteList->bindValue(':id', $listId, PDO::PARAM_INT);
        $deleteList->execute();

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

/**
 * Crée un nouvel item dans une liste
 *
 * @param PDO $pdo Instance de connexion à la base de données
 * @param string $name Le nom de l'item
 * @param int $listId L'ID de la liste
 * @return bool|array Retourne les données de l'item créé si succès, false sinon
 */
function createItem(PDO $pdo, string $name, int $listId): bool|array
{
    $query = $pdo->prepare("INSERT INTO item (name, status, list_id) VALUES (:name, 0, :list_id)");
    $query->bindValue(':name', $name, PDO::PARAM_STR);
    $query->bindValue(':list_id', $listId, PDO::PARAM_INT);

    if ($query->execute()) {
        $itemId = $pdo->lastInsertId();
        $query = $pdo->prepare("SELECT * FROM item WHERE id = :id");
        $query->bindValue(':id', $itemId, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    return false;
}

/**
 * Met à jour un item
 *
 * @param PDO $pdo Instance de connexion à la base de données
 * @param int $itemId L'ID de l'item
 * @param string $name Le nouveau nom de l'item
 * @return bool Retourne true si la mise à jour a réussi, false sinon
 */
function updateItem(PDO $pdo, int $itemId, string $name): bool
{
    $query = $pdo->prepare("UPDATE item SET name = :name WHERE id = :id");
    $query->bindValue(':name', $name, PDO::PARAM_STR);
    $query->bindValue(':id', $itemId, PDO::PARAM_INT);
    return $query->execute();
}

/**
 * Supprime un item
 *
 * @param PDO $pdo Instance de connexion à la base de données
 * @param int $itemId L'ID de l'item à supprimer
 * @return bool Retourne true si la suppression a réussi, false sinon
 */
function deleteItem(PDO $pdo, int $itemId): bool
{
    $query = $pdo->prepare("DELETE FROM item WHERE id = :id");
    $query->bindValue(':id', $itemId, PDO::PARAM_INT);
    return $query->execute();
}

/**
 * Met à jour le statut d'un item (coché/décoché)
 *
 * @param PDO $pdo Instance de connexion à la base de données
 * @param int $itemId L'ID de l'item
 * @param bool $status Le nouveau statut (true = coché, false = décoché)
 * @return bool Retourne true si la mise à jour a réussi, false sinon
 */
function updateItemStatus(PDO $pdo, int $itemId, bool $status): bool
{
    $query = $pdo->prepare("UPDATE item SET status = :status WHERE id = :id");
    $query->bindValue(':status', $status ? 1 : 0, PDO::PARAM_INT);
    $query->bindValue(':id', $itemId, PDO::PARAM_INT);
    return $query->execute();
}
