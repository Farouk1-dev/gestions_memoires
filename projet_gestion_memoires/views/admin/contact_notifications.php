<?php

session_start();

/*
===================================
SECURITE ADMIN
===================================
*/

if(
    !isset($_SESSION['id']) ||
    $_SESSION['role'] != 'admin'
){

    header("Location: ../../login.php");
    exit();
}

include("../../config/database.php");

/*
===================================
RECUPERER MESSAGES
===================================
*/

$query = $conn->query("

SELECT *
FROM contact_admin
ORDER BY id DESC

");

$messages =
$query->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
Notifications contact
</title>

<!-- CSS -->

<link
rel="stylesheet"
href="../../assets/css/admin_dashboard.css">

<link
rel="stylesheet"
href="../../assets/css/contact_notifications.css">

<!-- ICONS -->

<link
rel="stylesheet"
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

            <!-- HEADER PAGE -->

            <div class="page-header">

                <div>

                    <h1>
                        Messages reçus
                    </h1>

                    <p>
                        Consultez les demandes envoyées
                        par les utilisateurs.
                    </p>

                </div>

                <div class="message-count">

                    <?= count($messages) ?>

                </div>

            </div>

            <!-- LISTE -->

            <?php if(count($messages) > 0){ ?>

                <div class="messages-list">

                    <div class="messages-list">

<?php foreach($messages as $msg): ?>

    <div class="message-card">

        <!-- LEFT -->

        <div class="card-left">

            <div class="avatar">
                <i class="fa-regular fa-user"></i>
            </div>

            <div class="message-info">

                <h3>
                    <?= htmlspecialchars($msg['nom']) ?>
                </h3>

                <p class="email">
                    <?= htmlspecialchars($msg['email']) ?>
                </p>

                <div class="subject">
                    Sujet : <?= htmlspecialchars($msg['sujet']) ?>
                </div>

                <p class="message-text">
                    <?= nl2br(htmlspecialchars($msg['message'])) ?>
                </p>

            </div>

        </div>

        <!-- RIGHT -->

        <div class="card-right">

            <div class="date">

                <i class="fa-regular fa-calendar"></i>

                <?= $msg['created_at'] ?>

            </div>

            <a
            class="delete-btn"

            href="../../controllers/delete_contact_notification.php?id=<?= $msg['id'] ?>"

            onclick="return confirm('Supprimer ce message ?')">

                <i class="fa-regular fa-trash-can"></i>

                Supprimer

            </a>

        </div>

    </div>

<?php endforeach; ?>

</div>

                </div>

            <?php } else { ?>

                <div class="empty-box">

                    <i class="fa-regular fa-envelope-open"></i>

                    <h2>
                        Aucun message reçu
                    </h2>

                    <p>
                        Les demandes des utilisateurs
                        apparaîtront ici.
                    </p>

                </div>

            <?php } ?>

        </div>

    </main>

</div>

</body>
</html>