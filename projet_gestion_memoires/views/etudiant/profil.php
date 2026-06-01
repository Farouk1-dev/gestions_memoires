<?php

session_start();

if(
    !isset($_SESSION['id']) ||
    $_SESSION['role'] != 'etudiant'
){
    header("Location: ../../login.php");
    exit();
}

include("../../config/database.php");

/*
====================================
UTILISATEUR CONNECTÉ
====================================
*/

$user_id = $_SESSION['id'];

/*
====================================
INFOS ETUDIANT
====================================
*/

$query = $conn->prepare("
SELECT *
FROM utilisateurs
WHERE id = ?
");

$query->execute([$user_id]);

$etudiant = $query->fetch();

/*
====================================
PHOTO PROFIL
====================================
*/

$photoProfil = "default.png";

if(
    !empty($etudiant['photo']) &&
    file_exists("../../uploads/" . $etudiant['photo'])
){
    $photoProfil = $etudiant['photo'];
}

/*
====================================
STATISTIQUES
====================================
*/

/* TOTAL MEMOIRES */

$totalMemoires = $conn->prepare("
SELECT COUNT(*) AS total
FROM memoires
WHERE utilisateur_id = ?
");

$totalMemoires->execute([$user_id]);

$nbMemoires =
$totalMemoires->fetch()['total'];

/* MEMOIRES VALIDES */

$valides = $conn->prepare("
SELECT COUNT(*) AS total
FROM memoires
WHERE utilisateur_id = ?
AND statut = 'valide'
");

$valides->execute([$user_id]);

$nbValides =
$valides->fetch()['total'];

/* EN ATTENTE */

$attente = $conn->prepare("
SELECT COUNT(*) AS total
FROM memoires
WHERE utilisateur_id = ?
AND statut = 'en_attente'
");

$attente->execute([$user_id]);

$nbAttente =
$attente->fetch()['total'];

/* RETOURNES */

$retournes = $conn->prepare("
SELECT COUNT(*) AS total
FROM memoires
WHERE utilisateur_id = ?
AND statut = 'retourne'
");

$retournes->execute([$user_id]);

$nbRetournes =
$retournes->fetch()['total'];

/*
====================================
NOTIFICATIONS
====================================
*/

$notifQuery = $conn->prepare("
SELECT COUNT(*) AS total
FROM notifications
WHERE utilisateur_id = ?
AND statut = 'non_lu'
");

$notifQuery->execute([$user_id]);

$nbNotifications =
$notifQuery->fetch()['total'];

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <title>
        Mon profil
    </title>

    <link rel="stylesheet"
    href="../../assets/css/profil.css">

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

<div class="container">

    <!-- SIDEBAR -->

    <?php include("../../includes/student_sidebar.php"); ?>

    <!-- MAIN -->

    <main class="main">

        <!-- HEADER -->

        <header class="topbar">

            <div class="top-left">

                <i class="fa-solid fa-bars menu-icon"></i>

                <div>

                    <h1>
                        Mon profil
                    </h1>

                    <p>

                        <span>
                            Accueil
                        </span>

                        >

                        Mon profil

                    </p>

                </div>

            </div>

            <div class="top-right">

                <!-- NOTIFICATIONS -->

                <div class="notif">

                    <i class="fa-regular fa-bell"></i>

                    <span>
                        <?= $nbNotifications ?>
                    </span>

                </div>

                <!-- PROFILE TOP -->

                <div class="profile-top">

                    <img
                    src="../../uploads/<?= $photoProfil ?>">

                    <div>

                        <h3>

                            <?= htmlspecialchars($etudiant['nom']) ?>

                        </h3>

                        <p>
                            Étudiant
                        </p>

                    </div>

                    <i class="fa-solid fa-chevron-down"></i>

                </div>

            </div>

        </header>

        <!-- CONTENT -->

        <div class="content">

            <!-- PROFILE CARD -->

            <div class="profile-card">

                <!-- LEFT -->

                <div class="left-profile">

                    <img
                    src="../../uploads/<?= $photoProfil ?>"
                    class="big-profile">

                    <button class="photo-btn">

                        <i class="fa-solid fa-camera"></i>

                        Changer la photo

                    </button>

                </div>

                <!-- CENTER -->

                <div class="center-profile">

                    <h2>

                        <?= htmlspecialchars($etudiant['nom']) ?>

                    </h2>

                    <span class="role">
                        Étudiant
                    </span>

                    <div class="infos">

                        <p>

                            <i class="fa-regular fa-envelope"></i>

                            <?= htmlspecialchars($etudiant['email']) ?>

                        </p>

                        <p>

                            <i class="fa-solid fa-phone"></i>

                            <?= htmlspecialchars($etudiant['telephone']) ?>

                        </p>

                        <p>

                            <i class="fa-regular fa-id-card"></i>

                            ID étudiant :
                            <?= $etudiant['id'] ?>

                        </p>

                        <p>

                            <i class="fa-solid fa-graduation-cap"></i>

                            Gestion des mémoires

                        </p>

                        <p>

                            <i class="fa-solid fa-layer-group"></i>

                            Niveau :
                            Licence / Master

                        </p>

                    </div>

                </div>

                <!-- RIGHT -->

                <div class="stats-grid">

                    <!-- CARD 1 -->

                    <div class="stat blue">

                        <div class="stat-top">

                            <div class="stat-icon blue-icon">

                                <i class="fa-regular fa-file-lines"></i>

                            </div>

                            <h3>
                                Mémoires soumis
                            </h3>

                        </div>

                        <div class="stat-number">

                            <?= $nbMemoires ?>

                        </div>

                        <div class="stat-link">

                            Voir détails

                        </div>

                    </div>

                    <!-- CARD 2 -->

                    <div class="stat green">

                        <div class="stat-top">

                            <div class="stat-icon green-icon">

                                <i class="fa-regular fa-circle-check"></i>

                            </div>

                            <h3>
                                Mémoires validés
                            </h3>

                        </div>

                        <div class="stat-number">

                            <?= $nbValides ?>

                        </div>

                        <div class="stat-link">

                            Voir détails

                        </div>

                    </div>

                    <!-- CARD 3 -->

                    <div class="stat orange">

                        <div class="stat-top">

                            <div class="stat-icon orange-icon">

                                <i class="fa-solid fa-hourglass-half"></i>

                            </div>

                            <h3>
                                En attente
                            </h3>

                        </div>

                        <div class="stat-number">

                            <?= $nbAttente ?>

                        </div>

                        <div class="stat-link">

                            Voir détails

                        </div>

                    </div>

                    <!-- CARD 4 -->

                    <div class="stat red">

                        <div class="stat-top">

                            <div class="stat-icon red-icon">

                                <i class="fa-solid fa-rotate-left"></i>

                            </div>

                            <h3>
                                Retournés
                            </h3>

                        </div>

                        <div class="stat-number">

                            <?= $nbRetournes ?>

                        </div>

                        <div class="stat-link">

                            Voir détails

                        </div>

                    </div>

                </div>

            </div>

            <!-- BOTTOM -->

            <div class="bottom-grid">

                <!-- INFORMATIONS -->

                <div class="box">

                    <h2>
                        Informations personnelles
                    </h2>

                    <div class="form-group">

                        <label>
                            Nom complet
                        </label>

                        <input
                        type="text"
                        value="<?= htmlspecialchars($etudiant['nom']) ?>">

                    </div>

                    <div class="form-group">

                        <label>
                            Email
                        </label>

                        <input
                        type="text"
                        value="<?= htmlspecialchars($etudiant['email']) ?>">

                    </div>

                    <div class="form-group">

                        <label>
                            Téléphone
                        </label>

                        <input
                        type="text"
                        value="<?= htmlspecialchars($etudiant['telephone']) ?>">

                    </div>

                    <button class="save-btn">

                        Modifier mes informations

                    </button>

                </div>

                <!-- SECURITE -->

                <div class="box">

                    <h2>
                        Sécurité du compte
                    </h2>

                    <!-- PASSWORD -->

                    <div class="security-item">

                        <div>

                            <h4>
                                Mot de passe
                            </h4>

                            <p>
                                *************
                            </p>

                        </div>

                        <button>

                            Changer le mot de passe

                        </button>

                    </div>

                    <!-- AUTH -->

                    <div class="security-item">

                        <div>

                            <h4>
                                Authentification
                            </h4>

                            <p>
                                Compte sécurisé
                            </p>

                        </div>

                        <span class="active-badge">

                            Activée

                        </span>

                    </div>

                    <!-- SESSION -->

                    <div class="security-item">

                        <div>

                            <h4>
                                Sessions actives
                            </h4>

                            <p>
                                Voir les appareils connectés
                            </p>

                        </div>

                        <button>

                            Voir les sessions

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </main>

</div>

</body>
</html>