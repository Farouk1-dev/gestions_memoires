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
==============================
RECUPERATION ID MEMOIRE
==============================
*/

if(!isset($_GET['id'])){

    die("Mémoire introuvable");
}

$id = intval($_GET['id']);

/*
==============================
RECUPERATION MEMOIRE
==============================
*/

$query = $conn->query("
SELECT memoires.*, utilisateurs.nom
FROM memoires
INNER JOIN utilisateurs
ON memoires.utilisateur_id = utilisateurs.id
WHERE memoires.id = '$id'
");

$memoire = $query->fetch(PDO::FETCH_ASSOC);

if(!$memoire){

    die("Mémoire introuvable");
}

/*
==============================
NOMBRE COMMENTAIRES
==============================
*/

$commentaires = $conn->query("
SELECT COUNT(*) as total
FROM commentaires
WHERE memoire_id = '$id'
");

$totalCommentaires =
$commentaires->fetch(PDO::FETCH_ASSOC)['total'];

/*
==============================
NOMBRE LIKES
==============================
*/

$likes = $conn->query("
SELECT COUNT(*) as total
FROM likes
WHERE memoire_id = '$id'
");

$totalLikes =
$likes->fetch(PDO::FETCH_ASSOC)['total'];

/*
==============================
ENREGISTRER EVALUATION
==============================
*/

if(isset($_POST['evaluer'])){

    $commentaire = $_POST['commentaire'];
    $decision = $_POST['decision'];

    /*
    ==============================
    AJOUT COMMENTAIRE
    ==============================
    */

    $professeur_id = $_SESSION['user_id'];

    $insertCommentaire = $conn->prepare("
    INSERT INTO commentaires
    (
        memoire_id,
        utilisateur_id,
        commentaire
    )
    VALUES
    (?, ?, ?)
    ");

    $insertCommentaire->execute([
        $id,
        $professeur_id,
        $commentaire
    ]);

    /*
    ==============================
    CHANGER STATUT
    ==============================
    */

    $nouveauStatut = "en_attente";

    if($decision == "valider"){

        $nouveauStatut = "valide";
    }

    elseif($decision == "retourner"){

        $nouveauStatut = "retourne";
    }

    elseif($decision == "revision"){

        $nouveauStatut = "revision";
    }

    $update = $conn->prepare("
    UPDATE memoires
    SET statut = ?
    WHERE id = ?
    ");

    $update->execute([
        $nouveauStatut,
        $id
    ]);

    /*
    ==============================
    REDIRECTION
    ==============================
    */

    header("Location: liste_memoires.php");

    exit();
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

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Évaluer un mémoire</title>

<link rel="stylesheet"
href="../../assets/css/evaluer.css">

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

        <header class="header">

            <div class="header-left">

                <i class="fa-solid fa-bars"></i>

                <div class="header-title">

                    <h1>Évaluer un mémoire</h1>

                    <div class="breadcrumb">

                        <a href="liste_memoires.php">
                            Mémoires à évaluer
                        </a>

                        <span>›</span>

                        <span>
                            Évaluer un mémoire
                        </span>

                    </div>

                </div>

            </div>

            <!-- PROFILE -->

            <div class="profile-box">

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

            </div>

        </header>

        <!-- CONTENT -->

        <section class="content">

            <!-- CARD -->

            <div class="memo-card">

                <div class="memo-header">

                    <i class="fa-regular fa-file-lines"></i>

                    <h2>
                        <?= htmlspecialchars($memoire['titre']) ?>
                    </h2>

                </div>

                <!-- INFOS -->

                <div class="info-grid">

                    <div class="info-item">

                        <i class="fa-regular fa-user"></i>

                        <div>

                            <h4>Étudiant</h4>

                            <p>
                                <?= htmlspecialchars($memoire['nom']) ?>
                            </p>

                        </div>

                    </div>

                    <div class="info-item">

                        <i class="fa-regular fa-calendar"></i>

                        <div>

                            <h4>Date de soumission</h4>

                            <p>
                                <?= $memoire['created_at'] ?>
                            </p>

                        </div>

                    </div>

                    <div class="info-item">

                        <i class="fa-regular fa-folder"></i>

                        <div>

                            <h4>Catégorie</h4>

                            <p>
                                <?= htmlspecialchars($memoire['categorie']) ?>
                            </p>

                        </div>

                    </div>

                    <div class="info-item">

                        <i class="fa-regular fa-file-pdf"></i>

                        <div>

                            <h4>Fichier</h4>

                            <a target="_blank"
                            href="../../uploads/memoire/<?= $memoire['fichier'] ?>">

                                Télécharger le mémoire

                            </a>

                        </div>

                    </div>

                </div>

            </div>

            <!-- FORMULAIRE -->

            <form
                method="POST"
                action="../../controllers/EvaluationController.php">

                <input
                    type="hidden"
                    name="memoire_id"
                    value="<?= $memoire['id'] ?>">

                <!-- COMMENTAIRE -->

                <div class="bottom-grid">

                    <div class="comment-box">

                        <h2 class="section-title">
                            Commentaire général
                        </h2>

                        <textarea
                        name="commentaire"
                        required
                        placeholder="Ajouter un commentaire..."></textarea>

                    </div>

                    <!-- DECISION -->

                    <div class="decision-box">

                        <h2 class="section-title">
                            Décision
                        </h2>

                        <label class="radio-item">

                            <input
                            type="radio"
                            name="decision"
                            value="valider"
                            required>

                            Valider le mémoire

                        </label>

                        <label class="radio-item">

                            <input
                            type="radio"
                            name="decision"
                            value="retourner">

                            Retourner pour correction

                        </label>

                    </div>

                </div>

                <!-- BUTTONS -->

                <div class="actions">

                    <a href="liste_memoires.php"
                    class="btn btn-cancel">

                        Annuler

                    </a>

                    <button
                    type="submit"
                    name="evaluer"
                    class="btn btn-save">

                        <i class="fa-regular fa-floppy-disk"></i>

                        Enregistrer l’évaluation

                    </button>

                </div>

            </form>

        </section>

    </main>

</div>

</body>
</html>