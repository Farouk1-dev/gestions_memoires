<?php

session_start();

if(
    !isset($_SESSION['id']) ||
    $_SESSION['role'] != 'admin'
){

    header("Location: ../../login.php");

    exit();
}

include("../../config/database.php");

/* ID */

if(!isset($_GET['id'])){

    header("Location: users.php");
    exit();
}

$id = $_GET['id'];

/* USER */

$sql = "SELECT * FROM utilisateurs
WHERE id = :id";

$stmt = $conn->prepare($sql);

$stmt->execute([
    'id' => $id
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

/* USER INTROUVABLE */

if(!$user){

    header("Location: users.php");
    exit();
}

/* UPDATE */

if(isset($_POST['update_user'])){

    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $role = $_POST['role'];

    $photo = $user['photo'];

    /* PHOTO */

    if(isset($_FILES['photo']) &&
    $_FILES['photo']['error'] == 0){

        $photo_name =
        time() . "_" .
        basename($_FILES['photo']['name']);

        $destination =
        "../../uploads/" . $photo_name;

        move_uploaded_file(
            $_FILES['photo']['tmp_name'],
            $destination
        );

        $photo = $photo_name;
    }

    /* SQL UPDATE */

    $update = "
    UPDATE utilisateurs SET

        nom = :nom,
        email = :email,
        telephone = :telephone,
        role = :role,
        photo = :photo

    WHERE id = :id
    ";

    $stmt = $conn->prepare($update);

    $stmt->execute([

        'nom' => $nom,
        'email' => $email,
        'telephone' => $telephone,
        'role' => $role,
        'photo' => $photo,
        'id' => $id

    ]);

    header("Location: users.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
    content="width=device-width, initial-scale=1.0">

    <title>Modifier utilisateur</title>

    <!-- CSS -->

    <link rel="stylesheet"
    href="../../assets/css/create_user.css">

    <!-- ICONS -->

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

                    Modifier utilisateur

                </h2>

                <!-- FORM -->

                <form
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
                            value="<?php echo $user['nom']; ?>"
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
                            value="<?php echo $user['email']; ?>"
                            required>

                        </div>

                    </div>

                    <!-- ROLE -->

                    <div class="input-group">

                        <label>Rôle</label>

                        <div class="input-box">

                            <i class="fa-solid fa-user-shield"></i>

                            <select name="role">

                                <option value="admin"
                                <?php
                                if($user['role'] == 'admin')
                                echo 'selected';
                                ?>>

                                    Administrateur

                                </option>

                                <option value="professeur"
                                <?php
                                if($user['role'] == 'professeur')
                                echo 'selected';
                                ?>>

                                    Professeur

                                </option>

                                <option value="etudiant"
                                <?php
                                if($user['role'] == 'etudiant')
                                echo 'selected';
                                ?>>

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
                            value="<?php echo $user['telephone']; ?>">

                        </div>

                    </div>

                    <!-- PHOTO -->

                    <div class="input-group">

                        <label>Photo de profil</label>

                        <div class="photo-upload">

                            <img
                            src="../../uploads/<?php echo $user['photo']; ?>"
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

                    <!-- BUTTON -->

                    <div class="button-box">

                        <button
                        type="submit"
                        name="update_user">

                            <i class="fa-solid fa-floppy-disk"></i>

                            Modifier l’utilisateur

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </main>

</div>

<!-- PREVIEW PHOTO -->

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