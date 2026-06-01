<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Connexion</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>

<body>

<div class="container">

    <!-- LEFT -->

    <div class="left">

        <div class="overlay">

            <img
                src="assets/images/logo.png"
                class="logo"
            >

            <h1>
                UATM GASA FORMATION
            </h1>

            <div class="line"></div>

            <h2>
                GESTION DES MÉMOIRES
            </h2>

            <i class="fa-solid fa-book-open icon-book"></i>

            <p>
                Plateforme dédiée à la gestion,
                au dépôt, à l’évaluation
                et à la valorisation des mémoires
                de fin d’études.
            </p>

        </div>

    </div>

    <!-- RIGHT -->

    <div class="right">

        <div class="login-box">

            <!-- ICON -->

                <div class="header-login">

                    <div class="icon-user">

                        <i class="fa-solid fa-circle-user"></i>

                    </div>

                    <div class="title-content">

                        <h2>Connexion</h2>

                        <p>
                            Connectez-vous à votre compte
                        </p>

                    </div>

                </div>

            <!-- FORM -->

            <form
                action="controllers/AuthController.php"
                method="POST"
            >

                <!-- EMAIL -->

                <div class="input-box">

                    <label>Email</label>

                    <div class="input">

                        <i class="fa-regular fa-envelope"></i>

                        <input
                            type="email"
                            name="email"
                            placeholder="example@uatm.ga"
                            required
                        >

                    </div>

                </div>

                <!-- PASSWORD -->

                <div class="input-box">

                    <label>Mot de passe</label>

                    <div class="input">

                        <i class="fa-solid fa-lock"></i>

                        <input
                            type="password"
                            name="password"
                            placeholder="********"
                            required
                        >

                        <i class="fa-regular fa-eye"></i>

                    </div>

                </div>

                <!-- FORGOT -->

                <div class="forgot">

                    <a href="views/auth/forgot_password.php">
                        Mot de passe oublié ?
                    </a>

                </div>

                <!-- BUTTON -->

                <button
                    type="submit"
                    name="login"
                    class="btn"
                >

                    <i class="fa-solid fa-right-to-bracket"></i>

                    Se connecter

                </button>

            </form>

            <!-- SEPARATOR -->

            <div class="separator">

                <div class="line-separator"></div>

                <span>OU</span>

                <div class="line-separator"></div>

            </div>

            <!-- ADMIN BUTTON 

            <button class="admin-btn">

                <i class="fa-regular fa-id-badge"></i>

                Se connecter en tant qu’administrateur

            </button> -->

            <!-- BOTTOM TEXT -->

            <div class="bottom-text">

                <p>
                    Vous n’avez pas de compte ?
                </p>

                <a href="views/auth/contact_admin.php">
                    Contactez l’administrateur
                </a>

            </div>

        </div>

    </div>

</div>

</body>

</html>