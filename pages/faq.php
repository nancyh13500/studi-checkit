<?php

require_once __DIR__ . "/../templates/header.php";
?>

<div class="container col-xxl-8 px-4 py-5">
    <div class="row">
        <div class="col-12 text-center mb-5">
            <h1 class="display-5 fw-bold mb-3">Foire aux Questions</h1>
            <p class="lead">Trouvez les réponses aux questions les plus fréquentes sur CheckIt</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="accordion" id="faqAccordion">

                <!-- Question 1 -->
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
                            <i class="bi bi-question-circle me-2"></i>
                            Qu'est-ce que CheckIt ?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            CheckIt est une plateforme en ligne qui vous permet de créer et gérer vos listes de tâches de manière simple et organisée. Vous pouvez créer un nombre illimité de listes, les classer par catégories et suivre vos tâches en temps réel.
                        </div>
                    </div>
                </div>

                <!-- Question 2 -->
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
                            <i class="bi bi-person-plus me-2"></i>
                            Comment créer un compte ?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Pour créer un compte sur CheckIt, cliquez sur le bouton "Se connecter" dans le menu, puis sélectionnez "S'inscrire". Remplissez le formulaire avec vos informations (nom d'utilisateur, email et mot de passe) et validez votre inscription. C'est gratuit et ne prend que quelques instants !
                        </div>
                    </div>
                </div>

                <!-- Question 3 -->
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                            <i class="bi bi-card-checklist me-2"></i>
                            Comment créer une nouvelle liste ?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Une fois connecté, vous pouvez créer une nouvelle liste en cliquant sur le bouton "Ajouter une liste" depuis la page "Mes listes". Donnez un titre à votre liste, choisissez une catégorie (ou créez-en une nouvelle), puis ajoutez vos premiers éléments de liste. Vous pouvez ensuite ajouter autant d'éléments que vous le souhaitez.
                        </div>
                    </div>
                </div>

                <!-- Question 4 -->
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
                            <i class="bi bi-tags-fill me-2"></i>
                            Comment fonctionnent les catégories ?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Les catégories vous permettent d'organiser vos listes par thème (travail, courses, voyage, etc.). Vous pouvez créer autant de catégories que vous le souhaitez et leur attribuer une icône personnalisée. Une fois créées, vous pouvez filtrer vos listes par catégorie depuis la page "Mes listes" pour retrouver rapidement ce que vous cherchez.
                        </div>
                    </div>
                </div>

                <!-- Question 5 -->
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false" aria-controls="faq5">
                            <i class="bi bi-check-circle me-2"></i>
                            Comment marquer une tâche comme terminée ?
                        </button>
                    </h2>
                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Pour marquer une tâche comme terminée, ouvrez votre liste et cliquez sur la case à cocher à côté de l'élément. La tâche sera automatiquement barrée et marquée comme complétée. Vous pouvez également décocher une tâche si vous souhaitez la réactiver.
                        </div>
                    </div>
                </div>

                <!-- Question 6 -->
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6" aria-expanded="false" aria-controls="faq6">
                            <i class="bi bi-pencil-square me-2"></i>
                            Puis-je modifier ou supprimer une liste ?
                        </button>
                    </h2>
                    <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Oui, vous pouvez modifier vos listes à tout moment. Cliquez sur "Voir la liste" depuis la page "Mes listes" pour accéder à la page de modification. Vous pouvez modifier le titre, changer la catégorie, ajouter ou supprimer des éléments, et même supprimer la liste entière si nécessaire.
                        </div>
                    </div>
                </div>

                <!-- Question 7 -->
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7" aria-expanded="false" aria-controls="faq7">
                            <i class="bi bi-shield-lock me-2"></i>
                            Mes données sont-elles sécurisées ?
                        </button>
                    </h2>
                    <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Oui, la sécurité de vos données est une priorité pour CheckIt. Vos listes sont privées et accessibles uniquement depuis votre compte. Nous utilisons des méthodes de sécurité standard pour protéger vos informations personnelles et vos données.
                        </div>
                    </div>
                </div>

                <!-- Question 8 -->
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq8" aria-expanded="false" aria-controls="faq8">
                            <i class="bi bi-phone me-2"></i>
                            Puis-je utiliser CheckIt sur mobile ?
                        </button>
                    </h2>
                    <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            CheckIt est une application web responsive, ce qui signifie qu'elle s'adapte automatiquement à tous les types d'écrans, y compris les smartphones et les tablettes. Vous pouvez accéder à vos listes depuis n'importe quel appareil disposant d'une connexion Internet et d'un navigateur web.
                        </div>
                    </div>
                </div>

                <!-- Question 9 -->
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq9" aria-expanded="false" aria-controls="faq9">
                            <i class="bi bi-currency-euro me-2"></i>
                            CheckIt est-il gratuit ?
                        </button>
                    </h2>
                    <div id="faq9" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Oui, CheckIt est entièrement gratuit ! Vous pouvez créer un compte, créer autant de listes et de catégories que vous le souhaitez, et utiliser toutes les fonctionnalités sans aucun frais.
                        </div>
                    </div>
                </div>

                <!-- Question 10 -->
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq10" aria-expanded="false" aria-controls="faq10">
                            <i class="bi bi-question-octagon me-2"></i>
                            J'ai oublié mon mot de passe, que faire ?
                        </button>
                    </h2>
                    <div id="faq10" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Si vous avez oublié votre mot de passe, contactez-nous via la page "A propos" ou utilisez la fonctionnalité de réinitialisation de mot de passe si elle est disponible. Nous vous aiderons à récupérer l'accès à votre compte dans les plus brefs délais.
                        </div>
                    </div>
                </div>

                <!-- Question 11 -->
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq11" aria-expanded="false" aria-controls="faq11">
                            <i class="bi bi-people me-2"></i>
                            Puis-je partager mes listes avec d'autres personnes ?
                        </button>
                    </h2>
                    <div id="faq11" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Actuellement, CheckIt est conçu pour un usage personnel. Chaque liste est privée et associée à votre compte. Si vous souhaitez collaborer sur des listes, vous pouvez créer un compte partagé ou cette fonctionnalité pourrait être ajoutée dans une future mise à jour.
                        </div>
                    </div>
                </div>

                <!-- Question 12 -->
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq12" aria-expanded="false" aria-controls="faq12">
                            <i class="bi bi-download me-2"></i>
                            Puis-je exporter mes listes ?
                        </button>
                    </h2>
                    <div id="faq12" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Pour l'instant, l'exportation de listes n'est pas disponible, mais cette fonctionnalité pourrait être prévue dans les prochaines versions de CheckIt. Vos données restent accessibles en ligne à tout moment depuis votre compte.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../templates/footer.php" ?>