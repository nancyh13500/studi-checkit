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

/**
 * Met à jour le nom et l'icône d'une catégorie dans la base de données
 *
 * @param PDO $pdo Instance de connexion à la base de données
 * @param int $id L'identifiant de la catégorie
 * @param string $name Le nouveau nom de la catégorie
 * @param string $icon Le nouveau nom de l'icône de la catégorie
 * @return bool|array Retourne les données de la catégorie mise à jour si succès, false sinon
 */
function updateCategory(PDO $pdo, int $id, string $name, string $icon): bool|array
{
    $query = $pdo->prepare("UPDATE category SET name = :name, icon = :icon WHERE id = :id");
    $query->bindValue(':name', $name, PDO::PARAM_STR);
    $query->bindValue(':icon', $icon, PDO::PARAM_STR);
    $query->bindValue(':id', $id, PDO::PARAM_INT);

    if ($query->execute()) {
        // Récupérer la catégorie mise à jour
        $query = $pdo->prepare("SELECT * FROM category WHERE id = :id");
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    return false;
}

/**
 * Récupère une catégorie par son identifiant
 *
 * @param PDO $pdo Instance de connexion à la base de données
 * @param int $id L'identifiant de la catégorie
 * @return array|false Retourne les données de la catégorie si trouvée, false sinon
 */
function getCategoryById(PDO $pdo, int $id): array|false
{
    $query = $pdo->prepare("SELECT * FROM category WHERE id = :id");
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();

    return $query->fetch(PDO::FETCH_ASSOC);
}
