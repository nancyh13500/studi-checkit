<?php
require_once __DIR__ . "/lib/session.php";
require_once __DIR__ . "/lib/pdo.php";
require_once __DIR__ . "/lib/user.php";

$errors = [];

if (isset($_POST['loginUser'])) {
    $user = verifyUserLoginPassword($pdo, $_POST['email'], $_POST['password']);

    if ($user) {
        // Régénérer l'ID de session pour prévenir les attaques de fixation de session
        session_regenerate_id(true);

        // on va le connecter => session
        $_SESSION['user'] = $user;

        // Créer un token de connexion persistante si "Se souvenir de moi" est coché
        if (isset($_POST['remember_me']) && $_POST['remember_me'] === '1') {
            createRememberToken($user);
        }

        header('location: index.php');
        exit();
    } else {
        // afficher une erreur
        $errors[] = "Email ou mot de passe incorrect";
    }
}

require_once __DIR__ . "/templates/header.php";
?>

<div class="container col-xxl-8 px-4 py-5">
    <h1>Se connecter</h1>

    <?php
    foreach ($errors as $error) { ?>
        <div class="alert alert-danger" role="alert">
            <?= $error; ?>
        </div>
    <?php }
    ?>

    <form action="" method="post">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" name="password" id="password" class="form-control">
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="remember_me" id="remember_me" value="1" class="form-check-input">
            <label for="remember_me" class="form-check-label">Se souvenir de moi</label>
        </div>

        <input type="submit" name="loginUser" value="Connexion" class="btn btn-primary">
    </form>
</div>

<?php require_once __DIR__ . "/templates/footer.php" ?>