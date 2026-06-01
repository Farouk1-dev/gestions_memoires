<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
=========================================
SECURITE
=========================================
*/

if(
    !isset($_SESSION['id']) ||
    $_SESSION['role'] != 'professeur'
){

    header("Location: ../../login.php");

    exit();
}

/*
=========================================
CONNEXION BDD
=========================================
*/

include("../../config/database.php");

/*
=========================================
PROF CONNECTÉ
=========================================
*/

$professeur_id = $_SESSION['id'];

/*
=========================================
TOTAL A EVALUER
=========================================
*/

$reqEvaluer = $conn->prepare("
SELECT COUNT(*) AS total
FROM soumissions
WHERE professeur_id = ?
AND statut = 'en_attente'
");

$reqEvaluer->execute([$professeur_id]);

$totalEvaluer =
$reqEvaluer->fetch(PDO::FETCH_ASSOC)['total'];

/*
=========================================
TOTAL VALIDES
=========================================
*/

$reqValides = $conn->prepare("
SELECT COUNT(*) AS total
FROM soumissions
WHERE professeur_id = ?
AND statut = 'valide'
");

$reqValides->execute([$professeur_id]);

$totalValides =
$reqValides->fetch(PDO::FETCH_ASSOC)['total'];

/*
=========================================
TOTAL RETOURNES
=========================================
*/

$reqRetour = $conn->prepare("
SELECT COUNT(*) AS total
FROM soumissions
WHERE professeur_id = ?
AND statut = 'retourne'
");

$reqRetour->execute([$professeur_id]);

$totalRetour =
$reqRetour->fetch(PDO::FETCH_ASSOC)['total'];

/*
=========================================
TOTAL NOTIFICATIONS
=========================================
*/

$reqNotifications = $conn->prepare("
SELECT COUNT(*) AS total
FROM notifications
WHERE utilisateur_id = ?
");

$reqNotifications->execute([$professeur_id]);

$totalNotifications =
$reqNotifications->fetch(PDO::FETCH_ASSOC)['total'];

/*
=========================================
MEMOIRES A EVALUER
=========================================
*/

$reqMemoires = $conn->prepare("
SELECT
    soumissions.*,
    memoires.titre,
    memoires.categorie,
    utilisateurs.nom AS etudiant_nom

FROM soumissions

INNER JOIN memoires
ON soumissions.memoire_id = memoires.id

INNER JOIN utilisateurs
ON soumissions.etudiant_id = utilisateurs.id

WHERE soumissions.professeur_id = ?

ORDER BY soumissions.date_soumission DESC

LIMIT 5
");

$reqMemoires->execute([$professeur_id]);

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Dashboard Professeur
    </title>

    <!-- CSS -->

    <link
        rel="stylesheet"
        href="../../assets/css/dashboard_professeur.css"
    >

    <!-- ICONS -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>

<body>

<div class="container">

    <!-- SIDEBAR -->

    <?php include("../../includes/prof_sidebar.php"); ?>

    <!-- MAIN -->

    <main class="main">

        <!-- HEADER -->

        <header class="header">

            <div class="header-left">

                <i class="fa-solid fa-bars"></i>

                <h1>
                    Tableau de bord - Professeur
                </h1>

            </div>

            <div class="header-right">

                <!-- NOTIFICATIONS -->

                <div class="notif-icon">

                    <i class="fa-regular fa-bell"></i>

                    <span>

                        <?= $totalNotifications ?>

                    </span>

                </div>

                <!-- PROFILE -->

                <div class="profile-box">

                    <?php

                        $prof = $conn->prepare("
                        SELECT photo
                        FROM utilisateurs
                        WHERE id = ?
                        ");

                        $prof->execute([$professeur_id]);

                        $profData = $prof->fetch();

                        ?>

                        <img
                            src="../../uploads/<?=
                            !empty($profData['photo'])
                            ? $profData['photo']
                            : 'default.png'
                            ?>"
                            class="profile"
                        >

                    <div>

                        <h3>

                            <?= $_SESSION['nom'] ?>

                        </h3>

                        <p>
                            Maître de mémoire
                        </p>

                    </div>

                </div>

            </div>

        </header>

        <!-- CONTENT -->

        <section class="content">

            <!-- WELCOME -->

            <h2 class="welcome">

                Bienvenue

                <?= $_SESSION['nom'] ?>

                👋

            </h2>

            <!-- CARDS -->

            <div class="cards">

                <!-- A EVALUER -->

                <div class="card blue">

                    <div class="card-top">

                        <div class="icon">

                            <i class="fa-regular fa-clipboard"></i>

                        </div>

                        <h3>
                            À évaluer
                        </h3>

                    </div>

                    <h1>

                        <?= $totalEvaluer ?>

                    </h1>

                </div>

                <!-- VALIDES -->

                <div class="card green">

                    <div class="card-top">

                        <div class="icon">

                            <i class="fa-regular fa-circle-check"></i>

                        </div>

                        <h3>
                            Validés
                        </h3>

                    </div>

                    <h1>

                        <?= $totalValides ?>

                    </h1>

                </div>

                <!-- RETOUR -->

                <div class="card orange-card">

                    <div class="card-top">

                        <div class="icon">

                            <i class="fa-solid fa-share"></i>

                        </div>

                        <h3>
                            En retour
                        </h3>

                    </div>

                    <h1>

                        <?= $totalRetour ?>

                    </h1>

                </div>

                <!-- NOTIFICATIONS -->

                <div class="card red">

                    <div class="card-top">

                        <div class="icon">

                            <i class="fa-regular fa-bell"></i>

                        </div>

                        <h3>
                            Notifications
                        </h3>

                    </div>

                    <h1>

                        <?= $totalNotifications ?>

                    </h1>

                </div>

            </div>

            <!-- TABLE -->

            <div class="table-section">

                <h2>
                    Mémoires à évaluer
                </h2>

                <table>

                    <thead>

                        <tr>

                            <th>Mémoire</th>
                            <th>Étudiant</th>
                            <th>Date</th>
                            <th>Catégorie</th>
                            <th>Statut</th>
                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php
                    while(
                        $memoire =
                        $reqMemoires->fetch(PDO::FETCH_ASSOC)
                    ):
                    ?>

                        <tr>

                            <!-- TITRE -->

                            <td>

                                <div class="memoire-info">

                                    <div class="pdf-icon">

                                        <i class="fa-solid fa-file-pdf"></i>

                                    </div>

                                    <span>

                                        <?= htmlspecialchars($memoire['titre']) ?>

                                    </span>

                                </div>

                            </td>

                            <!-- ETUDIANT -->

                            <td>

                                <?= htmlspecialchars($memoire['etudiant_nom']) ?>

                            </td>

                            <!-- DATE -->

                            <td>

                                <?= $memoire['date_soumission'] ?>

                            </td>

                            <!-- CATEGORIE -->

                            <td>

                                <?= htmlspecialchars($memoire['categorie']) ?>

                            </td>

                            <!-- STATUT -->

                            <td>

                                <?php
                                if($memoire['statut'] == 'en_attente'){
                                ?>

                                    <span class="status">

                                        À évaluer

                                    </span>

                                <?php
                                }

                                elseif($memoire['statut'] == 'valide'){
                                ?>

                                    <span class="status green-status">

                                        Validé

                                    </span>

                                <?php
                                }

                                else{
                                ?>

                                    <span class="status orange-status">

                                        Retourné

                                    </span>

                                <?php
                                }
                                ?>

                            </td>

                            <!-- ACTION -->

                            <td>

                                <a
                                    href="evaluer.php?id=<?= $memoire['memoire_id'] ?>"
                                    class="btn-eval"
                                >

                                    <i class="fa-regular fa-eye"></i>

                                    Évaluer

                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</div>

</body>
</html>