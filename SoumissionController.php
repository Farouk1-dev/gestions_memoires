<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

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

include("../models/Soumission.php");

class SoumissionController{

    private $soumissionModel;

    private $conn;

    public function __construct($conn){

        $this->conn = $conn;

        $this->soumissionModel =
        new Soumission($conn);
    }

    /*
    =========================================
    SOUMETTRE
    =========================================
    */

    public function soumettre(){

        /*
        =====================================
        ETUDIANT CONNECTÉ
        =====================================
        */

        $etudiant_id = $_SESSION['id'];

        /*
        =====================================
        DONNEES
        =====================================
        */

        $memoire_id =
        $_POST['memoire_id'];

        $professeur_id =
        $_POST['professeur_id'];

        $commentaire =
        htmlspecialchars(
            trim($_POST['message'])
        );

        /*
        =====================================
        VALIDATION
        =====================================
        */

        if(
            empty($memoire_id)
            ||
            empty($professeur_id)
        ){

            $_SESSION['message'] =
            "Veuillez remplir tous les champs.";

            header(
                "Location: ../views/etudiant/soumettre.php"
            );

            exit;
        }

        /*
        =====================================
        INSERTION
        =====================================
        */

        $this->soumissionModel
        ->ajouter(

            $memoire_id,

            $etudiant_id,

            $professeur_id,

            $commentaire

        );

        /*
        =====================================
        NOTIFICATION PROF
        =====================================
        */

        $titre =
        "Nouveau mémoire soumis";

        $message =
        "Un étudiant vous a soumis un mémoire.";

        $notif = $this->conn->prepare("
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

            $professeur_id,

            $titre,

            $message

        ]);

        /*
        =====================================
        SUCCES
        =====================================
        */

        $_SESSION['message'] =
        "Mémoire soumis avec succès.";

        header(
            "Location: ../views/etudiant/soumettre.php"
        );

        exit;
    }
}

/*
=========================================
EXECUTION
=========================================
*/

if(isset($_POST['submit'])){

    $controller =
    new SoumissionController($conn);

    $controller->soumettre();
}