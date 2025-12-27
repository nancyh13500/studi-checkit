<?php

function getCategories(PDO $pdo): array
{
    $query = $pdo->prepare("SELECT * FROM category");
    $query->execute();

    return $query->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Crée une nouvelle catégorie dans la base de données
 *
 * @param PDO $pdo Instance de connexion à la base de données
 * @param string $name Le nom de la catégorie
 * @param string $icon L'icône de la catégorie
 * @return bool|array Retourne les données de la catégorie créée si succès, false sinon
 */
function createCategory(PDO $pdo, string $name, string $icon): bool|array
{
    $query = $pdo->prepare("INSERT INTO category (name, icon) VALUES (:name, :icon)");
    $query->bindValue(':name', $name, PDO::PARAM_STR);
    $query->bindValue(':icon', $icon, PDO::PARAM_STR);

    if ($query->execute()) {
        // Récupérer la catégorie créée
        $categoryId = $pdo->lastInsertId();
        $query = $pdo->prepare("SELECT * FROM category WHERE id = :id");
        $query->bindValue(':id', $categoryId, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    return false;
}
