<?php

session_start();

include("../config/database.php");

/*
=========================================
FORMULAIRE
=========================================
*/

if(isset($_POST['forgot_password'])){

    /*
    =========================================
    EMAIL
    =========================================
    */

    $email = trim($_POST['email']);

    /*
    =========================================
    VALIDATION
    =========================================
    */

    if(empty($email)){

        $_SESSION['error'] =
        "Veuillez entrer votre email.";

        header(
        "Location: ../views/auth/forgot_password.php"
        );

        exit();
    }

    /*
    =========================================
    VERIFIER EMAIL
    =========================================
    */

    $query = $conn->prepare("
    SELECT *
    FROM utilisateurs
    WHERE email = ?
    ");

    $query->execute([$email]);

    /*
    =========================================
    EMAIL INTROUVABLE
    =========================================
    */

    if($query->rowCount() == 0){

        $_SESSION['error'] =
        "Aucun compte trouvé avec cet email.";

        header(
        "Location: ../views/auth/forgot_password.php"
        );

        exit();
    }

    /*
    =========================================
    UTILISATEUR
    =========================================
    */

    $user = $query->fetch(PDO::FETCH_ASSOC);

    /*
    =========================================
    SUJET
    =========================================
    */

    $sujet =
    "Mot de passe oublié";

    /*
    =========================================
    MESSAGE
    =========================================
    */

    $message =

    "Bonjour administrateur,

    L'utilisateur ".$user['nom']."

    a oublié son mot de passe.

    Email du compte :
    ".$user['email']."

    Merci de l'aider à récupérer son compte.";

    /*
    =========================================
    INSERTION
    =========================================
    */

    $insert = $conn->prepare("

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

    $insert->execute([

        $user['nom'],

        $user['email'],

        $sujet,

        $message

    ]);

    /*
    =========================================
    SUCCES
    =========================================
    */

    $_SESSION['success'] =

    "Votre demande a été envoyée à l'administrateur.";

    header(
    "Location: ../views/auth/forgot_password.php"
    );

    exit();
}
?>