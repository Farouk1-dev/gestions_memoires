<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if(
    !isset($_SESSION['id']) ||
    $_SESSION['role'] != 'admin'
){

    header("Location: ../../login.php");

    exit();
}

include("../../config/database.php");

require_once("../../models/Like.php");

$likeModel = new Like();

$user_id = $_SESSION['id'];

$memoireSelectionne = null;

/*
====================================
TOUS LES MEMOIRES
====================================
*/

$query = $conn->prepare("
    SELECT memoires.*, utilisateurs.nom
    FROM memoires
    INNER JOIN utilisateurs
    ON memoires.utilisateur_id = utilisateurs.id
    ORDER BY memoires.id DESC
");

$query->execute();

$memoires = $query->fetchAll();

/*
====================================
MEMOIRE SELECTIONNE
====================================
*/

if(isset($_GET['memoire'])){

    $memoire_id = $_GET['memoire'];

    $sql = $conn->prepare("
        SELECT memoires.*, utilisateurs.nom
        FROM memoires
        INNER JOIN utilisateurs
        ON memoires.utilisateur_id = utilisateurs.id
        WHERE memoires.id = ?
    ");

    $sql->execute([$memoire_id]);

    $memoireSelectionne = $sql->fetch();
}

/*
====================================
COMMENTAIRES
====================================
*/

$commentaires = [];

if($memoireSelectionne){

    $commentaireQuery = $conn->prepare("
        SELECT commentaires.*, utilisateurs.nom
        FROM commentaires
        INNER JOIN utilisateurs
        ON commentaires.utilisateur_id = utilisateurs.id
        WHERE commentaires.memoire_id = ?
        ORDER BY commentaires.id DESC
    ");

    $commentaireQuery->execute([
        $memoireSelectionne['id']
    ]);

    $commentaires = $commentaireQuery->fetchAll();
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Interactions</title>

<link rel="stylesheet"
href="../../assets/css/interactions.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

<div class="container">

<?php include("../../includes/admin_header.php"); ?>

<main class="main">

    <!-- HEADER -->

    <header class="topbar">

        <div class="topbar-left">

            <i class="fa-solid fa-bars"></i>

            <h1>Mémoires</h1>

        </div>

        <div class="search-box">

            <input type="text"
            placeholder="Rechercher un mémoire...">

            <i class="fa-solid fa-magnifying-glass"></i>

        </div>

        <div class="topbar-right">

            <div class="notif">

                <i class="fa-regular fa-bell"></i>

                <span>3</span>

            </div>

            <div class="profile">

                <img
                    src="../../uploads/<?=
                    isset($_SESSION['photo'])
                    ? $_SESSION['photo']
                    : 'default.png'
                    ?>">

                    <div>

                        <h3>

                            <?=
                            isset($_SESSION['nom'])
                            ? $_SESSION['nom']
                            : 'Utilisateur'
                            ?>

                        </h3>

                        <p>Administrateur</p>

                    </div>

            </div>

        </div>

    </header>

    <!-- CONTENT -->

    <div class="interaction-layout <?= $memoireSelectionne ? 'active' : '' ?>">

        <!-- LEFT -->

        <div class="left-panel">

            <div class="panel-header">

                <h2>Tous les mémoires</h2>

                <span>
                    <?= count($memoires) ?> mémoires
                </span>

            </div>

            <!-- FILTERS -->

                <div class="filters">

                    <!-- CATEGORIES -->

                    <select>

                        <option>
                            Toutes les catégories
                        </option>

                        <option>
                            Intelligence Artificielle
                        </option>

                        <option>
                            Génie Logiciel
                        </option>

                        <option>
                            Réseaux Informatiques
                        </option>

                        <option>
                            Base de données
                        </option>

                    </select>

                    <!-- TRI -->

                    <select>

                        <option>
                            Plus récents
                        </option>

                        <option>
                            Plus anciens
                        </option>

                        <option>
                            Plus likés
                        </option>

                        <option>
                            Plus commentés
                        </option>

                    </select>

                    <!-- BOUTON -->

                    <button class="filter-btn">

                        <i class="fa-solid fa-filter"></i>

                        Filtrer

                    </button>

                </div>

            <div class="memoire-list">

                <?php foreach($memoires as $memoire): ?>

                <?php

                /*
                ========================
                NOMBRE COMMENTAIRES
                ========================
                */

                $countComment = $conn->prepare("
                    SELECT COUNT(*) AS total
                    FROM commentaires
                    WHERE memoire_id = ?
                ");

                $countComment->execute([
                    $memoire['id']
                ]);

                $nbCommentaires =
                $countComment->fetch()['total'];

                /*
                ========================
                NOMBRE LIKES
                ========================
                */

                $countLike = $conn->prepare("
                    SELECT COUNT(*) AS total
                    FROM likes
                    WHERE memoire_id = ?
                ");

                $countLike->execute([
                    $memoire['id']
                ]);

                $nbLikes =
                $countLike->fetch()['total'];

                ?>

                <a href="?memoire=<?= $memoire['id'] ?>"
                class="memoire-card
                <?= isset($_GET['memoire']) &&
                $_GET['memoire'] == $memoire['id']
                ? 'selected' : '' ?>">

                    <div class="pdf-icon">

                        <i class="fa-solid fa-file-pdf"></i>

                    </div>

                    <div class="memoire-info">

                        <h3>
                            <?= htmlspecialchars($memoire['titre']) ?>
                        </h3>

                        <span class="categorie">

                            <?= htmlspecialchars($memoire['categorie']) ?>

                        </span>

                        <p>

                            Par
                            <?= htmlspecialchars($memoire['nom']) ?>

                        </p>

                        <div class="meta">

                            <span>

                                <i class="fa-regular fa-thumbs-up"></i>

                                <?= $nbLikes ?>

                            </span>

                            <span>

                                <i class="fa-regular fa-comment"></i>

                                <?= $nbCommentaires ?>

                            </span>

                        </div>

                    </div>

                </a>

                <?php endforeach; ?>

            </div>

        </div>

        <!-- RIGHT -->

        <?php if($memoireSelectionne): ?>

        <div class="right-panel">

            <!-- DETAILS -->

            <div class="memoire-details">

                <a href="interactions.php"
                class="back-link">

                    <i class="fa-solid fa-arrow-left"></i>

                    Retour à la liste

                </a>

                <div class="detail-content">

                    <div class="big-pdf">

                        <i class="fa-solid fa-file-pdf"></i>

                    </div>

                    <div class="detail-info">

                        <h2>

                            <?= htmlspecialchars(
                                $memoireSelectionne['titre']
                            ) ?>

                        </h2>

                        <p>

                            Catégorie :
                            <?= htmlspecialchars(
                                $memoireSelectionne['categorie']
                            ) ?>

                        </p>

                        <p>

                            Par
                            <?= htmlspecialchars(
                                $memoireSelectionne['nom']
                            ) ?>

                        </p>

                    </div>

                </div>

                <!-- ACTIONS -->

                <?php

                    $dejaLike = $likeModel->existe(
                        $memoireSelectionne['id'],
                        $user_id
                    );

                    ?>

                <div class="actions">

                    <a class="like-btn <?= $dejaLike ? 'liked' : '' ?>"
                    href="../../controllers/LikeController.php?memoire=<?= $memoireSelectionne['id'] ?>">

                        <i class="fa-solid fa-thumbs-up"></i>

                        <?= $dejaLike ? 'Deliker' : 'Liker' ?>

                        (<?= $likeModel->nombre($memoireSelectionne['id']) ?>)

                    </a>

                    <a href="../../uploads/memoire/<?= $memoireSelectionne['fichier'] ?>"
                    target="_blank"
                    class="view-btn">

                        <i class="fa-regular fa-eye"></i>

                        Voir le mémoire

                    </a>

                </div>

            </div>

            <!-- COMMENTAIRES -->

            <div class="comments-box">

                <h3>Commentaires</h3>

                <?php if(count($commentaires) > 0): ?>

                    <?php foreach($commentaires as $commentaire): ?>

                    <div class="comment">

                        <div class="avatar">

                            <?= strtoupper(
                                substr($commentaire['nom'],0,1)
                            ) ?>

                        </div>

                        <div class="comment-body">

                            <div class="comment-top">

                                <h4>

                                    <?= htmlspecialchars(
                                        $commentaire['nom']
                                    ) ?>

                                </h4>

                                <span>

                                    <?= date(
                                        'd/m/Y H:i',
                                        strtotime(
                                            $commentaire['created_at']
                                        )
                                    ) ?>

                                </span>

                            </div>

                            <p>

                                <?= htmlspecialchars(
                                    $commentaire['commentaire']
                                ) ?>

                            </p>

                        </div>

                    </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p>
                        Aucun commentaire.
                    </p>

                <?php endif; ?>

                <!-- FORM -->

                <form method="POST"
                action="../../controllers/CommentaireController.php"
                class="comment-form">

                    <input type="hidden"
                    name="memoire_id"
                    value="<?= $memoireSelectionne['id'] ?>">

                    <textarea
                    name="commentaire"
                    placeholder="Écrire votre commentaire..."
                    required></textarea>

                    <button type="submit">

                        <i class="fa-solid fa-paper-plane"></i>

                        Publier

                    </button>

                </form>

            </div>

        </div>

        <?php endif; ?>

    </div>

</main>

</div>

</body>
</html>