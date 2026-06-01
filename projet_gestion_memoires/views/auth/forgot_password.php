<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
    content="width=device-width, initial-scale=1.0">

    <title>
        Mot de passe oublié
    </title>

    <!-- CSS -->

    <link
    rel="stylesheet"
    href="../../assets/css/forgot_password.css">

    <!-- ICONS -->

    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

<div class="container">

    <!-- LEFT -->

    <div class="left-side">

        <div class="overlay"></div>

        <div class="left-content">

            <!-- LOGO -->

            <img
            src="../../assets/images/logo.png"
            class="logo">

            <!-- TITLE -->

            <h1>
                UATM GASA FORMATION
            </h1>

            <div class="line"></div>

            <h3>
                GESTION DES MÉMOIRES
            </h3>

            <!-- ICON -->

            <div class="forgot-icon">

                <i class="fa-regular fa-envelope"></i>

            </div>

            <!-- TEXT -->

            <h2>
                Mot de passe oublié ?
            </h2>

            <p>

                Entrez votre adresse email associée
                à votre compte et nous vous enverrons
                votre mot de passe par email.

            </p>

        </div>

    </div>

    <!-- RIGHT -->

    <div class="right-side">

        <!-- HEADER -->

        <div class="form-header">

            <div class="header-icon">

                <i class="fa-regular fa-envelope"></i>

            </div>

            <div>

                <h1>
                    Mot de passe oublié
                </h1>

                <p>
                    Nous vous enverrons votre mot de passe par email.
                </p>

            </div>

        </div>

        <!-- MESSAGES -->

        <?php

        if(isset($_SESSION['error'])){

            echo "

            <div class='error-message'>

                ".$_SESSION['error']."

            </div>

            ";

            unset($_SESSION['error']);
        }

        if(isset($_SESSION['success'])){

            echo "

            <div class='success-message'>

                ".$_SESSION['success']."

            </div>

            ";

            unset($_SESSION['success']);
        }

        ?>

        <!-- FORM -->

            <form
            method="POST"
            action="../../controllers/forgot_password_controller.php">

            <!-- EMAIL -->

            <label>
                Email
            </label>

            <div class="input-box">

                <i class="fa-regular fa-envelope"></i>

                <input
                type="email"
                name="email"
                required>

            </div>

            <!-- INFO -->

            <div class="info-box">

                <div class="info-icon">

                    <i class="fa-solid fa-circle-info"></i>

                </div>

                <div>

                    <h4>
                        Information
                    </h4>

                    <p>

                        Entrez l’adresse email
                        que vous utilisez pour vous connecter.

                    </p>

                    <p>

                        Vous recevrez votre mot de passe par email.

                    </p>

                </div>

            </div>

            <!-- BUTTON -->

                <button
                    type="submit"
                    name="forgot_password"
                    class="submit-btn">

                        <i class="fa-regular fa-paper-plane"></i>

                        Envoyer mon mot de passe

                    </button>

        </form>

        <!-- BACK -->

        <a
        href="../../login.php"
        class="back-link">

            <i class="fa-solid fa-arrow-left"></i>

            Retour à la connexion

        </a>

        <!-- BOTTOM -->

        <div class="bottom-text">

            <p>
                Vous n’avez pas de compte ?
            </p>

            <a href="contact_admin.php">

                Contactez l’administrateur

            </a>

        </div>

    </div>

</div>

</body>
</html>