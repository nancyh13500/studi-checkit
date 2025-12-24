<?php
require_once __DIR__ . "/../lib/session.php";
require_once __DIR__ . "/../lib/pdo.php";
require_once __DIR__ . "/../lib/user.php";

$errors = [];

if (isset($_POST['registerUser'])) {
    // Récupération et validation des données
    $nickname = trim($_POST['nickname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    // Validation du pseudonyme
    if (empty($nickname)) {
        $errors[] = "Le pseudonyme est requis";
    } elseif (strlen($nickname) < 3) {
        $errors[] = "Le pseudonyme doit contenir au moins 3 caractères";
    }

    // Validation de l'email
    if (empty($email)) {
        $errors[] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    }

    // Validation du mot de passe
    if (empty($password)) {
        $errors[] = "Le mot de passe est requis";
    } elseif (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
    }

    // Vérification de la confirmation du mot de passe
    if ($password !== $passwordConfirm) {
        $errors[] = "Les mots de passe ne correspondent pas";
    }

    // Si aucune erreur, créer l'utilisateur
    if (empty($errors)) {
        $user = createUser($pdo, $nickname, $email, $password);

        if ($user) {
            // Inscription réussie, connecter l'utilisateur automatiquement
            $_SESSION['user'] = $user;
            header('location: ../index.php');
            exit();
        } else {
            $errors[] = "Cet email est déjà utilisé";
        }
    }
}

require_once __DIR__ . "/../templates/header.php";
?>

<div class="container col-xxl-8 px-4 py-5">
    <h1>Créer un compte</h1>

    <?php
    foreach ($errors as $error) { ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php }
    ?>

    <form action="" method="post">
        <div class="mb-3">
            <label for="nickname" class="form-label">Pseudonyme</label>
            <input type="text" name="nickname" id="nickname" class="form-control"
                value="<?= htmlspecialchars($_POST['nickname'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control"
                value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" name="password" id="password" class="form-control" required>
            <small class="form-text text-muted">Le mot de passe doit contenir au moins 6 caractères</small>
        </div>
        <div class="mb-3">
            <label for="password_confirm" class="form-label">Confirmer le mot de passe</label>
            <input type="password" name="password_confirm" id="password_confirm" class="form-control" required>
        </div>

        <input type="submit" name="registerUser" value="S'inscrire" class="btn btn-primary">
    </form>

    <div class="mt-3">
        <p>Déjà un compte ? <a href="../login.php">Se connecter</a></p>
    </div>
</div>

<?php require_once __DIR__ . "/../templates/footer.php" ?>

