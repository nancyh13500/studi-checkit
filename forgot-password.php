<?php
require_once __DIR__ . "/lib/session.php";
require_once __DIR__ . "/lib/pdo.php";
require_once __DIR__ . "/lib/user.php";

$errors = [];
$success = false;

if (isset($_POST['requestReset'])) {
    $email = trim($_POST['email'] ?? '');

    // Validation de l'email
    if (empty($email)) {
        $errors[] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    } else {
        // Créer le token de réinitialisation
        $token = createPasswordResetToken($pdo, $email);
        
        if ($token) {
            // Envoyer l'email de réinitialisation
            if (sendPasswordResetEmail($email, $token)) {
                $success = true;
            } else {
                $errors[] = "Erreur lors de l'envoi de l'email. Veuillez réessayer.";
            }
        } else {
            // Pour des raisons de sécurité, on affiche le même message même si l'email n'existe pas
            $success = true;
        }
    }
}

require_once __DIR__ . "/templates/header.php";
?>

<div class="container col-xxl-8 px-4 py-5">
    <h1>Mot de passe oublié</h1>

    <?php if ($success) { ?>
        <div class="alert alert-success" role="alert">
            Si cet email existe dans notre système, vous recevrez un lien de réinitialisation par email.
        </div>
        <p><a href="login.php">Retour à la connexion</a></p>
    <?php } else { ?>
        <p>Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>

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
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <input type="submit" name="requestReset" value="Envoyer le lien de réinitialisation" class="btn btn-primary">
            <a href="login.php" class="btn btn-secondary">Annuler</a>
        </form>
    <?php } ?>
</div>

<?php require_once __DIR__ . "/templates/footer.php" ?>

