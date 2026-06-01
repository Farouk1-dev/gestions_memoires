<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if(
    !isset($_SESSION['id']) ||
    $_SESSION['role'] != 'etudiant'
){

    header("Location: ../../login.php");

    exit();
}

include("../../config/database.php");

/*
=========================================
UTILISATEUR CONNECTÉ
=========================================
*/
$user_id = $_SESSION['id'];

/*
=========================================
RECUPERATION DES NOTIFICATIONS
=========================================
*/

$query = $conn->prepare("
    SELECT *
    FROM notifications
    WHERE utilisateur_id = ?
    ORDER BY id DESC
");

$query->execute([$user_id]);

$notifications = $query->fetchAll(PDO::FETCH_ASSOC);

/*
=========================================
COMPTEURS
=========================================
*/

$totalNotifications = count($notifications);

$nonLues = 0;
$commentaires = 0;
$memoires = 0;
$likes = 0;
$systeme = 0;

foreach($notifications as $notif){

    if($notif['statut'] == 'non_lu'){
        $nonLues++;
    }

    if(stripos($notif['titre'], 'commentaire') !== false){
        $commentaires++;
    }

    elseif(stripos($notif['titre'], 'mémoire') !== false){
        $memoires++;
    }

    elseif(stripos($notif['titre'], 'like') !== false){
        $likes++;
    }

    else{
        $systeme++;
    }

}

/*
====================================
PHOTO UTILISATEUR
====================================
*/

$userQuery = $conn->prepare("
SELECT photo
FROM utilisateurs
WHERE id = ?
");

$userQuery->execute([$user_id]);

$user = $userQuery->fetch(PDO::FETCH_ASSOC);

$photoProfil = "default.png";

if(
    !empty($user['photo']) &&
    file_exists("../../uploads/" . $user['photo'])
){
    $photoProfil = $user['photo'];
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
    content="width=device-width, initial-scale=1.0">

    <title>Notifications</title>

    <!-- CSS -->

    <link rel="stylesheet"
    href="../../assets/css/notifications_etudiant.css">

    <!-- ICONS -->

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

</head>

<body>

<div class="container">

    <!-- SIDEBAR -->

    <?php include("../../includes/student_sidebar.php"); ?>

    <!-- MAIN -->

    <main class="main">

        <!-- HEADER -->

       <header class="header">
            <div class="header-left">

                <i class="fa-solid fa-bars"></i>

                <h1>Notifications</h1>

            </div>

            <div class="header-right">

                <div class="header-notif">

                    <i class="fa-regular fa-bell"></i>

                    <span><?= $nonLues ?></span>

                </div>

                <div class="profile-box">

                    <img
                        src="../../uploads/<?= $photoProfil ?>"
                        class="profile">

                    <div>

                    <h3>
                            <?= htmlspecialchars($_SESSION['nom']) ?>
                        </h3>

                        <p>
                            Étudiant
                        </p>

                    </div>

                </div>

            </div>

        </header>

        <!-- CONTENT -->

        <div class="content">

            <!-- TOP -->

            <div class="content-top">

                <h2>
                    Notifications
                </h2>

                <button class="read-btn">

                    <i class="fa-solid fa-check"></i>

                    Tout marquer comme lu

                </button>

            </div>

            <!-- TABS -->

            <div class="tabs">

                <button class="active">

                    Toutes

                    <span>
                        <?= $totalNotifications ?>
                    </span>

                </button>

                <button>

                    Non lues

                    <span>
                        <?= $nonLues ?>
                    </span>

                </button>

                <button>

                    Commentaires

                    <span>
                        <?= $commentaires ?>
                    </span>

                </button>

                <button>

                    Likes

                    <span>
                        <?= $likes ?>
                    </span>

                </button>

                <button>

                    Mémoires

                    <span>
                        <?= $memoires ?>
                    </span>

                </button>

            </div>

            <!-- BODY -->

            <div class="notifications-layout">

                <!-- LISTE -->

                <div class="notifications-list">

                    <?php foreach($notifications as $notification): ?>

                    <?php

                    $classe = "systeme";

                    if(stripos($notification['titre'], 'commentaire') !== false){
                        $classe = "commentaire";
                    }

                    elseif(stripos($notification['titre'], 'like') !== false){
                        $classe = "like";
                    }

                    elseif(stripos($notification['titre'], 'mémoire') !== false){
                        $classe = "memoire";
                    }

                    ?>

                    <div class="notification-item">

                        <!-- ICON -->

                        <div class="notif-icon <?= $classe ?>">

                            <?php

                            if($classe == 'commentaire'){

                                echo '<i class="fa-regular fa-message"></i>';

                            }

                            elseif($classe == 'like'){

                                echo '<i class="fa-regular fa-heart"></i>';

                            }

                            elseif($classe == 'memoire'){

                                echo '<i class="fa-regular fa-file-lines"></i>';

                            }

                            else{

                                echo '<i class="fa-solid fa-gear"></i>';

                            }

                            ?>

                        </div>

                        <!-- CONTENT -->

                        <div class="notif-content">

                            <h3>
                                <?= $notification['titre'] ?>
                            </h3>

                            <p>
                                <?= $notification['message'] ?>
                            </p>

                        </div>

                        <!-- DATE -->

                        <div class="notif-time">

                            <?= $notification['created_at'] ?>

                        </div>

                    </div>

                    <?php endforeach; ?>

                </div>

                <!-- RIGHT -->

                <div class="side-summary">

                    <!-- RESUME -->

                    <div class="summary-box">

                        <h3>
                            Résumé
                        </h3>

                        <ul>

                            <li>

                                <span>Toutes</span>

                                <b>
                                    <?= $totalNotifications ?>
                                </b>

                            </li>

                            <li>

                                <span>Non lues</span>

                                <b>
                                    <?= $nonLues ?>
                                </b>

                            </li>

                            <li>

                                <span>Commentaires</span>

                                <b>
                                    <?= $commentaires ?>
                                </b>

                            </li>

                            <li>

                                <span>Likes</span>

                                <b>
                                    <?= $likes ?>
                                </b>

                            </li>

                            <li>

                                <span>Mémoires</span>

                                <b>
                                    <?= $memoires ?>
                                </b>

                            </li>

                            <li>

                                <span>Système</span>

                                <b>
                                    <?= $systeme ?>
                                </b>

                            </li>

                        </ul>

                    </div>

                    <!-- CONSEIL -->

                    <div class="tips-box">

                        <h3>
                            Conseil
                        </h3>

                        <p>

                            Activez les notifications
                            pour rester informé des
                            nouveaux commentaires,
                            likes et changements
                            de statut de vos mémoires.

                        </p>

                        <button>

                            Activer

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </main>

</div>

</body>
</html>