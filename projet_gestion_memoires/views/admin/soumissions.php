<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include("../../config/database.php");

include("../../models/Soumission.php");

/*
=========================================
UTILISATEUR CONNECTÉ
=========================================
*/

if(
    !isset($_SESSION['id']) ||
    $_SESSION['role'] != 'admin'
){

    header("Location: ../../login.php");

    exit();
}

$query = $conn->prepare("

SELECT
soumissions.*,

memoires.titre AS memoire_titre,

etudiant.nom AS etudiant_nom,

prof.nom AS professeur_nom

FROM soumissions

INNER JOIN memoires
ON soumissions.memoire_id = memoires.id

INNER JOIN utilisateurs etudiant
ON soumissions.etudiant_id = etudiant.id

INNER JOIN utilisateurs prof
ON soumissions.professeur_id = prof.id

ORDER BY soumissions.id DESC

");

$query->execute();

$soumissions = $query->fetchAll(PDO::FETCH_ASSOC);

/*
====================================
PHOTO ADMIN
====================================
*/

$userQuery = $conn->prepare("
SELECT photo
FROM utilisateurs
WHERE id = ?
");

$userQuery->execute([$_SESSION['id']]);

$user = $userQuery->fetch(PDO::FETCH_ASSOC);

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mes soumissions</title>

    <!-- CSS -->

    <link rel="stylesheet" href="../../assets/css/soumissions.css">

    <!-- ICONS -->

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

</head>

<body>

<div class="container">

    <!-- SIDEBAR -->

        <?php include("../../includes/admin_header.php"); ?>

    <!-- MAIN -->

    <main class="main">

        <!-- HEADER -->

        <header class="header">

            <div class="header-left">

                <i class="fa-solid fa-bars"></i>

                <div>

                    <h1>10. Mémoires soumis </h1>

                    <div class="breadcrumb">

                        <span>Tous les mémoires</span>

                        <i class="fa-solid fa-angle-right"></i>

                        <p>Mémoires soumis</p>

                    </div>

                </div>

            </div>

            <!-- RIGHT -->

            <div class="header-right">

                <!-- NOTIFICATION -->

                <div class="notif">

                    <i class="fa-regular fa-bell"></i>

                    <span>3</span>

                </div>

                <!-- PROFILE -->

                <div class="profile-box">

                    <img
                    src="../../uploads/<?= $photoProfil ?>"
                    class="profile">

                    <div>

                        <h3>

                            <?= htmlspecialchars($_SESSION['nom']) ?>

                        </h3>

                        <p>
                            Administrateur
                        </p>

                    </div>

                    <i class="fa-solid fa-angle-down"></i>

                </div>

            </div>

        </header>
        
                <!-- CONTENT -->

        <section class="content">

            <div class="page-header">

                <div>

                    <h2 class="page-title">
                        Toutes les soumissions
                    </h2>

                    <p class="page-subtitle">
                        Liste complète des mémoires soumis par les étudiants
                    </p>

                </div>

                <div class="total-box">

                    <i class="fa-solid fa-file-circle-check"></i>

                    <span>

                        <?= count($soumissions) ?>

                    </span>

                </div>

            </div>

            <!-- TABLE -->

            <div class="table-container">

                <table class="soumission-table">

                    <thead>

                        <tr>

                            <th>Titre du mémoire</th>

                            <th>Étudiant</th>

                            <th>Professeur</th>

                            <th>Commentaire</th>

                            <th>Date</th>

                            <th>Statut</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if(count($soumissions) > 0): ?>

                            <?php foreach($soumissions as $soumission): ?>

                                <tr>

                                    <!-- TITRE -->

                                    <td class="memoire-cell">

                                        <div class="memoire-info">

                                            <div class="pdf-icon">

                                                <i class="fa-solid fa-file-pdf"></i>

                                            </div>

                                            <div>

                                                <h4>

                                                    <?= htmlspecialchars($soumission['memoire_titre']) ?>

                                                </h4>

                                            </div>

                                        </div>

                                    </td>

                                    <!-- ETUDIANT -->

                                    <td>

                                        <span class="user-badge">

                                            <?= htmlspecialchars($soumission['etudiant_nom']) ?>

                                        </span>

                                    </td>

                                    <!-- PROFESSEUR -->

                                    <td>

                                        <span class="prof-badge">

                                            <?= htmlspecialchars($soumission['professeur_nom']) ?>

                                        </span>

                                    </td>

                                    <!-- COMMENTAIRE -->

                                    <td class="commentaire">

                                        <?= htmlspecialchars($soumission['commentaire']) ?>

                                    </td>

                                    <!-- DATE -->

                                    <td>

                                        <div class="date-box">

                                            <i class="fa-regular fa-calendar"></i>

                                            <?= htmlspecialchars($soumission['date_soumission']) ?>

                                        </div>

                                    </td>

                                    <!-- STATUT -->

                                    <td>

                                        <?php
                                        
                                        $statut =
                                        strtolower($soumission['statut']);

                                        ?>

                                        <span class="status <?= $statut ?>">

                                            <?= htmlspecialchars($soumission['statut']) ?>

                                        </span>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="6">

                                    <div class="empty-box">

                                        <i class="fa-regular fa-folder-open"></i>

                                        <p>
                                            Aucune soumission trouvée
                                        </p>

                                    </div>

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</div>

</body>
</html>