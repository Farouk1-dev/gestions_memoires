<?php

session_start();

include("../../config/database.php");

include("../../models/Memoire.php");

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

/* ================= PROFS ================= */

$req = $conn->query("
    SELECT *
    FROM utilisateurs
    WHERE role = 'professeur'
");

$professeurs =
$req->fetchAll(PDO::FETCH_ASSOC);

/* ================= MODEL ================= */

$memoireModel =
new Memoire($conn);

/* ================= MEMOIRES ================= */

$memoires =
$memoireModel
->getMemoiresByUtilisateur(
    $utilisateur_id
);


$message = "";

if(isset($_SESSION['message'])){

    $message =
    $_SESSION['message'];

    unset($_SESSION['message']);
}

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

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
Soumettre un mémoire
</title>

<link
rel="stylesheet"
href="../../assets/css/soumettre.css">

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
                    Soumettre un mémoire
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

                            <?= htmlspecialchars($_SESSION['nom']) ?>

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
            <form
                id="soumissionForm"
                action="../../controllers/SoumissionController.php"
                method="POST"
                autocomplete="off">

            <h2 class="page-title">

                Soumettre un mémoire

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
                        Sélectionnez un mémoire
                        pour voir ses détails.
                    </p>

                </div>

            </div>

            <!-- FORM BOX -->

            <div class="form-box">

                <h3 class="box-title">

                    Sélectionner un mémoire

                </h3>

                <!-- MEMOIRE -->

                <div class="form-group">

                    <label>
                        Mémoire *
                    </label>

                    <select
                        id="memoireSelect"
                        name="memoire_id">

                        <?php foreach(
                            $memoires as $memoire
                        ): ?>

                            <option

                            value="<?= $memoire['id'] ?>"

                            data-titre="<?= htmlspecialchars($memoire['titre']) ?>"

                            data-categorie="<?= htmlspecialchars($memoire['categorie']) ?>"

                            data-resume="<?= htmlspecialchars($memoire['resume']) ?>"

                            data-date="<?= date(
                                'd/m/Y',
                                strtotime(
                                    $memoire['created_at']
                                )
                            ) ?>"

                            data-fichier="<?= $memoire['fichier'] ?>">

                                <?= $memoire['titre'] ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- PROF -->

                <div class="form-group">

                    <label>
                        Professeur *
                    </label>

                    <select name="professeur_id">

                        <?php foreach(
                            $professeurs as $prof
                        ): ?>

                            <option
                            value="<?= $prof['id'] ?>">

                                <?= $prof['nom'] ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

            </div>

            <!-- PDF -->

            <div class="pdf-box">

                <h3 class="box-title">

                    Résumé du mémoire

                </h3>

                <div class="pdf-content">

                    <div class="pdf-left">

                        <div class="pdf-icon">

                            <i class="fa-solid fa-file-pdf"></i>

                        </div>

                        <div>

                            <h4 id="titreMemoire">

                                -

                            </h4>

                            <p id="categorieMemoire">

                                -

                            </p>

                            <p id="dateMemoire">

                                -

                            </p>

                        </div>

                    </div>

                    <!-- VIEW -->

                    <button
                    type="button"
                    class="view-btn"
                    id="viewBtn">

                        <i class="fa-regular fa-eye"></i>

                        Voir le mémoire

                    </button>

                </div>

            </div>

            <!-- MESSAGE -->

            <div class="message-box">

                <h3 class="box-title">

                    Message au professeur

                </h3>

                <textarea
                    name="message"
                    placeholder="Entrez un message..."></textarea>

            </div>

            <!-- IMPORTANT -->

                <div class="important-box">

                    <i class="fa-solid fa-shield-halved"></i>

                    <div>

                        <h4>
                            Important
                        </h4>

                        <p>
                            Une fois soumis,
                            votre professeur recevra
                            une notification.
                        </p>

                    </div>

                </div>

                <!-- ACTIONS -->

                <div class="actions">

                    <!-- CANCEL -->

                    <button
                    type="reset"
                    class="cancel-btn">

                        Annuler

                    </button>

                    <!-- SUBMIT -->

                    <button
                        type="submit"
                        name="submit"
                        class="submit-btn">

                        <i class="fa-solid fa-paper-plane"></i>

                        Soumettre

                    </button>

                </div>
            </form>
        </section>

    </main>

</div>

<!-- ================= MODAL PDF ================= -->

<div
id="pdfModal"
style="
display:none;
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.8);
z-index:9999;
">

    <div
    style="
    width:90%;
    height:90%;
    margin:2% auto;
    background:white;
    position:relative;
    border-radius:10px;
    overflow:hidden;
    ">

        <!-- CLOSE -->

        <button
        id="closeModal"
        style="
        position:absolute;
        top:10px;
        right:10px;
        width:40px;
        height:40px;
        border:none;
        border-radius:50%;
        background:red;
        color:white;
        font-size:20px;
        cursor:pointer;
        z-index:10;
        ">

            X

        </button>

        <!-- PDF -->

        <iframe
        id="pdfFrame"
        width="100%"
        height="100%"
        style="border:none;"></iframe>

    </div>

</div>

<!-- ================= JS ================= -->

<script>

const memoireSelect =
document.getElementById(
    "memoireSelect"
);

const titreMemoire =
document.getElementById(
    "titreMemoire"
);

const categorieMemoire =
document.getElementById(
    "categorieMemoire"
);

const dateMemoire =
document.getElementById(
    "dateMemoire"
);

const viewBtn =
document.getElementById(
    "viewBtn"
);

const pdfModal =
document.getElementById(
    "pdfModal"
);

const pdfFrame =
document.getElementById(
    "pdfFrame"
);

const closeModal =
document.getElementById(
    "closeModal"
);

/* ================= UPDATE ================= */

function updateMemoire(){

    const option =
    memoireSelect.options[
        memoireSelect.selectedIndex
    ];

    titreMemoire.innerHTML =
    option.dataset.titre;

    categorieMemoire.innerHTML =
    "Catégorie : "
    + option.dataset.categorie;

    dateMemoire.innerHTML =
    "Date d’upload : "
    + option.dataset.date;

    /* ================= PDF ================= */

    viewBtn.onclick = () => {

        pdfModal.style.display =
        "block";

        pdfFrame.src =
        "../../uploads/memoire/"
        + option.dataset.fichier;
    };
}

/* ================= INIT ================= */

updateMemoire();

/* ================= CHANGE ================= */

memoireSelect.addEventListener(
    "change",
    updateMemoire
);

/* ================= CLOSE ================= */

closeModal.addEventListener(
    "click",
    () => {

        pdfModal.style.display =
        "none";

        pdfFrame.src = "";
    }
);

</script>

<script>
/* ================= INIT ================= */

updateMemoire();

/* ================= CLEAR MESSAGE ================= */

document.querySelector(
    'textarea[name="message"]'
).value = "";

</script>

</body>

</html>