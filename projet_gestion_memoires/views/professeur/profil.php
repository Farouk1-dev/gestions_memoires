<?php

session_start();

include("../../config/database.php");

/*
=========================================
SECURITE
=========================================
*/

if(
    !isset($_SESSION['id'])
    ||
    $_SESSION['role'] != 'professeur'
){
    header("Location: ../../login.php");
    exit();
}

/*
=========================================
PROFESSEUR CONNECTÉ
=========================================
*/

$professeur_id = $_SESSION['id'];

/*
=========================================
INFOS PROFESSEUR
=========================================
*/

$professeur = $conn->prepare("
SELECT *
FROM utilisateurs
WHERE id = ?
");

$professeur->execute([$professeur_id]);

$professeur = $professeur->fetch();

/*
=========================================
STATISTIQUES
=========================================
*/

/* MEMOIRES ENCADRÉS */

$memoires = $conn->prepare("
SELECT COUNT(*) as total
FROM soumissions
WHERE professeur_id = ?
");

$memoires->execute([$professeur_id]);

$total_memoires = $memoires->fetch()['total'];

/* MEMOIRES VALIDÉS */

$valides = $conn->prepare("
SELECT COUNT(*) as total
FROM soumissions
WHERE professeur_id = ?
AND statut = 'valide'
");

$valides->execute([$professeur_id]);

$total_valides = $valides->fetch()['total'];

/* EN ATTENTE */

$attente = $conn->prepare("
SELECT COUNT(*) as total
FROM soumissions
WHERE professeur_id = ?
AND statut = 'en_attente'
");

$attente->execute([$professeur_id]);

$total_attente = $attente->fetch()['total'];

/* RETOURNÉS */

$retournes = $conn->prepare("
SELECT COUNT(*) as total
FROM soumissions
WHERE professeur_id = ?
AND statut = 'retourne'
");

$retournes->execute([$professeur_id]);

$total_retournes = $retournes->fetch()['total'];

/*
=========================================
NOTIFICATIONS
=========================================
*/

$notifications = $conn->prepare("
SELECT COUNT(*) as total
FROM notifications
WHERE utilisateur_id = ?
");

$notifications->execute([$professeur_id]);

$total_notifications = $notifications->fetch()['total'];

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <title>
        Profil Professeur
    </title>

    <link rel="stylesheet"
    href="../../assets/css/profil_professeur.css">

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

<div class="container">

    <!-- SIDEBAR -->

    <?php include("../../includes/prof_sidebar.php"); ?>

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

                <!-- NOTIFICATION -->

                <div class="notif">

                    <i class="fa-regular fa-bell"></i>

                    <span>
                        <?= $total_notifications ?>
                    </span>

                </div>

                <!-- PROFIL TOP -->

                <div class="profile-top">

                    <img
                    src="../../uploads/<?=
                    !empty($professeur['photo'])
                    ? $professeur['photo']
                    : 'default.png'
                    ?>">

                    <div>

                        <h3>
                            <?= htmlspecialchars($professeur['nom']) ?>
                        </h3>

                        <p>
                            Maître de mémoire
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
                    src="../../uploads/<?=
                    !empty($professeur['photo'])
                    ? $professeur['photo']
                    : 'default.png'
                    ?>"
                    class="big-profile">

                    <button class="photo-btn">

                        <i class="fa-solid fa-camera"></i>

                        Changer la photo

                    </button>

                </div>

                <!-- CENTER -->

                <div class="center-profile">

                    <h2>
                        <?= htmlspecialchars($professeur['nom']) ?>
                    </h2>

                    <span class="role">

                        Maître de mémoire

                    </span>

                    <div class="infos">

                        <p>

                            <i class="fa-regular fa-envelope"></i>

                            <?= htmlspecialchars($professeur['email']) ?>

                        </p>

                        <p>

                            <i class="fa-solid fa-phone"></i>

                            <?= htmlspecialchars($professeur['telephone']) ?>

                        </p>

                        <p>

                            <i class="fa-solid fa-building"></i>

                            Département d’Informatique

                        </p>

                        <p>

                            <i class="fa-regular fa-id-card"></i>

                            ID :
                            <?= $professeur['id'] ?>

                        </p>

                        <p>

                            <i class="fa-regular fa-calendar"></i>

                            Compte professeur

                        </p>

                    </div>

                </div>

                <!-- STATS -->

                <div class="stats-grid">

                    <!-- MEMOIRES -->

                    <div class="stat blue">

                        <div class="stat-top">

                            <div class="stat-icon blue-icon">

                                <i class="fa-regular fa-file-lines"></i>

                            </div>

                            <h3>
                                Mémoires encadrés
                            </h3>

                        </div>

                        <div class="stat-number">

                            <?= $total_memoires ?>

                        </div>

                    </div>

                    <!-- VALIDÉS -->

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

                            <?= $total_valides ?>

                        </div>

                    </div>

                    <!-- ATTENTE -->

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

                            <?= $total_attente ?>

                        </div>

                    </div>

                    <!-- RETOURNÉS -->

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

                            <?= $total_retournes ?>

                        </div>

                    </div>

                </div>

            </div>

            <!-- BOTTOM -->

            <div class="bottom-grid">

                <!-- INFOS -->

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
                        value="<?= htmlspecialchars($professeur['nom']) ?>">

                    </div>

                    <div class="form-group">

                        <label>
                            Email
                        </label>

                        <input
                        type="text"
                        value="<?= htmlspecialchars($professeur['email']) ?>">

                    </div>

                    <div class="form-group">

                        <label>
                            Téléphone
                        </label>

                        <input
                        type="text"
                        value="<?= htmlspecialchars($professeur['telephone']) ?>">

                    </div>

                    <div class="form-group">

                        <label>
                            Fonction
                        </label>

                        <input
                        type="text"
                        value="Maître de mémoire">

                    </div>

                    <button class="save-btn">

                        Enregistrer les modifications

                    </button>

                </div>

                <!-- SECURITE -->

                <div class="box">

                    <h2>
                        Sécurité du compte
                    </h2>

                    <div class="security-item">

                        <div>

                            <h4>
                                Mot de passe
                            </h4>

                            <p>
                                ************
                            </p>

                        </div>

                        <button>

                            Changer le mot de passe

                        </button>

                    </div>

                    <div class="security-item">

                        <div>

                            <h4>
                                Sessions actives
                            </h4>

                            <p>
                                Compte connecté
                            </p>

                        </div>

                        <button>

                            Actif

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </main>

</div>

</body>
</html>