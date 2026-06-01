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

require_once("../models/Like.php");

$like = new Like();

/*
=========================================
UTILISATEUR CONNECTÉ
=========================================
*/

$utilisateur_id = $_SESSION['id'];

/*
=========================================
LIKE / DELIKE
=========================================
*/

if(isset($_GET['memoire'])){

    $memoire_id = $_GET['memoire'];

    /*
    =========================================
    SI DEJA LIKE
    =========================================
    */

    if(
        $like->existe(
            $memoire_id,
            $utilisateur_id
        )
    ){

        /*
        ==============================
        SUPPRIMER LIKE
        ==============================
        */

        $like->supprimer(

            $memoire_id,

            $utilisateur_id

        );

    }

    else{

        /*
        ==============================
        AJOUTER LIKE
        ==============================
        */

        $like->ajouter(

            $memoire_id,

            $utilisateur_id

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