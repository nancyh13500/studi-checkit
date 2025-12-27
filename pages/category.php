<?php
require_once __DIR__ . "/../lib/session.php";
require_once __DIR__ . "/../lib/pdo.php";
require_once __DIR__ . "/../lib/category.php";

// Vérifier que l'utilisateur est connecté
if (!isUserConnected()) {
    header('location: ../login.php');
    exit();
}

$errors = [];
$success = false;
$action = $_GET['action'] ?? 'addCategory';
$categoryToEdit = null;
$editCategoryId = null;

// Récupérer toutes les catégories
$categories = getCategories($pdo);

// Traitement de la modification d'une catégorie
if (isset($_POST['updateCategory'])) {
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $categoryName = trim($_POST['category_name'] ?? '');
    $categoryIcon = trim($_POST['category_icon'] ?? '');

    // Validation du nom
    if (empty($categoryName)) {
        $errors[] = "Le nom de la catégorie est requis";
    } elseif (strlen($categoryName) < 2) {
        $errors[] = "Le nom de la catégorie doit contenir au moins 2 caractères";
    }

    // Validation de l'icône
    if (empty($categoryIcon)) {
        $errors[] = "L'icône de la catégorie est requise";
    }

    // Si aucune erreur, mettre à jour la catégorie
    if (empty($errors) && $categoryId > 0) {
        $updatedCategory = updateCategory($pdo, $categoryId, $categoryName, $categoryIcon);

        if ($updatedCategory) {
            $success = true;
            // Recharger les catégories
            $categories = getCategories($pdo);
            $action = 'addCategory';
            $categoryToEdit = null;
            $editCategoryId = null;
        } else {
            $errors[] = "Une erreur est survenue lors de la modification de la catégorie";
        }
    }
}

// Traitement de l'ajout d'une catégorie
if (isset($_POST['addCategory'])) {
    $categoryName = trim($_POST['category_name'] ?? '');
    $categoryIcon = trim($_POST['category_icon'] ?? '');

    // Validation du nom
    if (empty($categoryName)) {
        $errors[] = "Le nom de la catégorie est requis";
    } elseif (strlen($categoryName) < 2) {
        $errors[] = "Le nom de la catégorie doit contenir au moins 2 caractères";
    }

    // Validation de l'icône
    if (empty($categoryIcon)) {
        $errors[] = "L'icône de la catégorie est requise";
    }

    // Si aucune erreur, créer la catégorie
    if (empty($errors)) {
        $newCategory = createCategory($pdo, $categoryName, $categoryIcon);

        if ($newCategory) {
            $success = true;
            // Recharger les catégories
            $categories = getCategories($pdo);
        } else {
            $errors[] = "Une erreur est survenue lors de la création de la catégorie";
        }
    }
}

// Si on veut éditer une catégorie
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editCategoryId = (int)$_GET['edit'];
    $categoryToEdit = getCategoryById($pdo, $editCategoryId);
    if ($categoryToEdit) {
        $action = 'editCategory';
    }
}

require_once __DIR__ . "/../templates/header.php";
?>

<div class="container col-xxl-8 px-4 py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= $action === 'editCategory' ? 'Modifier une catégorie' : 'Gérer les catégories' ?></h1>
        <a href="mes-listes.php" class="btn btn-outline-primary">Retour aux listes</a>
    </div>

    <?php if ($success) { ?>
        <div class="alert alert-success" role="alert">
            La catégorie a été <?= $action === 'editCategory' ? 'modifiée' : 'créée' ?> avec succès !
        </div>
    <?php } ?>

    <?php
    foreach ($errors as $error) { ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php }
    ?>

    <!-- Formulaire d'ajout/modification -->
    <div class="card mb-4">
        <div class="card-header">
            <h3><?= $action === 'editCategory' ? 'Modifier la catégorie' : 'Ajouter une nouvelle catégorie' ?></h3>
        </div>
        <div class="card-body">
            <form action="" method="post">
                <?php if ($action === 'editCategory' && $categoryToEdit) { ?>
                    <input type="hidden" name="category_id" value="<?= $categoryToEdit['id'] ?>">
                <?php } ?>

                <div class="mb-3">
                    <label for="category_name" class="form-label">Nom de la catégorie</label>
                    <input type="text" name="category_name" id="category_name" class="form-control"
                        value="<?= htmlspecialchars($categoryToEdit ? $categoryToEdit['name'] : ($_POST['category_name'] ?? '')); ?>"
                        placeholder="Ex: Maison, Sport, etc." required>
                </div>

                <div class="mb-3">
                    <label for="category_icon" class="form-label">Icône de la catégorie</label>
                    <input type="text" name="category_icon" id="category_icon" class="form-control"
                        value="<?= htmlspecialchars($categoryToEdit ? $categoryToEdit['icon'] : ($_POST['category_icon'] ?? '')); ?>"
                        placeholder="Ex: Déco, Tennis, etc." required>
                </div>

                <div class="d-flex py-3 gap-2">
                    <?php if ($action === 'editCategory') { ?>
                        <input type="submit" name="updateCategory" value="Modifier la catégorie" class="btn btn-primary">
                        <a href="category.php" class="btn btn-secondary">Annuler</a>
                    <?php } else { ?>
                        <input type="submit" name="addCategory" value="Créer la catégorie" class="btn btn-primary">
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des catégories existantes -->
    <div class="card">
        <div class="card-header">
            <h3>Liste des catégories</h3>
        </div>
        <div class="card-body">
            <?php if (empty($categories)) { ?>
                <p class="text-muted">Aucune catégorie n'a été créée pour le moment.</p>
            <?php } else { ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Icône</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($category['id']) ?></td>
                                    <td><?= htmlspecialchars($category['name']) ?></td>
                                    <td>
                                        <?php if (!empty($category['icon'])) { ?>
                                            <i class="bi <?= htmlspecialchars($category['icon']) ?>"></i>
                                            <span class="ms-2"><?= htmlspecialchars($category['icon']) ?></span>
                                        <?php } else { ?>
                                            <span class="text-muted">Aucune</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <a href="category.php?edit=<?= $category['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Modifier
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../templates/footer.php" ?>