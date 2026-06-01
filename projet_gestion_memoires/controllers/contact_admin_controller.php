<?php

session_start();

include("../config/database.php");

/*
====================================
VERIFIER FORMULAIRE
====================================
*/

if(isset($_POST['send_message'])){

    /*
    ====================================
    RECUPERATION DONNEES
    ====================================
    */

    $nom =
    trim($_POST['nom']);

    $email =
    trim($_POST['email']);

    $sujet =
    trim($_POST['sujet']);

    $message =
    trim($_POST['message']);
    /*
    ====================================
    VALIDATION
    ====================================
    */

    if(
        empty($nom)
        ||
        empty($email)
        ||
        empty($sujet)
        ||
        empty($message)
    ){

        $_SESSION['error'] =
        "Veuillez remplir tous les champs.";

        header(
        "Location: ../views/auth/contact_admin.php"
        );

        exit();
    }

    /*
    ====================================
    INSERTION
    ====================================
    */

    $sql = $conn->prepare("
    INSERT INTO contact_admin
    (
        nom,
        email,
        sujet,
        message
    )
    VALUES
    (?, ?, ?, ?)
    ");

    $sql->execute([

        $nom,
        $email,
        $sujet,
        $message

    ]);

    /*
    ====================================
    SUCCES
    ====================================
    */

    $_SESSION['success'] =
    "Message envoyé avec succès.";

    header(
    "Location: ../views/auth/contact_admin.php"
    );

    exit();
}
?>