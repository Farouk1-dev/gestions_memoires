<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

/*
=========================================
SECURITE ETUDIANT
=========================================
*/

if(
    !isset($_SESSION['id']) ||
    $_SESSION['role'] != 'etudiant'
){

    header("Location: ../login.php");

    exit();
}

include("../config/database.php");

include("../models/Memoire.php");

class MemoireController{

    private $memoireModel;

    public function __construct($conn){

        $this->memoireModel =
        new Memoire($conn);
    }

    /* ================= UPLOAD SIMPLE ================= */

    public function uploader(){

       $utilisateur_id = $_SESSION['id'];

        $titre =
        htmlspecialchars(
            trim($_POST['titre'])
        );

        $categorie =
        htmlspecialchars(
            trim($_POST['categorie'])
        );

        $resume =
        htmlspecialchars(
            trim($_POST['resume'])
        );

        /* ================= FILE ================= */

        $fichier = $_FILES['fichier'];

        $nom =
        $fichier['name'];

        $tmp =
        $fichier['tmp_name'];

        $taille =
        $fichier['size'];

        $extension = strtolower(
            pathinfo(
                $nom,
                PATHINFO_EXTENSION
            )
        );

        /* ================= VALIDATION ================= */

        if(
            empty($titre)
            ||
            empty($categorie)
            ||
            empty($resume)
        ){

            $_SESSION['message'] =
            "Veuillez remplir tous les champs.";

            header(
                "Location: ../views/etudiant/uploader.php"
            );

            exit;
        }

        if($extension != "pdf"){

            $_SESSION['message'] =
            "Seuls les PDF sont autorisés.";

            header(
                "Location: ../views/etudiant/uploader.php"
            );

            exit;
        }

        if($taille > 20 * 1024 * 1024){

            $_SESSION['message'] =
            "Le fichier dépasse 20 Mo.";

            header(
                "Location: ../views/etudiant/uploader.php"
            );

            exit;
        }

        /* ================= DOSSIER ================= */

        $dossier =
        "../uploads/memoire/";

        if(!is_dir($dossier)){

            mkdir(
                $dossier,
                0777,
                true
            );
        }

        /* ================= NEW NAME ================= */

        $nouveauNom =
            time()
            . "_"
            . uniqid()
            . ".pdf";

        /* ================= MOVE ================= */

        move_uploaded_file(
            $tmp,
            $dossier . $nouveauNom
        );

        /* ================= INSERT ================= */

        $this->memoireModel
        ->ajouter(
            $utilisateur_id,
            $titre,
            $categorie,
            $resume,
            $nouveauNom
        );

        $_SESSION['message'] =
        "Mémoire uploadé avec succès.";

        header(
            "Location: ../views/etudiant/uploader.php"
        );

        exit;
    }

    /* ================= UPLOAD MULTIPLE ================= */

    public function uploaderMultiple(){

        $utilisateur_id = 6;

        /* ================= FILES ================= */

        $fichiers =
        $_FILES['fichiers'];

        $nombre =
        count($fichiers['name']);

        /* ================= VALIDATION ================= */

        if($nombre <= 0){

            $_SESSION['message'] =
            "Aucun fichier sélectionné.";

            header(
                "Location: ../views/etudiant/uploader_multiple.php"
            );

            exit;
        }

        if($nombre > 10){

            $_SESSION['message'] =
            "Maximum 10 fichiers.";

            header(
                "Location: ../views/etudiant/uploader_multiple.php"
            );

            exit;
        }

        /* ================= DOSSIER ================= */

        $dossier =
        "../uploads/memoire/";

        if(!is_dir($dossier)){

            mkdir(
                $dossier,
                0777,
                true
            );
        }

        $uploades = 0;

        /* ================= LOOP ================= */

        for($i = 0; $i < $nombre; $i++){

            $nom =
            $fichiers['name'][$i];

            $tmp =
            $fichiers['tmp_name'][$i];

            $taille =
            $fichiers['size'][$i];

            $extension = strtolower(
                pathinfo(
                    $nom,
                    PATHINFO_EXTENSION
                )
            );

            /* ================= VALIDATION ================= */

            if($extension != "pdf"){

                continue;
            }

            if($taille > 20 * 1024 * 1024){

                continue;
            }

            /* ================= NEW NAME ================= */

            $nouveauNom =
                time()
                . "_"
                . uniqid()
                . ".pdf";

            /* ================= MOVE ================= */

            $upload = move_uploaded_file(
                $tmp,
                $dossier . $nouveauNom
            );

            if($upload){

                /* ================= TITRE AUTO ================= */

                $titre =
                pathinfo(
                    $nom,
                    PATHINFO_FILENAME
                );

                /* ================= INSERT ================= */

                $this->memoireModel
                ->ajouter(
                    $utilisateur_id,
                    $titre,
                    "Non classé",
                    "Upload multiple",
                    $nouveauNom
                );

                $uploades++;
            }
        }

        $_SESSION['message'] =
        $uploades .
        " mémoire(s) uploadé(s) avec succès.";

        header(
            "Location: ../views/etudiant/uploader_multiple.php"
        );

        exit;
    }
}

/* ================= EXECUTION ================= */

$controller =
new MemoireController($conn);

/* ================= SIMPLE ================= */

if(isset($_POST['upload'])){

    $controller->uploader();
}

/* ================= MULTIPLE ================= */

if(isset($_POST['upload_multiple'])){

    $controller->uploaderMultiple();
}