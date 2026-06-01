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

/* FILTRE ROLE */

$role_filter = $_GET['role'] ?? '';

/* RECHERCHE */

$search = $_GET['search'] ?? '';

/* SQL */

$sql = "SELECT * FROM utilisateurs WHERE 1";

/* PARAMS */

$params = [];

/* FILTRE ROLE */

if($role_filter != ''){

    $sql .= " AND role = :role";

    $params['role'] = $role_filter;
}

/* RECHERCHE */

if($search != ''){

    $sql .= "
    AND (
        nom LIKE :search
        OR email LIKE :search
        OR telephone LIKE :search
    )";

    $params['search'] = "%$search%";
}

/* TRI */

$sql .= " ORDER BY id DESC";

/* PREPARE */

$stmt = $conn->prepare($sql);

/* EXECUTE */

$stmt->execute($params);

/* FETCH */

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
    content="width=device-width, initial-scale=1.0">

    <title>Gestion des utilisateurs</title>

    <!-- CSS -->

    <link rel="stylesheet"
    href="../../assets/css/admin.css">

    <link rel="stylesheet"
    href="../../assets/css/users.css">

    <!-- FONT AWESOME -->

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

<div class="container">

    <!-- SIDEBAR -->

    <?php include("../../includes/admin_sidebar.php"); ?>

    <!-- MAIN -->

    <main class="main">

        <!-- HEADER -->

        <?php include("../../includes/admin_header.php"); ?>

        <!-- CONTENT -->

        <div class="content">

            <!-- TITLE -->

            <div class="page-title">

                <i class="fa-solid fa-users"></i>

                <h2>
                    Gestion des utilisateurs
                </h2>

            </div>

            <!-- SEARCH + FILTER -->

            <div class="top-actions">

                <!-- SEARCH -->

                <form method="GET"
                class="search-box">

                    <i class="fa-solid fa-search"></i>

                    <input
                    type="text"
                    name="search"
                    placeholder="Rechercher un utilisateur..."
                    value="<?php echo $search; ?>">

                </form>

                <!-- FILTER -->

                <form method="GET">

                    <select
                    name="role"
                    onchange="this.form.submit()">

                        <option value="">
                            Tous les rôles
                        </option>

                        <!-- ADMIN -->

                        <option value="admin"
                        <?php
                        if($role_filter == 'admin')
                        echo 'selected';
                        ?>>

                            Administrateurs

                        </option>

                        <!-- PROFESSEUR -->

                        <option value="professeur"
                        <?php
                        if($role_filter == 'professeur')
                        echo 'selected';
                        ?>>

                            Professeurs

                        </option>

                        <!-- ETUDIANT -->

                        <option value="etudiant"
                        <?php
                        if($role_filter == 'etudiant')
                        echo 'selected';
                        ?>>

                            Étudiants

                        </option>

                    </select>

                </form>

            </div>

            <!-- TABLE -->

            <div class="table-box">

                <table>

                    <thead>

                        <tr>

                            <th>Photo</th>

                            <th>Nom</th>

                            <th>Email</th>

                            <th>Téléphone</th>

                            <th>Rôle</th>

                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php

                    if(count($users) > 0){

                        foreach($users as $user){

                    ?>

                        <tr>

                            <!-- PHOTO -->

                            <td>

                                <?php

                                if(!empty($user['photo'])){

                                ?>

                                    <img
                                    src="../../uploads/<?php echo $user['photo']; ?>"
                                    class="user-photo">

                                <?php

                                }else{

                                ?>

                                    <img
                                    src="../../assets/images/default-user.png"
                                    class="user-photo">

                                <?php } ?>

                            </td>

                            <!-- NOM -->

                            <td>

                                <?php echo $user['nom']; ?>

                            </td>

                            <!-- EMAIL -->

                            <td>

                                <?php echo $user['email']; ?>

                            </td>

                            <!-- TELEPHONE -->

                            <td>

                                <?php echo $user['telephone']; ?>

                            </td>

                            <!-- ROLE -->

                            <td>

                                <span class="role-badge">

                                    <?php echo ucfirst($user['role']); ?>

                                </span>

                            </td>

                            <!-- ACTIONS -->

                            <td>

                                <div class="actions">

                                    <!-- EDIT -->

                                    <a
                                    href="edit_user.php?id=<?php echo $user['id']; ?>"
                                    class="edit-btn">

                                        <i class="fa-solid fa-pen"></i>

                                    </a>

                                    <!-- DELETE -->

                                    <a
                                    href="../../controllers/delete_user.php?id=<?php echo $user['id']; ?>"
                                    class="delete-btn"
                                    onclick="return confirm('Supprimer cet utilisateur ?')">

                                        <i class="fa-solid fa-trash"></i>

                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php

                        }

                    }else{

                    ?>

                        <tr>

                            <td colspan="6"
                            style="text-align:center;padding:40px;">

                                Aucun utilisateur trouvé

                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</div>

</body>
</html>