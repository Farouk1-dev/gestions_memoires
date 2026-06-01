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

<title>
Uploader plusieurs mémoires
</title>

<link
rel="stylesheet"
href="../../assets/css/uploader_multiple.css">

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
                    Uploader plusieurs mémoires
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

                Uploader plusieurs mémoires

            </h2>

            <p class="page-description">

                Sélectionnez plusieurs fichiers
                PDF pour les uploader.

            </p>

            <!-- MESSAGE -->

            <?php if(!empty($message)): ?>

                <div class="alert-box">

                    <?= $message ?>

                </div>

            <?php endif; ?>

            <!-- FORM -->

            <form
            action="../../controllers/MemoireController.php"
            method="POST"
            enctype="multipart/form-data">

                <!-- UPLOAD BOX -->

                <div class="upload-box">

                    <i class="fa-solid fa-cloud-arrow-up"></i>

                    <h3>
                        Glissez-déposez vos fichiers ici
                    </h3>

                    <p>ou</p>

                    <!-- BUTTON -->

                    <button
                    type="button"
                    id="browseBtn">

                        <i class="fa-regular fa-folder-open"></i>

                        Parcourir les fichiers

                    </button>

                    <!-- INPUT -->

                    <input
                    type="file"
                    id="fileInput"
                    name="fichiers[]"
                    accept=".pdf"
                    multiple
                    hidden>

                    <!-- INFO -->

                    <div class="upload-info">

                        Formats acceptés :
                        <span>PDF</span>

                        |

                        Taille max :
                        <span>20 Mo</span>

                        |

                        Maximum :
                        <span>10 fichiers</span>

                    </div>

                </div>

                <!-- FILES -->

                <div class="files-section">

                    <h3
                    class="section-title"
                    id="fileCount">

                        Fichiers sélectionnés (0)

                    </h3>

                    <!-- TABLE -->

                    <div class="table-box">

                        <!-- HEAD -->

                        <div class="table-head">

                            <div>Fichier</div>
                            <div>Taille</div>
                            <div>Statut</div>
                            <div>Action</div>

                        </div>

                        <!-- BODY -->

                        <div id="fileList"></div>

                    </div>

                </div>

                <!-- INFO -->

                <div class="info-card">

                    <i class="fa-solid fa-circle-info"></i>

                    <div>

                        <h4>
                            Information
                        </h4>

                        <p>
                            Vous pouvez supprimer un fichier
                            avant l’upload.
                        </p>

                    </div>

                </div>

                <!-- ACTIONS -->

                <div class="actions">

                    <button
                    type="reset"
                    class="cancel-btn">

                        Annuler

                    </button>

                    <button
                    type="submit"
                    name="upload_multiple"
                    class="upload-btn"
                    id="uploadBtn">

                        <i class="fa-solid fa-upload"></i>

                        Uploader (0)

                    </button>

                </div>

            </form>

        </section>

    </main>

</div>

<!-- ================= JAVASCRIPT ================= -->

<script>

const browseBtn =
document.getElementById("browseBtn");

const fileInput =
document.getElementById("fileInput");

const fileList =
document.getElementById("fileList");

const fileCount =
document.getElementById("fileCount");

const uploadBtn =
document.getElementById("uploadBtn");

/* ================= CLICK ================= */

browseBtn.addEventListener(
    "click",
    () => {

        fileInput.click();
    }
);

/* ================= FILES ================= */

fileInput.addEventListener(
    "change",
    updateFiles
);

function updateFiles(){

    fileList.innerHTML = "";

    const fichiers =
    Array.from(fileInput.files);

    fileCount.innerHTML =
    `Fichiers sélectionnés (${fichiers.length})`;

    uploadBtn.innerHTML =
    `<i class="fa-solid fa-upload"></i>
    Uploader (${fichiers.length})`;

    fichiers.forEach((file,index)=>{

        const taille =
        (file.size / (1024 * 1024))
        .toFixed(2);

        const item =
        document.createElement("div");

        item.classList.add("file-item");

        item.innerHTML = `

            <div class="file-info">

                <div class="pdf-icon">

                    <i class="fa-solid fa-file-pdf"></i>

                </div>

                <div>

                    <h4>${file.name}</h4>

                    <p>PDF Document</p>

                </div>

            </div>

            <div class="file-size">

                ${taille} Mo

            </div>

            <div class="status ready">

                <i class="fa-solid fa-circle-check"></i>

                Prêt

            </div>

            <button
            type="button"
            class="delete-btn"
            onclick="removeFile(${index})">

                <i class="fa-regular fa-trash-can"></i>

            </button>
        `;

        fileList.appendChild(item);
    });
}

/* ================= REMOVE ================= */

function removeFile(index){

    const dt = new DataTransfer();

    const fichiers =
    Array.from(fileInput.files);

    fichiers.forEach((file,i)=>{

        if(i !== index){

            dt.items.add(file);
        }
    });

    fileInput.files = dt.files;

    updateFiles();
}

</script>

</body>

</html>