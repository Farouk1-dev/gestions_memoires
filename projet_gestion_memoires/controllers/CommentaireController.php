<?php

session_start();

/*
=========================================
SECURITE
=========================================
*/

if(
    !isset($_SESSION['id']) ||

    (
        $_SESSION['role'] != 'etudiant'
        &&
        $_SESSION['role'] != 'professeur'
        &&
        $_SESSION['role'] != 'admin'
    )
){
    header("Location: ../../login.php");
    exit();
}

require_once("../models/Commentaire.php");

$commentaireModel = new Commentaire();

/*
=========================================
UTILISATEUR CONNECTÉ
=========================================
*/

$utilisateur_id = $_SESSION['id'];

/*
=========================================
AJOUT COMMENTAIRE
=========================================
*/

if(
    isset($_POST['memoire_id']) &&
    isset($_POST['commentaire'])
){

    $memoire_id = $_POST['memoire_id'];

    $commentaire =
    trim($_POST['commentaire']);

    /*
    =========================================
    VERIFICATION
    =========================================
    */

    if(!empty($commentaire)){

        $commentaireModel->ajouter(

            $memoire_id,

            $utilisateur_id,

            $commentaire

        );
    }

    /*
    =========================================
    REDIRECTION
    =========================================
    */

        if($_SESSION['role'] == 'etudiant'){

        header(
            "Location: ../views/etudiant/interactions.php?memoire="
            .$memoire_id
        );

    }

    elseif($_SESSION['role'] == 'professeur'){

        header(
            "Location: ../views/professeur/interactions.php?memoire="
            .$memoire_id
        );

    }

    elseif($_SESSION['role'] == 'admin'){

        header(
            "Location: ../views/admin/interactions.php?memoire="
            .$memoire_id
        );

    }

    exit();
}
?>