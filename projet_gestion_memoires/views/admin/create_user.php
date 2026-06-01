<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
    content="width=device-width, initial-scale=1.0">

    <title>Créer un utilisateur</title>

    <link rel="stylesheet"
    href="../../assets/css/create_user.css">

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

<div class="container">

    <!-- SIDEBAR -->


    <!-- MAIN -->

    <main class="main">

        <!-- HEADER -->

        <?php include("../../includes/admin_header.php"); ?>

        <!-- CONTENT -->

        <div class="content">

            <!-- FORM BOX -->

            <div class="form-box">

                <h2 class="form-title">

                    Nouveau utilisateur

                </h2>

                <!-- ALERTES -->

                <?php

                if(isset($_GET['success'])){

                    if($_GET['success'] == "etudiant"){

                        echo '
                        <div class="alert success">
                            Étudiant créé avec succès.
                        </div>
                        ';

                    }elseif($_GET['success'] == "professeur"){

                        echo '
                        <div class="alert success">
                            Professeur créé avec succès.
                        </div>
                        ';

                    }elseif($_GET['success'] == "admin"){

                        echo '
                        <div class="alert success">
                            Administrateur créé avec succès.
                        </div>
                        ';
                    }
                }

                if(isset($_GET['error'])){

                    if($_GET['error'] == "email"){

                        echo '
                        <div class="alert error">
                            Cet email existe déjà.
                        </div>
                        ';
                    }
                }

                ?>

                <!-- FORM -->

                <form
                action="../../controllers/UtilisateurController.php"
                method="POST"
                enctype="multipart/form-data">

                    <!-- NOM -->

                    <div class="input-group">

                        <label>Nom complet</label>

                        <div class="input-box">

                            <i class="fa-regular fa-user"></i>

                            <input
                            type="text"
                            name="nom"
                            placeholder="Ex. Kouassi Jean"
                            required>

                        </div>

                    </div>

                    <!-- EMAIL -->

                    <div class="input-group">

                        <label>Email</label>

                        <div class="input-box">

                            <i class="fa-regular fa-envelope"></i>

                            <input
                            type="email"
                            name="email"
                            placeholder="example@uatm.ga"
                            required>

                        </div>

                    </div>

                    <!-- ROLE -->

                    <div class="input-group">

                        <label>Rôle</label>

                        <div class="input-box">

                            <i class="fa-solid fa-user-shield"></i>

                            <select name="role" required>

                                <option value="">
                                    Sélectionner un rôle
                                </option>

                                <option value="admin">
                                    Administrateur
                                </option>

                                <option value="professeur">
                                    Professeur
                                </option>

                                <option value="etudiant">
                                    Étudiant
                                </option>

                            </select>

                        </div>

                    </div>

                    <!-- TELEPHONE -->

                    <div class="input-group">

                        <label>Téléphone</label>

                        <div class="input-box">

                            <i class="fa-solid fa-phone"></i>

                            <input
                            type="text"
                            name="telephone"
                            placeholder="07 00 00 00 00"
                            required>

                        </div>

                    </div>

                    <!-- PHOTO -->

                    <div class="input-group">

                        <label>Photo de profil</label>

                        <div class="photo-upload">

                            <img
                            src="../../assets/images/default-user.png"
                            class="preview-photo"
                            id="previewPhoto">

                            <input
                            type="file"
                            name="photo"
                            id="photoInput"
                            hidden>

                            <button
                            type="button"
                            class="upload-btn"
                            onclick="document.getElementById('photoInput').click()">

                                <i class="fa-solid fa-camera"></i>

                                Choisir une photo

                            </button>

                        </div>

                    </div>

                    <!-- PASSWORD -->

                    <div class="input-group">

                        <label>Mot de passe</label>

                        <div class="input-box">

                            <i class="fa-solid fa-lock"></i>

                            <input
                            type="password"
                            name="password"
                            placeholder="********"
                            required>

                            <i class="fa-regular fa-eye password-toggle"></i>

                        </div>

                    </div>

                    <!-- BUTTON -->

                    <div class="button-box">

                        <button
                        type="submit"
                        name="create_user">

                            <i class="fa-solid fa-user-plus"></i>

                            Créer l’utilisateur

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </main>

</div>

<!-- SCRIPT PHOTO -->

<script>

const photoInput =
document.getElementById("photoInput");

const previewPhoto =
document.getElementById("previewPhoto");

photoInput.addEventListener("change", function(){

    const file = this.files[0];

    if(file){

        previewPhoto.src =
        URL.createObjectURL(file);
    }

});

</script>

</body>
</html>