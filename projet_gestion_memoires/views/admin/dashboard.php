<?php

session_start();

include("../../config/database.php");

/*
=========================================
SECURITE ADMIN
=========================================
*/

if(
    !isset($_SESSION['id'])
    ||
    $_SESSION['role'] != 'admin'
){

    header("Location: ../../login.php");
    exit();
}

/*
=========================================
TOTAL UTILISATEURS
=========================================
*/

$reqUsers = $conn->query("
SELECT COUNT(*) AS total
FROM utilisateurs
");

$totalUsers =
$reqUsers->fetch(PDO::FETCH_ASSOC)['total'];

/*
=========================================
TOTAL MEMOIRES
=========================================
*/

$reqMemoires = $conn->query("
SELECT COUNT(*) AS total
FROM memoires
");

$totalMemoires =
$reqMemoires->fetch(PDO::FETCH_ASSOC)['total'];

/*
=========================================
TOTAL SOUMISSIONS
=========================================
*/

$reqSoumissions = $conn->query("
SELECT COUNT(*) AS total
FROM soumissions
");

$totalSoumissions =
$reqSoumissions->fetch(PDO::FETCH_ASSOC)['total'];

/*
=========================================
TOTAL COMMENTAIRES
=========================================
*/

$reqCommentaires = $conn->query("
SELECT COUNT(*) AS total
FROM commentaires
");

$totalCommentaires =
$reqCommentaires->fetch(PDO::FETCH_ASSOC)['total'];

/*
=========================================
DERNIERS MEMOIRES
=========================================
*/

$recentMemoires = $conn->query("
SELECT memoires.titre,
utilisateurs.nom,
memoires.created_at

FROM memoires

INNER JOIN utilisateurs
ON memoires.utilisateur_id = utilisateurs.id

ORDER BY memoires.created_at DESC

LIMIT 3
");

/*
=========================================
DERNIERES SOUMISSIONS
=========================================
*/

$recentSoumissions = $conn->query("
SELECT memoires.titre,
utilisateurs.nom,
soumissions.date_soumission

FROM soumissions

INNER JOIN memoires
ON soumissions.memoire_id = memoires.id

INNER JOIN utilisateurs
ON soumissions.etudiant_id = utilisateurs.id

ORDER BY soumissions.date_soumission DESC

LIMIT 3
");

/*
=========================================
DERNIERS COMMENTAIRES
=========================================
*/

$recentCommentaires = $conn->query("
SELECT commentaires.commentaire,
utilisateurs.nom,
commentaires.created_at

FROM commentaires

INNER JOIN utilisateurs
ON commentaires.utilisateur_id = utilisateurs.id

ORDER BY commentaires.created_at DESC

LIMIT 3
");
?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <title>
        Dashboard Administrateur
    </title>

    <link rel="stylesheet"
    href="../../assets/css/admin.css">

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

<div class="container">

    <!-- SIDEBAR -->

    <!-- MAIN -->

    <main class="main">

        <!-- HEADER -->

        <?php include("../../includes/admin_header.php"); ?>

        <!-- CONTENT -->

        <div class="content">

            <!-- TITLE -->

            <h2 class="welcome">

                Bienvenue Administrateur 👋

            </h2>

            <!-- CARDS -->

            <div class="cards">

                <!-- USERS -->

                <div class="card blue">

                    <div class="card-top">

                        <div class="icon">

                            <i class="fa-solid fa-users"></i>

                        </div>

                        <h3>Utilisateurs</h3>

                    </div>

                    <h1><?= $totalUsers ?></h1>

                </div>

                <!-- MEMOIRES -->

                <div class="card green">

                    <div class="card-top">

                        <div class="icon">

                            <i class="fa-regular fa-file-lines"></i>

                        </div>

                        <h3>Mémoires</h3>

                    </div>

                    <h1><?= $totalMemoires ?></h1>

                </div>

                <!-- SOUMISSIONS -->

                <div class="card purple">

                    <div class="card-top">

                        <div class="icon">

                            <i class="fa-regular fa-folder-open"></i>

                        </div>

                        <h3>Soumissions</h3>

                    </div>

                    <h1><?= $totalSoumissions ?></h1>

                </div>

                <!-- COMMENTAIRES -->

                <div class="card red">

                    <div class="card-top">

                        <div class="icon">

                            <i class="fa-regular fa-message"></i>

                        </div>

                        <h3>Commentaires</h3>

                    </div>

                    <h1><?= $totalCommentaires ?></h1>

                </div>

            </div>

            <!-- ACTIVITES -->

            <div class="recent-section">

                <h2 class="section-title">

                    Activité récente

                </h2>

                <div class="recent-box">

                    <!-- MEMOIRES -->

                    <?php
                    while(
                        $memoire =
                        $recentMemoires->fetch(PDO::FETCH_ASSOC)
                    ):
                    ?>

                    <div class="activity-item">

                        <div class="activity-left">

                            <div class="activity-icon blue-icon">

                                <i class="fa-regular fa-file-lines"></i>

                            </div>

                            <p>

                                Nouveau mémoire :

                                <strong>
                                    <?= $memoire['titre'] ?>
                                </strong>

                                par

                                <?= $memoire['nom'] ?>

                            </p>

                        </div>

                        <span>

                            <i class="fa-regular fa-clock"></i>

                            <?= $memoire['created_at'] ?>

                        </span>

                    </div>

                    <?php endwhile; ?>

                    <!-- SOUMISSIONS -->

                    <?php
                    while(
                        $soumission =
                        $recentSoumissions->fetch(PDO::FETCH_ASSOC)
                    ):
                    ?>

                    <div class="activity-item">

                        <div class="activity-left">

                            <div class="activity-icon green-icon">

                                <i class="fa-regular fa-folder-open"></i>

                            </div>

                            <p>

                                Soumission :

                                <strong>
                                    <?= $soumission['titre'] ?>
                                </strong>

                                par

                                <?= $soumission['nom'] ?>

                            </p>

                        </div>

                        <span>

                            <i class="fa-regular fa-clock"></i>

                            <?= $soumission['date_soumission'] ?>

                        </span>

                    </div>

                    <?php endwhile; ?>

                    <!-- COMMENTAIRES -->

                    <?php
                    while(
                        $commentaire =
                        $recentCommentaires->fetch(PDO::FETCH_ASSOC)
                    ):
                    ?>

                    <div class="activity-item">

                        <div class="activity-left">

                            <div class="activity-icon purple-icon">

                                <i class="fa-regular fa-message"></i>

                            </div>

                            <p>

                                <?= $commentaire['nom'] ?>

                                a commenté :

                                <strong>
                                    "<?= $commentaire['commentaire'] ?>"
                                </strong>

                            </p>

                        </div>

                        <span>

                            <i class="fa-regular fa-clock"></i>

                            <?= $commentaire['created_at'] ?>

                        </span>

                    </div>

                    <?php endwhile; ?>

                    <!-- FOOTER -->

                    <div class="activity-footer">

                        Voir toutes les activités

                        <i class="fa-solid fa-arrow-right"></i>

                    </div>

                </div>

            </div>

        </div>

    </main>

</div>

</body>
</html>