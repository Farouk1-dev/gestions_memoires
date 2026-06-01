<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if(
    !isset($_SESSION['id']) ||
    $_SESSION['role'] != 'professeur'
){

    header("Location: ../login.php");

    exit();
}

include("../config/database.php");

/*
===================================
VERIFIER FORMULAIRE
===================================
*/

if(isset($_POST['evaluer'])){

    /*
    ===================================
    RECUPERATION DONNEES
    ===================================
    */

    $memoire_id = $_POST['memoire_id'];

    $professeur_id = $_SESSION['id'];

    $commentaire = $_POST['commentaire'];

    $decision = $_POST['decision'];

    /*
    ===================================
    CHANGER STATUT
    ===================================
    */

    $statut = "en_attente";

    if($decision == "valider"){

        $statut = "valide";
    }

    elseif($decision == "retourner"){

        $statut = "retour_pour_correction";
    }

    elseif($decision == "revision"){

        $statut = "revision";
    }

    /*
    ===================================
    RECUPERER ETUDIANT
    ===================================
    */

    $query = $conn->prepare("
    SELECT utilisateur_id
    FROM memoires
    WHERE id = ?
    ");

    $query->execute([$memoire_id]);

    $memoire = $query->fetch(PDO::FETCH_ASSOC);

    $etudiant_id = $memoire['utilisateur_id'];

    /*
    ===================================
    AJOUTER COMMENTAIRE
    ===================================
    */

    $comment = $conn->prepare("
    INSERT INTO commentaires
    (
        memoire_id,
        utilisateur_id,
        commentaire
    )
    VALUES
    (?, ?, ?)
    ");

    $comment->execute([
        $memoire_id,
        $professeur_id,
        $commentaire
    ]);
    
    /*
    =========================================
    MODIFIER LA SOUMISSION
    =========================================
    */

    $update = $conn->prepare("
    UPDATE soumissions
    SET

        commentaire = ?,
        statut = ?

    WHERE memoire_id = ?
    ");

    $update->execute([

        $commentaire,

        $statut,

        $memoire_id

    ]);

    /*
    ===================================
    NOTIFICATION ETUDIANT
    ===================================
    */

    $titre = "Mémoire évalué";

    $message =
    "Votre mémoire a été évalué par le professeur.";

    $notif = $conn->prepare("
    INSERT INTO notifications
    (
        utilisateur_id,
        titre,
        message
    )
    VALUES
    (?, ?, ?)
    ");

    $notif->execute([
        $etudiant_id,
        $titre,
        $message
    ]);

    /*
    ===================================
    REDIRECTION
    ===================================
    */

    header("Location: ../views/professeur/liste_memoires.php?success=1");
        exit();
    }
?>