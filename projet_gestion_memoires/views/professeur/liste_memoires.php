<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if(
    !isset($_SESSION['id']) ||
    $_SESSION['role'] != 'professeur'
){

    header("Location: ../../login.php");

    exit();
}

include("../../config/database.php");

/*
=========================================
PROF CONNECTÉ
=========================================
*/

$professeur_id = $_SESSION['id'];

/*
=========================================
RECUPERATION DES MEMOIRES
=========================================
*/

$query = $conn->prepare("

    SELECT

        soumissions.*,

        memoires.id AS memoire_id,
        memoires.titre,
        memoires.categorie,
        memoires.fichier,

        utilisateurs.nom AS etudiant_nom

    FROM soumissions

    INNER JOIN memoires
    ON soumissions.memoire_id = memoires.id

    INNER JOIN utilisateurs
    ON soumissions.etudiant_id = utilisateurs.id

    WHERE soumissions.professeur_id = ?

    ORDER BY soumissions.id DESC

");

$query->execute([$professeur_id]);

$memoires = $query->fetchAll(PDO::FETCH_ASSOC);

/*
=========================================
STATISTIQUES
=========================================
*/

$total = count($memoires);

$attente = 0;
$valide = 0;
$retour = 0;

foreach($memoires as $memoire){

    if($memoire['statut'] == 'en_attente'){

        $attente++;

    }

    elseif($memoire['statut'] == 'valide'){

        $valide++;

    }

    elseif($memoire['statut'] == 'retour_pour_correction'){

        $retour++;
    }
}


/*
==================================
PHOTO PROFESSEUR
==================================
*/

$profQuery = $conn->prepare("
SELECT photo, nom
FROM utilisateurs
WHERE id = ?
");

$profQuery->execute([$_SESSION['id']]);

$professeur = $profQuery->fetch(PDO::FETCH_ASSOC);

$photoProfil = "default.png";

if(
    !empty($professeur['photo']) &&
    file_exists("../../uploads/" . $professeur['photo'])
){
    $photoProfil = $professeur['photo'];
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

    <title>Liste des mémoires</title>

    <link
        rel="stylesheet"
        href="../../assets/css/liste_memoires.css"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
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

                <h1>Liste des mémoires</h1>

            </div>

            <div class="profile-box">

                <i class="fa-regular fa-bell notif"></i>

                 <img
                    src="../../uploads/<?= $photoProfil ?>"
                    class="profile">

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

        </header>

        <!-- CONTENT -->

        <section class="content">

            <!-- TITLE -->

            <div class="page-title">

                <h2>Tous les mémoires</h2>

                <p>

                    Retrouvez et consultez tous les mémoires soumis dans le système.

                </p>

            </div>

            <!-- STATS -->

            <div class="stats">

                <div class="stat-card">

                    <div class="icon blue">

                        <i class="fa-regular fa-file-lines"></i>

                    </div>

                    <div>

                        <h4>Total mémoires</h4>

                        <h2><?= $total ?></h2>

                        <p>Tous statuts</p>

                    </div>

                </div>

                <div class="stat-card">

                    <div class="icon orange">

                        <i class="fa-regular fa-hourglass-half"></i>

                    </div>

                    <div>

                        <h4>En attente</h4>

                        <h2><?= $attente ?></h2>

                        <p>Soumis</p>

                    </div>

                </div>

                <div class="stat-card">

                    <div class="icon green">

                        <i class="fa-regular fa-circle-check"></i>

                    </div>

                    <div>

                        <h4>Validés</h4>

                        <h2><?= $valide ?></h2>

                        <p>Acceptés</p>

                    </div>

                </div>

                <div class="stat-card">

                    <div class="icon red">

                        <i class="fa-solid fa-arrow-rotate-left"></i>

                    </div>

                    <div>

                        <h4>Retournés</h4>

                        <h2><?= $retour ?></h2>

                        <p>À corriger</p>

                    </div>

                </div>

            </div>

            <!-- TABLE -->

            <div class="table-container">

                <table>

                    <thead>

                        <tr>

                            <th>Titre du mémoire</th>
                            <th>Étudiant</th>
                            <th>Catégorie</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach($memoires as $memoire): ?>

                        <tr>

                            <!-- TITRE -->

                            <td>

                                <div class="memoire-info">

                                    <div class="memoire-icon">

                                        <i class="fa-regular fa-file-lines"></i>

                                    </div>

                                    <div>

                                        <h4>

                                            <?= htmlspecialchars($memoire['titre']) ?>

                                        </h4>

                                        <p>

                                            Soumis par :
                                            <?= htmlspecialchars($memoire['etudiant_nom']) ?>

                                        </p>

                                    </div>

                                </div>

                            </td>

                            <!-- ETUDIANT -->

                            <td>

                                <?= htmlspecialchars($memoire['etudiant_nom']) ?>

                            </td>

                            <!-- CATEGORIE -->

                            <td>

                                <?= htmlspecialchars($memoire['categorie']) ?>

                            </td>

                            <!-- DATE -->

                            <td>

                                <?= date(
                                    'd/m/Y',
                                    strtotime($memoire['date_soumission'])
                                ) ?>

                                <br>

                                <?= date(
                                    'H:i',
                                    strtotime($memoire['date_soumission'])
                                ) ?>

                            </td>

                            <!-- STATUT -->

                            <td>

                                <?php

                                $classe = "orange-status";
                                $texte = "En attente";

                                if($memoire['statut'] == 'valide'){

                                    $classe = "green-status";
                                    $texte = "Validé";

                                }

                                elseif($memoire['statut'] == 'retour_pour_correction'){

                                    $classe = "red-status";
                                    $texte = "Retourné";

                                }

                                ?>

                                <span class="status <?= $classe ?>">

                                    <?= $texte ?>

                                </span>

                            </td>

                            <!-- ACTIONS -->

                            <td>

                                <div class="actions">

                                    <!-- VOIR -->

                                    <a
                                        href="../../uploads/memoire/<?= $memoire['fichier'] ?>"
                                        target="_blank"
                                    >
                                        <button type="button">
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                    </a>

                                    <!-- MENU -->

                                    <div class="dropdown">

                                        <button class="dropdown-btn">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>

                                        <div class="dropdown-content">

                                            <a href="detail_memoire.php?id=<?= $memoire['memoire_id'] ?>">
                                                Voir les détails
                                            </a>

                                            <a href="evaluer.php?id=<?= $memoire['memoire_id'] ?>">
                                                Evaluer
                                            </a>

                                        </div>

                                    </div>

                                </div>

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