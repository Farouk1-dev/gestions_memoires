<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

/*
=========================================
SECURITE
=========================================
*/

if(
    !isset($_SESSION['id']) ||
    $_SESSION['role'] != 'etudiant'
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
ETUDIANT CONNECTÉ
=========================================
*/

$etudiant_id = $_SESSION['id'];

/*
=========================================
TOTAL MEMOIRES
=========================================
*/

$reqMemoires = $conn->prepare("
SELECT COUNT(*) AS total
FROM memoires
WHERE utilisateur_id = ?
");

$reqMemoires->execute([$etudiant_id]);

$totalMemoires =
$reqMemoires->fetch(PDO::FETCH_ASSOC)['total'];

/*
=========================================
TOTAL SOUMISSIONS
=========================================
*/

$reqSoumissions = $conn->prepare("
SELECT COUNT(*) AS total
FROM soumissions
WHERE etudiant_id = ?
");

$reqSoumissions->execute([$etudiant_id]);

$totalSoumissions =
$reqSoumissions->fetch(PDO::FETCH_ASSOC)['total'];

/*
=========================================
TOTAL VALIDES
=========================================
*/

$reqValides = $conn->prepare("
SELECT COUNT(*) AS total
FROM soumissions
WHERE etudiant_id = ?
AND statut = 'valide'
");

$reqValides->execute([$etudiant_id]);

$totalValides =
$reqValides->fetch(PDO::FETCH_ASSOC)['total'];

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

$reqNotifications->execute([$etudiant_id]);

$totalNotifications =
$reqNotifications->fetch(PDO::FETCH_ASSOC)['total'];

/*
=========================================
MEMOIRES RECENTS
=========================================
*/

$reqRecents = $conn->prepare("
SELECT *
FROM memoires
WHERE utilisateur_id = ?
ORDER BY created_at DESC
LIMIT 5
");

$reqRecents->execute([$etudiant_id]);

?>

<?php

/*
====================================
RECUPERER PHOTO UTILISATEUR
====================================
*/

$reqUser = $conn->prepare("
SELECT photo
FROM utilisateurs
WHERE id = ?
");

$reqUser->execute([$etudiant_id]);

$user = $reqUser->fetch(PDO::FETCH_ASSOC);

$photoProfil = "default.png";

if(
    !empty($user['photo']) &&
    file_exists("../../uploads/" . $user['photo'])
){
    $photoProfil = $user['photo'];
}
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
        Dashboard Étudiant
    </title>

    <!-- CSS -->

    <link
        rel="stylesheet"
        href="../../assets/css/dashboard_etudiant.css"
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

    <?php include("../../includes/student_sidebar.php"); ?>

    <!-- MAIN -->

    <div class="main">

        <!-- HEADER -->

        <div class="header">

            <div class="header-left">

                <i class="fa-solid fa-bars"></i>

                <h1>
                    Tableau de bord - Étudiant
                </h1>

            </div>

            <!-- PROFILE -->

            <div class="profile-box">

                <img
                    src="../../uploads/<?= $photoProfil ?>"
                    class="profile"
                >

                <div>

                    <h3>

                        <?= $_SESSION['nom'] ?>

                    </h3>

                    <p>
                        Étudiant
                    </p>

                </div>

            </div>

        </div>

        <!-- CONTENT -->

        <div class="content">

            <!-- WELCOME -->

            <h2 class="welcome">

                Bienvenue

                <?= $_SESSION['nom'] ?>

                👋

            </h2>

            <!-- CARDS -->

            <div class="cards">

                <!-- MEMOIRES -->

                <div class="card blue">

                    <div class="card-top">

                        <div class="icon">

                            <i class="fa-regular fa-file-lines"></i>

                        </div>

                        <h3>
                            Mes mémoires
                        </h3>

                    </div>

                    <h1>

                        <?= $totalMemoires ?>

                    </h1>

                </div>

                <!-- SOUMISSIONS -->

                <div class="card orange">

                    <div class="card-top">

                        <div class="icon">

                            <i class="fa-regular fa-paper-plane"></i>

                        </div>

                        <h3>
                            Soumissions
                        </h3>

                    </div>

                    <h1>

                        <?= $totalSoumissions ?>

                    </h1>

                </div>

                <!-- VALIDES -->

                <div class="card green">

                    <div class="card-top">

                        <div class="icon">

                            <i class="fa-regular fa-circle-check"></i>

                        </div>

                        <h3>
                            Mémoires validés
                        </h3>

                    </div>

                    <h1>

                        <?= $totalValides ?>

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

            <!-- MEMOIRES RECENTS -->

            <section class="recent-section">

                <h2 class="section-title">

                    Mes mémoires récents

                </h2>

                <div class="recent-box">

                    <?php
                    while(
                        $memoire =
                        $reqRecents->fetch(PDO::FETCH_ASSOC)
                    ):
                    ?>

                    <div class="memoire-item">

                        <!-- LEFT -->

                        <div class="memoire-left">

                            <div class="memoire-icon">

                                <i class="fa-solid fa-file-pdf"></i>

                            </div>

                            <div>

                                <h3>

                                    <?= htmlspecialchars($memoire['titre']) ?>

                                </h3>

                                <p>

                                    <?= $memoire['created_at'] ?>

                                    •

                                    <?= htmlspecialchars($memoire['categorie']) ?>

                                </p>

                            </div>

                        </div>

                        <!-- STATUS -->

                        <?php
                        if($memoire['statut'] == 'soumis'){
                        ?>

                        <span class="badge-status blue-badge">

                            Soumis

                        </span>

                        <?php
                        }

                        elseif(
                            $memoire['statut']
                            == 'en_attente'
                        ){
                        ?>

                        <span class="badge-status orange-badge">

                            En attente

                        </span>

                        <?php
                        }

                        elseif(
                            $memoire['statut']
                            == 'valide'
                        ){
                        ?>

                        <span class="badge-status green-badge">

                            Validé

                        </span>

                        <?php
                        }

                        else{
                        ?>

                        <span class="badge-status gray-badge">

                            Brouillon

                        </span>

                        <?php
                        }
                        ?>

                    </div>

                    <?php endwhile; ?>

                </div>

                <!-- BUTTON -->

                <a
                    href="interactions.php"
                    class="view-all"
                >

                    <i class="fa-regular fa-folder"></i>

                    Voir tous mes mémoires

                </a>

            </section>

        </div>

    </div>

</div>

</body>

</html>