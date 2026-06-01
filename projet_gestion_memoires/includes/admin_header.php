<aside class="sidebar">

    <!-- LOGO -->

    <div class="logo-box">

        <img
        src="../../assets/images/logo.png"
        class="logo">

        <div>

            <h2>
                UATM GASA FORMATION
            </h2>

            <p>
                GESTION DES MEMOIRES
            </p>

        </div>

    </div>

    <!-- MENU -->

    <ul class="menu">

        <!-- DASHBOARD -->

        <li class="<?php
        if(basename($_SERVER['PHP_SELF'])
        == 'dashboard.php')
        echo 'active';
        ?>">

            <a href="dashboard.php">

                <i class="fa-solid fa-house"></i>

                <span>
                    Tableau de bord
                </span>

            </a>

        </li>


        <li class="<?php
            if(basename($_SERVER['PHP_SELF'])
            == 'interactions.php')
            echo 'active';
            ?>">

                <a href="interactions.php">

                    <i class="fa-solid fa-book"></i>

                    <span>
                        Tous les mémoires
                    </span>

                </a>

        </li>

        <!-- UTILISATEURS -->

        <li class="<?php
        if(
            basename($_SERVER['PHP_SELF'])
            == 'users.php'
            ||
            basename($_SERVER['PHP_SELF'])
            == 'edit_user.php'
        )
        echo 'active';
        ?>">

            <a href="users.php">

                <i class="fa-solid fa-users"></i>

                <span>
                    Utilisateurs
                </span>

            </a>

        </li>

        <!-- CREER UTILISATEUR -->

        <li class="<?php
        if(basename($_SERVER['PHP_SELF'])
        == 'create_user.php')
        echo 'active';
        ?>">

            <a href="create_user.php">

                <i class="fa-solid fa-user-plus"></i>

                <span>
                    Créer utilisateur
                </span>

            </a>

        </li>


        <!-- SOUMISSIONS -->

        <li class="<?php
        if(basename($_SERVER['PHP_SELF'])
        == 'soumissions.php')
        echo 'active';
        ?>">

            <a href="soumissions.php">

                <i class="fa-solid fa-paper-plane"></i>

                <span>
                    Soumissions
                </span>

            </a>

        </li>

        <!-- CONTACT NOTIFICATIONS -->

        <li class="<?php
        if(basename($_SERVER['PHP_SELF'])
        == 'contact_notifications.php')
        echo 'active';
        ?>">

            <a href="contact_notifications.php">

                <i class="fa-solid fa-bell"></i>

                <span>
                    Notifications
                </span>

            </a>

        </li>

        <!-- PROFIL -->

        <li class="<?php
        if(basename($_SERVER['PHP_SELF'])
        == 'profile.php')
        echo 'active';
        ?>">

            <!-- <a href="profile.php">

                <i class="fa-regular fa-user"></i>

                <span>
                    Profil
                </span>

            </a>-->

        </li>

    </ul>

    <!-- LOGOUT -->

    <div class="logout">

        <a
            href="../../logout.php"
            onclick="return confirm('Voulez-vous vous déconnecter ?')"
        >

            <i class="fa-solid fa-right-from-bracket"></i>

            Déconnexion

        </a>

    </div>

</aside>