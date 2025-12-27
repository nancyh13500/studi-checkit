<?php
require_once __DIR__ . "/lib/session.php";
require_once __DIR__ . "/lib/pdo.php";
require_once __DIR__ . "/lib/user.php";

$errors = [];
$success = false;
$token = $_GET['token'] ?? '';

// Vérifier si le token est présent
if (empty($token)) {
    $errors[] = "Token de réinitialisation manquant";
} else {
    // Vérifier si le token est valide
    $user = verifyResetToken($pdo, $token);
    if (!$user) {
        $errors[] = "Token invalide ou expiré";
    }
}

if (isset($_POST['resetPassword']) && empty($errors)) {
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

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

    // Si aucune erreur, réinitialiser le mot de passe
    if (empty($errors)) {
        if (resetPassword($pdo, $token, $password)) {
            $success = true;
        } else {
            $errors[] = "Erreur lors de la réinitialisation du mot de passe";
        }
    }
}

require_once __DIR__ . "/templates/header.php";
?>

<div class="container col-xxl-8 px-4 py-5">
    <h1>Réinitialiser le mot de passe</h1>

    <?php if ($success) { ?>
        <div class="alert alert-success" role="alert">
            Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.
        </div>
        <p><a href="login.php" class="btn btn-primary">Se connecter</a></p>
    <?php } elseif (!empty($errors)) { ?>
        <?php
        foreach ($errors as $error) { ?>
            <div class="alert alert-danger" role="alert">
                <?= $error; ?>
            </div>
        <?php }
        ?>
        <p><a href="forgot-password.php">Demander un nouveau lien de réinitialisation</a></p>
    <?php } else { ?>
        <p>Entrez votre nouveau mot de passe ci-dessous.</p>

        <form action="" method="post">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            
            <div class="mb-3">
                <label for="password" class="form-label">Nouveau mot de passe</label>
                <input type="password" name="password" id="password" class="form-control" required minlength="6">
            </div>
            
            <div class="mb-3">
                <label for="password_confirm" class="form-label">Confirmer le nouveau mot de passe</label>
                <input type="password" name="password_confirm" id="password_confirm" class="form-control" required minlength="6">
            </div>

            <input type="submit" name="resetPassword" value="Réinitialiser le mot de passe" class="btn btn-primary">
            <a href="login.php" class="btn btn-secondary">Annuler</a>
        </form>
    <?php } ?>
</div>

<?php require_once __DIR__ . "/templates/footer.php" ?>

