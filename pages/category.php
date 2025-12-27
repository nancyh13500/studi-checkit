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

require_once __DIR__ . "/../templates/header.php";
?>

<div class="container col-xxl-8 px-4 py-5">
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

        <div class="d-flex py-3 gap-2">
            <input type="submit" name="addCategory" value="Créer la catégorie" class="btn btn-primary">
            <a href="mes-listes.php" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . "/../templates/footer.php" ?>