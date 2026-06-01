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
==================================
VERIFICATION ID MEMOIRE
==================================
*/

if(!isset($_GET['id'])){

    die("Mémoire introuvable");

}

$id = intval($_GET['id']);

/*
==================================
RECUPERATION MEMOIRE
==================================
*/

$query = $conn->prepare("
    SELECT 
        memoires.*,
        utilisateurs.nom
    FROM memoires
    INNER JOIN utilisateurs
    ON memoires.utilisateur_id = utilisateurs.id
    WHERE memoires.id = ?
");

$query->execute([$id]);

$memoire = $query->fetch(PDO::FETCH_ASSOC);

if(!$memoire){

    die("Mémoire non trouvé");

}

/*
==================================
NOMBRE LIKES
==================================
*/

$likeQuery = $conn->prepare("
    SELECT COUNT(*) as total
    FROM likes
    WHERE memoire_id = ?
");

$likeQuery->execute([$id]);

$likes = $likeQuery->fetch(PDO::FETCH_ASSOC);

/*
==================================
NOMBRE COMMENTAIRES
==================================
*/

$commentQuery = $conn->prepare("
    SELECT COUNT(*) as total
    FROM commentaires
    WHERE memoire_id = ?
");

$commentQuery->execute([$id]);

$commentaires = $commentQuery->fetch(PDO::FETCH_ASSOC);

/*
==================================
LISTE COMMENTAIRES
==================================
*/

$commentairesQuery = $conn->prepare("
    SELECT 
        commentaires.*,
        utilisateurs.nom,
        utilisateurs.photo
    FROM commentaires
    INNER JOIN utilisateurs
    ON commentaires.utilisateur_id = utilisateurs.id
    WHERE commentaires.memoire_id = ?
    ORDER BY commentaires.created_at DESC
");

$commentairesQuery->execute([$id]);

$listeCommentaires =
$commentairesQuery->fetchAll(PDO::FETCH_ASSOC);


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

    <title>Détail Mémoire</title>

    <!-- CSS -->

    <link
        rel="stylesheet"
        href="../../assets/css/detail_memoire.css"
    >

    <!-- FONT AWESOME -->

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

                <div>

                    <h1>
                        Détail d’un mémoire
                    </h1>

                </div>

            </div>

            <!-- RIGHT -->

            <div class="header-right">

                <div class="notif-icon">

                    <i class="fa-regular fa-bell"></i>

                    <span>3</span>

                </div>

                <!-- PROFILE -->

                <div class="profile-box">

                    <img
                        src="../../uploads/<?= $photoProfil ?>"
                        class="profile"
                    >

                    <div>

                        <h3>
                            <?= htmlspecialchars($professeur['nom']) ?>
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

            <!-- TITLE -->

            <h1 class="memoire-title">

                <?= htmlspecialchars($memoire['titre']) ?>

            </h1>

            <!-- META -->

            <div class="memoire-meta">

                <span>

                    Par :
                    <?= htmlspecialchars($memoire['nom']) ?>

                </span>

                <span>

                    Soumis le :
                    <?= date(
                        'd/m/Y à H:i',
                        strtotime($memoire['created_at'])
                    ) ?>

                </span>

                <span>

                    Catégorie :
                    <?= htmlspecialchars($memoire['categorie']) ?>

                </span>

            </div>

            <!-- ACTIONS -->

            <div class="memoire-actions">

                <!-- PDF -->

                <a
                    href="../../uploads/memoire/<?= urlencode($memoire['fichier']) ?>"
                    target="_blank"
                    class="btn-primary"
                >

                    <i class="fa-solid fa-download"></i>

                    Télécharger (PDF)

                </a>

                <!-- LIKES -->

                <button class="btn-light">

                    <i class="fa-regular fa-thumbs-up"></i>

                    <?= $likes['total'] ?>

                </button>

                <!-- COMMENTAIRES -->

                <button class="btn-light">

                    <i class="fa-regular fa-comment"></i>

                    <?= $commentaires['total'] ?>
                    Commentaires

                </button>

            </div>

            <!-- RESUME -->

            <div class="resume-box">

                <h2>
                    Résumé
                </h2>

                <p>

                    <?= nl2br(
                        htmlspecialchars($memoire['resume'])
                    ) ?>

                </p>

            </div>

            <!-- COMMENTAIRES -->

            <div class="comment-section">

                <h2>
                    Commentaires
                </h2>

                <?php
                if(count($listeCommentaires) > 0):
                ?>

                    <?php
                    foreach(
                        $listeCommentaires
                        as $commentaire
                    ):
                    ?>

                    <div class="comment-card">

                        <!-- PHOTO -->

                        <?php
                        if(!empty($commentaire['photo'])):
                        ?>

                            <img
                                src="../../uploads/autre/<?= $commentaire['photo'] ?>"
                                class="comment-avatar"
                            >

                        <?php else: ?>

                            <img
                                src="../../assets/images/prof.jpg"
                                class="comment-avatar"
                            >

                        <?php endif; ?>

                        <!-- CONTENT -->

                        <div class="comment-content">

                            <div class="comment-top">

                                <h3>

                                    <?= htmlspecialchars(
                                        $commentaire['nom']
                                    ) ?>

                                </h3>

                                <span>

                                    <?= date(
                                        'd/m/Y à H:i',
                                        strtotime(
                                            $commentaire['created_at']
                                        )
                                    ) ?>

                                </span>

                            </div>

                            <p>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $commentaire['commentaire']
                                    )
                                ) ?>

                            </p>

                        </div>

                    </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p
                        style="
                        margin-top:20px;
                        color:#64748b;
                        "
                    >

                        Aucun commentaire
                        pour ce mémoire.

                    </p>

                <?php endif; ?>

            </div>

        </section>

    </main>

</div>

</body>
</html>