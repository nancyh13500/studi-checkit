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
$action = $_GET['action'] ?? '';

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
            // Rediriger vers mes-listes.php après création
            header('location: mes-listes.php');
            exit();
        } else {
            $errors[] = "Une erreur est survenue lors de la création de la catégorie";
        }
    }
}

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

// Traitement du formulaire d'ajout/modification
if (isset($_POST['addList'])) {
    $title = trim($_POST['title'] ?? '');
    $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $listId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

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

    // Si aucune erreur, créer ou modifier la liste
    if (empty($errors)) {
        // Si un ID est présent, on est en mode modification
        if ($listId > 0) {
            // Vérifier que la liste existe et appartient à l'utilisateur
            $existingList = getListById($pdo, $listId);
            if ($existingList && $existingList['user_id'] == $_SESSION['user']['id']) {
                if (updateList($pdo, $listId, $title, $categoryId)) {
                    $success = true;
                    // Recharger la liste mise à jour
                    $list = getListById($pdo, $listId);
                } else {
                    $errors[] = "Une erreur est survenue lors de la modification de la liste";
                }
            } else {
                $errors[] = "Vous n'avez pas le droit de modifier cette liste";
            }
        } else {
            // Mode création
            $newList = createList($pdo, $title, $_SESSION['user']['id'], $categoryId);

            if ($newList) {
                // Rediriger vers la page d'édition de la liste pour permettre d'ajouter des items
                header('location: ajout-modification-liste.php?id=' . $newList['id']);
                exit();
            } else {
                $errors[] = "Une erreur est survenue lors de la création de la liste";
            }
        }
    }
}

// Traitement de l'ajout d'un item
if (isset($_POST['addItem']) && isset($_GET['id'])) {
    $listId = (int)$_GET['id'];
    $itemName = trim($_POST['item_name'] ?? '');

    if (empty($itemName)) {
        $errors[] = "Le nom de l'item est requis";
    } elseif (strlen($itemName) < 2) {
        $errors[] = "Le nom de l'item doit contenir au moins 2 caractères";
    } else {
        if (createItem($pdo, $itemName, $listId)) {
            header('location: ajout-modification-liste.php?id=' . $listId);
            exit();
        } else {
            $errors[] = "Une erreur est survenue lors de l'ajout de l'item";
        }
    }
}

// Traitement de la modification d'un item
if (isset($_POST['updateItem']) && isset($_GET['id'])) {
    $listId = (int)$_GET['id'];
    $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
    $itemName = trim($_POST['item_name'] ?? '');

    if (empty($itemName)) {
        $errors[] = "Le nom de l'item est requis";
    } elseif ($itemId <= 0) {
        $errors[] = "ID d'item invalide";
    } else {
        if (updateItem($pdo, $itemId, $itemName)) {
            header('location: ajout-modification-liste.php?id=' . $listId);
            exit();
        } else {
            $errors[] = "Une erreur est survenue lors de la modification de l'item";
        }
    }
}

// Traitement de la suppression d'un item
if (isset($_POST['deleteItem']) && isset($_GET['id'])) {
    $listId = (int)$_GET['id'];
    $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;

    if ($itemId <= 0) {
        $errors[] = "ID d'item invalide";
    } else {
        if (deleteItem($pdo, $itemId)) {
            header('location: ajout-modification-liste.php?id=' . $listId);
            exit();
        } else {
            $errors[] = "Une erreur est survenue lors de la suppression de l'item";
        }
    }
}

// Traitement du changement de statut d'un item
if (isset($_POST['toggleStatus']) && isset($_GET['id'])) {
    $listId = (int)$_GET['id'];
    $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;

    if ($itemId > 0) {
        // Récupérer l'item pour connaître son statut actuel
        $items = getListItems($pdo, $listId);
        $currentItem = null;
        foreach ($items as $it) {
            if ($it['id'] == $itemId) {
                $currentItem = $it;
                break;
            }
        }

        if ($currentItem) {
            $newStatus = !$currentItem['status'];
            if (updateItemStatus($pdo, $itemId, $newStatus)) {
                header('location: ajout-modification-liste.php?id=' . $listId);
                exit();
            }
        }
    }
}

// Récupérer toutes les catégories
$categories = getCategories($pdo);

// Récupérer les items de la liste si on est en mode modification
$items = [];
if ($list) {
    $items = getListItems($pdo, $list['id']);
}

require_once __DIR__ . "/../templates/header.php";
?>

<div class="container col-xxl-8 px-4 py-5">
    <?php if ($action === 'addCategory') { ?>
        <h1>Ajouter une catégorie</h1>

        <?php if ($success) { ?>
            <div class="alert alert-success" role="alert">
                La catégorie a été créée avec succès !
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
                <label for="category_name" class="form-label">Nom de la catégorie</label>
                <input type="text" name="category_name" id="category_name" class="form-control"
                    value="<?= htmlspecialchars($_POST['category_name'] ?? ''); ?>"
                    placeholder="Ex: Maison, Sport, etc." required>
            </div>
            <div class="mb-3">
                <label for="category_icon" class="form-label">Icône (classe Bootstrap Icons)</label>
                <input type="text" name="category_icon" id="category_icon" class="form-control"
                    value="<?= htmlspecialchars($_POST['category_icon'] ?? ''); ?>"
                    placeholder="Ex: bi-house, bi-briefcase, bi-airplane, etc." required>
                <small class="form-text text-muted">Utilisez une classe d'icône Bootstrap Icons (ex: bi-house)</small>
            </div>

            <div class="d-flex py-3 gap-2">
                <input type="submit" name="addCategory" value="Créer la catégorie" class="btn btn-primary">
                <a href="mes-listes.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    <?php } else { ?>
        <h1><?= $list ? 'Modifier la liste' : 'Ajouter une liste' ?></h1>

        <?php if ($success) { ?>
            <div class="alert alert-success" role="alert">
                <?= $list ? 'La liste a été modifiée avec succès !' : 'La liste a été créée avec succès !' ?>
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

            <div class="d-flex py-3 gap-2">
                <input type="submit" name="addList" value="<?= $list ? 'Modifier' : 'Créer la liste' ?>" class="btn btn-primary">
                <a href="mes-listes.php" class="btn btn-secondary">Annuler</a>
                <?php if ($list) { ?>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        Supprimer
                    </button>
                <?php } ?>
            </div>
        </form>
    <?php } ?>

    <?php if ($list && $action !== 'addCategory') { ?>
        <!-- Accordéon Bootstrap pour gérer les items -->
        <div class="accordion mt-4" id="itemsAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button <?= empty($items) ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseItems" aria-expanded="<?= empty($items) ? 'true' : 'false' ?>" aria-controls="collapseItems">
                        <i class="bi bi-list-check me-2"></i>
                        Gérer les items de la liste (<?= count($items) ?>)
                    </button>
                </h2>
                <div id="collapseItems" class="accordion-collapse collapse <?= empty($items) ? 'show' : '' ?>" data-bs-parent="#itemsAccordion">
                    <div class="accordion-body">
                        <!-- Formulaire d'ajout d'un nouvel item -->
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-plus-circle me-2"></i>Ajouter un nouvel item
                            </div>
                            <div class="card-body">
                                <form action="" method="post" class="d-flex gap-2">
                                    <input type="hidden" name="list_id" value="<?= $list['id'] ?>">
                                    <input type="text" name="item_name" id="item_name" class="form-control"
                                        placeholder="Nom de l'item" required>
                                    <button type="submit" name="addItem" class="btn btn-primary">
                                        <i class="ajouter"></i> Ajouter
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Liste des items existants -->
                        <?php if (!empty($items)) { ?>
                            <div class="list-group">
                                <?php foreach ($items as $item) { ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center flex-grow-1">
                                            <form action="" method="post" class="me-2">
                                                <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                                <button type="submit" name="toggleStatus" class="btn btn-sm btn-link p-0 me-2" title="<?= $item['status'] ? 'Marquer comme non fait' : 'Marquer comme fait' ?>">
                                                    <i class="bi bi-check-circle<?= $item['status'] ? '-fill text-success' : '' ?>" style="font-size: 1.2rem;"></i>
                                                </button>
                                            </form>
                                            <span class="<?= $item['status'] ? 'text-decoration-line-through text-muted' : '' ?>">
                                                <?= htmlspecialchars($item['name']) ?>
                                            </span>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editItemModal<?= $item['id'] ?>">
                                                <i class="bi bi-pencil"></i> Modifier
                                            </button>
                                            <form action="" method="post" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet item ?');">
                                                <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                                <button type="submit" name="deleteItem" class="btn btn-outline-danger">
                                                    <i class="bi bi-trash"></i> Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Modal de modification d'item -->
                                    <div class="modal fade" id="editItemModal<?= $item['id'] ?>" tabindex="-1" aria-labelledby="editItemModalLabel<?= $item['id'] ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editItemModalLabel<?= $item['id'] ?>">Modifier l'item</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="" method="post">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                                        <div class="mb-3">
                                                            <label for="edit_item_name<?= $item['id'] ?>" class="form-label">Nom de l'item</label>
                                                            <input type="text" name="item_name" id="edit_item_name<?= $item['id'] ?>"
                                                                class="form-control" value="<?= htmlspecialchars($item['name']) ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <button type="submit" name="updateItem" class="btn btn-primary">Enregistrer</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>Aucun item dans cette liste. Ajoutez-en un ci-dessus.
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

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