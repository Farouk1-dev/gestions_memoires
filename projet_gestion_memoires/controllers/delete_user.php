<?php

session_start();

include("../config/database.php");

/*
=========================================
SECURITE ADMIN
=========================================
*/

if(
    !isset($_SESSION['id']) ||
    $_SESSION['role'] != 'admin'
){
    header("Location: ../login.php");
    exit();
}

/*
=========================================
VERIFIER ID
=========================================
*/

if(isset($_GET['id'])){

    $id = $_GET['id'];

    /*
    =====================================
    SUPPRIMER COMMENTAIRES UTILISATEUR
    =====================================
    */

    $deleteCommentaires = $conn->prepare("

    DELETE FROM commentaires
    WHERE utilisateur_id = ?

    ");

    $deleteCommentaires->execute([$id]);

    /*
    =====================================
    SUPPRIMER LIKES UTILISATEUR
    =====================================
    */

    $deleteLikes = $conn->prepare("

    DELETE FROM likes
    WHERE utilisateur_id = ?

    ");

    $deleteLikes->execute([$id]);

    /*
    =====================================
    SUPPRIMER NOTIFICATIONS
    =====================================
    */

    $deleteNotifications = $conn->prepare("

    DELETE FROM notifications
    WHERE utilisateur_id = ?

    ");

    $deleteNotifications->execute([$id]);

    /*
    =====================================
    SUPPRIMER SOUMISSIONS
    =====================================
    */

    $deleteSoumissions = $conn->prepare("

    DELETE FROM soumissions
    WHERE etudiant_id = ?
    OR professeur_id = ?

    ");

    $deleteSoumissions->execute([$id, $id]);

    /*
    =====================================
    RECUPERER MEMOIRES
    =====================================
    */

    $getMemoires = $conn->prepare("

    SELECT id
    FROM memoires
    WHERE utilisateur_id = ?

    ");

    $getMemoires->execute([$id]);

    $memoires = $getMemoires->fetchAll(PDO::FETCH_ASSOC);

    /*
    =====================================
    SUPPRIMER DONNEES DES MEMOIRES
    =====================================
    */

    foreach($memoires as $memoire){

        $memoire_id = $memoire['id'];

        /*
        ==============================
        COMMENTAIRES MEMOIRE
        ==============================
        */

        $deleteCommentairesMemoire = $conn->prepare("

        DELETE FROM commentaires
        WHERE memoire_id = ?

        ");

        $deleteCommentairesMemoire
        ->execute([$memoire_id]);

        /*
        ==============================
        LIKES MEMOIRE
        ==============================
        */

        $deleteLikesMemoire = $conn->prepare("

        DELETE FROM likes
        WHERE memoire_id = ?

        ");

        $deleteLikesMemoire
        ->execute([$memoire_id]);

        /*
        ==============================
        SOUMISSIONS MEMOIRE
        ==============================
        */

        $deleteSoumissionMemoire = $conn->prepare("

        DELETE FROM soumissions
        WHERE memoire_id = ?

        ");

        $deleteSoumissionMemoire
        ->execute([$memoire_id]);
    }

    /*
    =====================================
    SUPPRIMER MEMOIRES
    =====================================
    */

    $deleteMemoires = $conn->prepare("

    DELETE FROM memoires
    WHERE utilisateur_id = ?

    ");

    $deleteMemoires->execute([$id]);

    /*
    =====================================
    SUPPRIMER UTILISATEUR
    =====================================
    */

    $deleteUser = $conn->prepare("

    DELETE FROM utilisateurs
    WHERE id = ?

    ");

    $deleteUser->execute([$id]);

    /*
    =====================================
    REDIRECTION
    =====================================
    */

    header(
        "Location: ../views/admin/users.php?success=delete"
    );

    exit();
}

/*
=========================================
SI PAS D'ID
=========================================
*/

header("Location: ../views/admin/users.php");

exit();

?>