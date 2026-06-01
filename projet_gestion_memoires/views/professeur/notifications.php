<?php

session_start();

include("../../config/database.php");

/*
=========================================
SECURITE
=========================================
*/

if(
    !isset($_SESSION['id'])
    ||
    $_SESSION['role'] != 'professeur'
){
    header("Location: ../../login.php");
    exit();
}

/*
=========================================
PROFESSEUR CONNECTÉ
=========================================
*/

$professeur_id = $_SESSION['id'];

/*
=========================================
RECUPERER NOTIFICATIONS
=========================================
*/

$notifications = $conn->prepare("
SELECT *
FROM notifications
WHERE utilisateur_id = ?
ORDER BY created_at DESC
");

$notifications->execute([$professeur_id]);

$notifications = $notifications->fetchAll();

/*
=========================================
NOMBRE NOTIFICATIONS
=========================================
*/

$total_notifications = count($notifications);

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <title>
        Notifications
    </title>

    <link rel="stylesheet"
    href="../../assets/css/notifications.css">

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

<div class="container">

    <!-- SIDEBAR -->

    <?php include("../../includes/prof_sidebar.php"); ?>

    <!-- MAIN -->

    <main class="main">

        <!-- HEADER -->

        <header class="header">

            <div class="header-left">

                <i class="fa-solid fa-bars"></i>

                <div>
                    <h1>Notifications</h1>
                </div>

            </div>

            <div class="header-right">

                <!-- ICON NOTIFICATION -->

                <div class="notif-icon">

                    <i class="fa-regular fa-bell"></i>

                    <span>
                        <?= $total_notifications ?>
                    </span>

                </div>

                <!-- PROFIL -->

                <div class="profile-box">

                    <?php

                        $prof = $conn->prepare("
                        SELECT photo
                        FROM utilisateurs
                        WHERE id = ?
                        ");

                        $prof->execute([$professeur_id]);

                        $profData = $prof->fetch();

                        ?>

                        <img
                            src="../../uploads/<?=
                            !empty($profData['photo'])
                            ? $profData['photo']
                            : 'default.png'
                            ?>"
                            class="profile"
                        >

                    <div>

                        <h3>

                            <?= $_SESSION['nom'] ?>

                        </h3>

                        <p>
                            Maître de mémoire
                        </p>

                    </div>

                </div>

                <i class="fa-solid fa-chevron-down"></i>

            </div>

        </header>

        <!-- CONTENT -->

        <div class="content">

            <!-- TABS -->

            <div class="tabs">

                <div class="tab active">

                    Toutes
                    (<?= $total_notifications ?>)

                </div>

                <button class="read-btn">

                    <i class="fa-regular fa-circle-check"></i>

                    Marquer tout comme lu

                </button>

            </div>

            <!-- FILTERS -->

            <div class="filters">

                <div class="search-box">

                    <input
                    type="text"
                    placeholder="Rechercher une notification...">

                    <i class="fa-solid fa-magnifying-glass"></i>

                </div>

            </div>

            <!-- NOTIFICATIONS -->

            <div class="notifications-box">

                <?php
                if(count($notifications) > 0):
                ?>

                    <?php
                    foreach($notifications as $notification):
                    ?>

                        <?php

                        /*
                        =====================================
                        TYPE NOTIFICATION
                        =====================================
                        */

                        $message = $notification['message'];

                        $icon = "fa-regular fa-bell";
                        $color = "blue";
                        $tag = "Notification";
                        $tag_class = "blue-tag";

                        if(stripos($message, 'soumis') !== false){

                            $icon = "fa-regular fa-file-lines";
                            $color = "blue";
                            $tag = "Soumission";
                            $tag_class = "blue-tag";
                        }

                        elseif(stripos($message, 'validé') !== false){

                            $icon = "fa-regular fa-circle-check";
                            $color = "green";
                            $tag = "Validé";
                            $tag_class = "green-tag";
                        }

                        elseif(stripos($message, 'retourné') !== false){

                            $icon = "fa-solid fa-rotate-left";
                            $color = "red";
                            $tag = "Correction";
                            $tag_class = "red-tag";
                        }

                        elseif(stripos($message, 'commentaire') !== false){

                            $icon = "fa-regular fa-message";
                            $color = "purple";
                            $tag = "Commentaire";
                            $tag_class = "purple-tag";
                        }

                        ?>

                        <!-- ITEM -->

                        <div class="notif-item">

                            <div class="notif-left">

                                <div class="notif-icon <?= $color ?>">

                                    <i class="<?= $icon ?>"></i>

                                </div>

                                <div>

                                    <h3>

                                        <?= htmlspecialchars($tag) ?>

                                    </h3>

                                    <p>

                                        <?= htmlspecialchars($message) ?>

                                    </p>

                                    <span class="tag <?= $tag_class ?>">

                                        <?= htmlspecialchars($tag) ?>

                                    </span>

                                </div>

                            </div>

                            <div class="notif-right">

                                <span>

                                    <?= date(
                                        'd/m/Y H:i',
                                        strtotime(
                                            $notification['created_at']
                                        )
                                    ) ?>

                                </span>

                                <div class="dot active-dot"></div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <!-- VIDE -->

                    <div class="empty-box">

                        <i class="fa-regular fa-bell-slash"></i>

                        <h3>
                            Aucune notification
                        </h3>

                        <p>
                            Vous n’avez aucune notification pour le moment.
                        </p>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </main>

</div>

</body>
</html>