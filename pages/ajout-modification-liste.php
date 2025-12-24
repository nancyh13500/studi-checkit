<?php
require_once __DIR__ . "/../lib/session.php";
require_once __DIR__ . "/../lib/pdo.php";
require_once __DIR__ . "/../lib/list.php";
require_once __DIR__ . "/../lib/category.php";

// Vérifier que l'utilisateur est connecté
if (!isUserConnected()) {
    header('location: ../login.php');
    exit();
}

$errors = [];
$success = false;
$list = null;

// Si un ID est fourni, récupérer la liste pour modification
if (isset($_GET['id'])) {
    $listId = (int)$_GET['id'];
    $list = getListById($pdo, $listId);

    // Vérifier que la liste existe et appartient à l'utilisateur connecté
    if (!$list || $list['user_id'] != $_SESSION['user']['id']) {
        header('location: mes-listes.php');
        exit();
    }
}

// Traitement de la suppression de liste
if (isset($_POST['deleteList']) && isset($_GET['id'])) {
    $listId = (int)$_GET['id'];
    $list = getListById($pdo, $listId);

    // Vérifier que la liste existe et appartient à l'utilisateur connecté
    if ($list && $list['user_id'] == $_SESSION['user']['id']) {
        if (deleteList($pdo, $listId)) {
            header('location: mes-listes.php');
            exit();
        } else {
            $errors[] = "Une erreur est survenue lors de la suppression de la liste";
        }
    } else {
        header('location: mes-listes.php');
        exit();
    }
}

// Traitement du formulaire d'ajout
if (isset($_POST['addList'])) {
    $title = trim($_POST['title'] ?? '');
    $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;

    // Validation du titre
    if (empty($title)) {
        $errors[] = "Le titre est requis";
    } elseif (strlen($title) < 3) {
        $errors[] = "Le titre doit contenir au moins 3 caractères";
    }

    // Validation de la catégorie
    if ($categoryId <= 0) {
        $errors[] = "Veuillez sélectionner une catégorie";
    }

    // Si aucune erreur, créer la liste
    if (empty($errors)) {
        $newList = createList($pdo, $title, $_SESSION['user']['id'], $categoryId);

        if ($newList) {
            $success = true;
            // Rediriger vers la page de détail de la liste ou mes-listes.php
            header('location: mes-listes.php');
            exit();
        } else {
            $errors[] = "Une erreur est survenue lors de la création de la liste";
        }
    }
}

// Récupérer toutes les catégories
$categories = getCategories($pdo);

require_once __DIR__ . "/../templates/header.php";
?>

<div class="container col-xxl-8 px-4 py-5">
    <h1><?= $list ? 'Modifier la liste' : 'Ajouter une liste' ?></h1>

    <?php if ($success) { ?>
        <div class="alert alert-success" role="alert">
            La liste a été créée avec succès !
        </div>
    <?php } ?>

    <?php
    foreach ($errors as $error) { ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php }
    ?>

    <form action="" method="post">
        <div class="mb-3">
            <label for="title" class="form-label">Titre de la liste</label>
            <input type="text" name="title" id="title" class="form-control"
                value="<?= htmlspecialchars($list ? $list['title'] : ($_POST['title'] ?? '')); ?>"
                required>
        </div>
        <div class="mb-3">
            <label for="category_id" class="form-label">Catégorie</label>
            <select name="category_id" id="category_id" class="form-select" required>
                <option value="">Sélectionnez une catégorie</option>
                <?php foreach ($categories as $category) { ?>
                    <option value="<?= $category['id'] ?>"
                        <?= ($list && (int)$list['category_id'] === (int)$category['id']) ||
                            (isset($_POST['category_id']) && (int)$_POST['category_id'] === (int)$category['id'])
                            ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($category['name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="d-flex gap-2">
            <input type="submit" name="addList" value="<?= $list ? 'Modifier' : 'Créer la liste' ?>" class="btn btn-primary">
            <a href="mes-listes.php" class="btn btn-secondary">Annuler</a>
            <?php if ($list) { ?>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    Supprimer
                </button>
            <?php } ?>
        </div>
    </form>

    <?php if ($list) { ?>
        <!-- Modal de confirmation de suppression -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Êtes-vous sûr de vouloir supprimer la liste "<?= htmlspecialchars($list['title']) ?>" ? Cette action est irréversible et supprimera également tous les éléments de la liste.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <form action="" method="post" style="display: inline;">
                            <input type="submit" name="deleteList" value="Supprimer" class="btn btn-danger">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<?php require_once __DIR__ . "/../templates/footer.php" ?>

