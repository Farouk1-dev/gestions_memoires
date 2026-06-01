<?php

session_start();


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

/* ================= MESSAGE SESSION ================= */

$message = "";

if(isset($_SESSION['message'])){

    $message = $_SESSION['message'];

    unset($_SESSION['message']);
}

?>

<?php

/*
====================================
PHOTO UTILISATEUR
====================================
*/

include("../../config/database.php");

$user_id = $_SESSION['id'];

$userQuery = $conn->prepare("
SELECT photo
FROM utilisateurs
WHERE id = ?
");

$userQuery->execute([$user_id]);

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

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Uploader un mémoire</title>

<link
rel="stylesheet"
href="../../assets/css/uploader.css">

<link
rel="stylesheet"
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

                <h1>
                    Uploader un mémoire
                </h1>

            </div>

            <div class="top-right">

                <div class="notif">

                    <i class="fa-regular fa-bell"></i>

                    <span>3</span>

                </div>

                <div class="profile">

                   <img
                        src="../../uploads/<?= $photoProfil ?>">

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

        </header>

        <!-- CONTENT -->

        <section class="content">

            <!-- TITLE -->

            <h2 class="page-title">
                Uploader un mémoire
            </h2>

            <!-- MESSAGE -->

            <?php if(!empty($message)): ?>

                <div class="alert-box">

                    <?= $message ?>

                </div>

            <?php endif; ?>

            <!-- INFO -->

            <div class="info-box">

                <div class="info-icon">

                    <i class="fa-solid fa-circle-info"></i>

                </div>

                <div>

                    <h3>
                        Informations
                    </h3>

                    <p>
                        Veuillez remplir les informations
                        ci-dessous puis sélectionner
                        votre mémoire PDF.
                    </p>

                </div>

            </div>

            <!-- FORM -->

            <form
            action="../../controllers/MemoireController.php"
            method="POST"
            enctype="multipart/form-data">

                <div class="form-box">

                    <!-- TITRE -->

                    <div class="form-group">

                        <label>
                            Titre du mémoire *
                        </label>

                        <input
                        type="text"
                        name="titre"
                        placeholder="Entrez le titre du mémoire"
                        required>

                    </div>

                    <!-- CATEGORIE -->

                    <div class="form-group">

                        <label>
                            Catégorie *
                        </label>

                        <select
                        name="categorie"
                        required>

                            <option value="">
                                Sélectionnez une catégorie
                            </option>

                            <option value="Genie Logiciel">
                                Génie Logiciel
                            </option>

                            <option value="Reseaux Informatiques">
                                Réseaux Informatiques
                            </option>

                            <option value="Intelligence Artificielle">
                                Intelligence Artificielle
                            </option>

                            <option value="Cybersécurité">
                                Cybersécurité
                            </option>

                        </select>

                    </div>

                    <!-- RESUME -->

                    <div class="form-group">

                        <label>
                            Résumé *
                        </label>

                        <textarea
                        name="resume"
                        maxlength="500"
                        placeholder="Entrez un résumé du mémoire"
                        required></textarea>

                        <span class="counter">
                            0/500
                        </span>

                    </div>

                    <!-- UPLOAD -->

                    <div class="upload-section">

                        <label>
                            Fichier du mémoire *
                        </label>

                        <div class="upload-box">

                            <i class="fa-solid fa-cloud-arrow-up"></i>

                            <h3>
                                Glissez-déposez votre fichier ici
                            </h3>

                            <p>
                                ou
                            </p>

                            <!-- INPUT CACHE -->

                                <input
                                type="file"
                                name="fichier"
                                accept=".pdf"
                                id="fileInput"
                                hidden
                                required>

                                <!-- BUTTON -->

                                <button
                                type="button"
                                id="browseBtn">

                                    Parcourir les fichiers

                                </button>

                                <!-- FILE NAME -->

                                <p id="fileName">

                                    Aucun fichier sélectionné

                                </p>

                            <div class="upload-info">

                                Formats acceptés :
                                <span>PDF</span>

                                |

                                Taille maximale :
                                <span>20 Mo</span>

                            </div>

                        </div>

                    </div>

                    <!-- ACTIONS -->

                    <div class="actions">

                        <!-- RESET -->

                        <button
                        type="reset"
                        class="cancel-btn">

                            Annuler

                        </button>

                        <!-- SUBMIT -->

                        <button
                        type="submit"
                        name="upload"
                        class="submit-btn">

                            <i class="fa-solid fa-upload"></i>

                            Uploader

                        </button>

                    </div>

                </div>

            </form>

        </section>

    </main>

</div>

<script>

const browseBtn =
document.getElementById("browseBtn");

const fileInput =
document.getElementById("fileInput");

const fileName =
document.getElementById("fileName");

/* ================= CLICK ================= */

browseBtn.addEventListener(
    "click",
    () => {

        fileInput.click();
    }
);

/* ================= CHANGE ================= */

fileInput.addEventListener(
    "change",
    () => {

        if(fileInput.files.length > 0){

            fileName.innerHTML =
            fileInput.files[0].name;

        }else{

            fileName.innerHTML =
            "Aucun fichier sélectionné";
        }
    }
);

</script>

</body>

</html>