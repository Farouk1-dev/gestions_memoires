<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

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

include("../config/database.php");

if(isset($_POST['create_user'])){

    /*
    =========================================
    RECUPERATION DES DONNEES
    =========================================
    */

    $nom =
    trim($_POST['nom']);

    $email =
    trim($_POST['email']);

    $telephone =
    trim($_POST['telephone']);

    $password =
    $_POST['password'];

    $role =
    $_POST['role'];

    $filiere =
    $_POST['filiere'] ?? NULL;

    $departement =
    $_POST['departement'] ?? NULL;

    $matricule =
    $_POST['matricule'] ?? NULL;

    /*
    =========================================
    VALIDATION
    =========================================
    */

    if(
        empty($nom)
        ||
        empty($email)
        ||
        empty($password)
        ||
        empty($role)
    ){

        $_SESSION['message'] =
        "Veuillez remplir tous les champs.";

        header(
        "Location: ../views/admin/create_user.php"
        );

        exit();
    }

    /*
    =========================================
    HASH PASSWORD
    =========================================
    */

    $hashed_password =
    password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    /*
    =========================================
    PHOTO
    =========================================
    */

    $photo = "default.png";

    /*
    =========================================
    DOSSIER
    =========================================
    */

    $dossier =
    "../uploads/";

    if(!is_dir($dossier)){

        mkdir(
            $dossier,
            0777,
            true
        );
    }

    /*
    =========================================
    UPLOAD IMAGE
    =========================================
    */

    if(
        isset($_FILES['photo']) &&
        $_FILES['photo']['error'] == 0
    ){

        $nomPhoto =
        $_FILES['photo']['name'];

        $tmpPhoto =
        $_FILES['photo']['tmp_name'];

        $extension =
        strtolower(
            pathinfo(
                $nomPhoto,
                PATHINFO_EXTENSION
            )
        );

        $extensionsAutorisees = [

            "jpg",
            "jpeg",
            "png",
            "webp"

        ];

        /*
        =====================================
        VERIFICATION EXTENSION
        =====================================
        */

        if(
            in_array(
                $extension,
                $extensionsAutorisees
            )
        ){

            $photo =
            time()
            . "_"
            . uniqid()
            . "."
            . $extension;

            move_uploaded_file(

                $tmpPhoto,

                $dossier . $photo

            );
        }
    }

    /*
    =========================================
    VERIFIER EMAIL
    =========================================
    */

    $check =
    $conn->prepare("
        SELECT id
        FROM utilisateurs
        WHERE email = ?
    ");

    $check->execute([$email]);

    if($check->rowCount() > 0){

        $_SESSION['message'] =
        "Cet email existe déjà.";

        header(
        "Location: ../views/admin/create_user.php"
        );

        exit();
    }

    /*
    =========================================
    INSERTION
    =========================================
    */

    $sql = $conn->prepare("
        INSERT INTO utilisateurs
        (
            nom,
            email,
            telephone,
            password,
            role,
            photo,
            filiere,
            departement,
            matricule
        )

        VALUES
        (
            ?, ?, ?, ?, ?, ?, ?, ?, ?
        )
    ");

    $insert = $sql->execute([

        $nom,

        $email,

        $telephone,

        $hashed_password,

        $role,

        $photo,

        $filiere,

        $departement,

        $matricule

    ]);

    /*
    =========================================
    SUCCES
    =========================================
    */

    if($insert){

        $_SESSION['message'] =
        "Utilisateur créé avec succès.";

        header(
        "Location: ../views/admin/create_user.php"
        );

        exit();

    }

    else{

        $_SESSION['message'] =
        "Erreur lors de l'ajout.";

        header(
        "Location: ../views/admin/create_user.php"
        );

        exit();
    }
}
?>