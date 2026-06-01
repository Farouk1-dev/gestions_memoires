<?php

session_start();

include("../config/database.php");

if(isset($_POST['login'])){

    login();

}

function login(){

    global $conn;

    $email = $_POST['email'];

    $password = $_POST['password'];

    $sql = "
    SELECT *
    FROM utilisateurs
    WHERE email = :email
    ";

    $requete = $conn->prepare($sql);

    $requete->execute([

        ":email" => $email

    ]);

    $utilisateur = $requete->fetch(
        PDO::FETCH_ASSOC
    );

    if($utilisateur){

        if(
            password_verify(
                $password,
                $utilisateur['password']
            )
        ){

            $_SESSION['id'] =
            $utilisateur['id'];

            $_SESSION['nom'] =
            $utilisateur['nom'];

            $_SESSION['role'] =
            $utilisateur['role'];

            // Redirection selon rôle

            if(
                $utilisateur['role']
                == "admin"
            ){

                header(
                    "Location: ../views/admin/dashboard.php"
                );

            }

            elseif(
                $utilisateur['role']
                == "etudiant"
            ){

                header(
                    "Location: ../views/etudiant/dashboard.php"
                );

            }

            elseif(
                $utilisateur['role']
                == "professeur"
            ){

                header(
                    "Location: ../views/professeur/dashboard.php"
                );

            }

        }else{

            echo "Mot de passe incorrect";

        }

    }else{

        echo "Utilisateur introuvable";

    }

}

?>