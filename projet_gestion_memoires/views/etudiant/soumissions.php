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
    $_SESSION['role'] != 'etudiant'
){

    header("Location: ../../login.php");

    exit();
}

$utilisateur_id = $_SESSION['id'];

/* ================= MODEL ================= */

$soumissionModel =
new Soumission($conn);

/* ================= DATA ================= */

$soumissions =
$soumissionModel
->getSoumissionsEtudiant(
    $utilisateur_id
);

/*
====================================
PHOTO UTILISATEUR
====================================
*/

$userQuery = $conn->prepare("
SELECT photo
FROM utilisateurs
WHERE id = ?
");

$userQuery->execute([$utilisateur_id]);

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

    <?php include("../../includes/student_sidebar.php"); ?>

    <!-- MAIN -->

    <main class="main">

        <!-- HEADER -->

        <header class="header">

            <div class="header-left">

                <i class="fa-solid fa-bars"></i>

                <div>

                    <h1>10. Mémoires soumis - Étudiant</h1>

                    <div class="breadcrumb">

                        <span>Nouveau mémoire</span>

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

                    <div class="profile-box">

                        <img
                        src="../../uploads/<?= $photoProfil ?>">

                    <div>

                        <h3>

                            <?= htmlspecialchars($_SESSION['nom']) ?>

                        </h3>

                        <p>
                            Étudiant
                        </p>

                    </div>

                    <i class="fa-solid fa-angle-down"></i>

                </div>


                </div>

            </div>

        </header>

        <!-- CONTENT -->

        <section class="content">

            <h2 class="page-title">Mes soumissions</h2>

            <!-- TABLE -->

            <div class="table-box">

                <table>

                    <thead>

                        <tr>

                            <th>Titre du mémoire</th>

                            <th>Maître de mémoire</th>

                            <th>Date de soumission</th>

                            <th>Statut</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach(
                            $soumissions as $soumission
                        ): ?>

                        <tr>

                            <!-- TITRE -->

                            <td class="memoire-title">

                                <div class="memoire-info">

                                    <!-- ICON -->

                                    <div class="pdf-icon">

                                        <i class="fa-solid fa-file-pdf"></i>

                                    </div>

                                    <!-- TITLE -->

                                    <span>

                                        <?= htmlspecialchars(
                                            $soumission['titre']
                                        ) ?>

                                    </span>

                                </div>

                            </td>

                            <!-- PROF -->

                            <td>

                                <?= htmlspecialchars(
                                    $soumission['nom']
                                ) ?>

                            </td>

                            <!-- DATE -->

                            <td>

                                <?= date(
                                    'd/m/Y',
                                    strtotime(
                                        $soumission['date_soumission']
                                    )
                                ) ?>

                            </td>

                            <!-- STATUT -->

                            <td>

                                <?php

                                $statut =
                                $soumission['statut'];

                                /* ================= EN ATTENTE ================= */

                                if(
                                    $statut == 'en_attente'
                                ){

                                    echo '

                                    <span class="badge orange">

                                        <i class="fa-regular fa-hourglass-half"></i>

                                        En attente

                                    </span>

                                    ';
                                }

                                /* ================= VALIDE ================= */

                                elseif(
                                    $statut == 'valide'
                                ){

                                    echo '

                                    <span class="badge green">

                                        <i class="fa-regular fa-circle-check"></i>

                                        Validé

                                    </span>

                                    ';
                                }

                                /* ================= RETOUR ================= */

                                elseif(
                                    $statut
                                    == 'retour_pour_correction'
                                ){

                                    echo '

                                    <span class="badge red">

                                        <i class="fa-solid fa-rotate-left"></i>

                                        Retour pour correction

                                    </span>

                                    ';
                                }

                                ?>

                            </td>

                        </tr>

                        <?php endforeach; ?>

                        </tbody>

                </table>

            </div>

        </section>

    </main>

</div>

</body>
</html>