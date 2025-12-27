<?php
require_once __DIR__ . "/../lib/session.php";

// Déterminer le chemin de base selon l'emplacement du fichier qui inclut header.php
$basePath = (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../' : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <div class="container">
        <header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom">
            <div class="col-md-3 mb-2 mb-md-0">
                <a href="<?= $basePath ?>index.php" class="d-inline-flex link-body-emphasis text-decoration-none">
                    <img src="/assets/images/logo-checkit.png" alt="Logo CheckIt" width="180">
                </a>
            </div>

            <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
                <li><a href="<?= $basePath ?>index.php" class="nav-link px-2 link-secondary">Accueil</a></li>
                <li><a href="<?= $basePath ?>pages/mes-listes.php" class="nav-link px-2">Mes listes</a></li>
                <li><a href="<?= $basePath ?>pages/faq.php" class="nav-link px-2">FAQs</a></li>
                <li><a href="<?= $basePath ?>pages/a-propos.php" class="nav-link px-2">A propos</a></li>
            </ul>

            <div class="col-md-3 text-end">
                <?php if (isUserConnected()) { ?>
                    <a href="<?= $basePath ?>logout.php" class="btn btn-outline-primary me-2">Déconnexion</a>
                <?php } else { ?>
                    <a href="<?= $basePath ?>login.php" class="btn btn-outline-primary me-2">Se connecter</a>
                <?php } ?>
            </div>
        </header>
    </div>