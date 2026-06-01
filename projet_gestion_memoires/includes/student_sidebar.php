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

        <!-- TABLEAU DE BORD -->

        <li class="<?php
        if(
            basename($_SERVER['PHP_SELF'])
            == 'dashboard.php'
        )
        echo 'active';
        ?>">

            <a href="dashboard.php">

                <i class="fa-solid fa-house"></i>

                <span>
                    Tableau de bord
                </span>

            </a>

        </li>

       <!-- MEMOIRES -->

        <li class="<?php
        if(
            basename($_SERVER['PHP_SELF'])
            == 'interactions.php'
        )
        echo 'active';
        ?>">

            <a href="interactions.php">

                <i class="fa-solid fa-book"></i>

                <span>
                    Mémoires
                </span>

            </a>

        </li>

        <!-- UPLOADER UN MEMOIRE -->

        <li class="<?php
        if(
            basename($_SERVER['PHP_SELF'])
            == 'uploader.php'
        )
        echo 'active';
        ?>">

            <a href="uploader.php">

                <i class="fa-solid fa-upload"></i>

                <span>
                    Uploader un mémoire
                </span>

            </a>

        </li>

        <!-- UPLOADER PLUSIEURS MEMOIRES -->

        <li class="<?php
        if(
            basename($_SERVER['PHP_SELF'])
            == 'uploader_multiple.php'
        )
        echo 'active';
        ?>">

            <a href="uploader_multiple.php">

                <i class="fa-solid fa-cloud-arrow-up"></i>

                <span>
                    Uploader plusieurs mémoires
                </span>

            </a>

        </li>

        <!-- SOUMETTRE UN MEMOIRE -->

        <li class="<?php
        if(
            basename($_SERVER['PHP_SELF'])
            == 'soumettre.php'
        )
        echo 'active';
        ?>">

            <a href="soumettre.php">

                <i class="fa-solid fa-paper-plane"></i>

                <span>
                    Soumettre un mémoire
                </span>

            </a>

        </li>

        <!-- MES SOUMISSIONS -->

        <li class="<?php
        if(
            basename($_SERVER['PHP_SELF'])
            == 'soumissions.php'
        )
        echo 'active';
        ?>">

            <a href="soumissions.php">

                <i class="fa-solid fa-list-check"></i>

                <span>
                    Mes soumissions
                </span>

            </a>

        </li>

        <!-- NOTIFICATIONS -->

        <li class="<?php
        if(
            basename($_SERVER['PHP_SELF'])
            == 'notifications.php'
        )
        echo 'active';
        ?>">

            <a href="notifications.php">

                <i class="fa-regular fa-bell"></i>

                <span>
                    Notifications
                </span>

            </a>

        </li>

        <!-- PROFIL -->

        <li class="<?php
        if(
            basename($_SERVER['PHP_SELF'])
            == 'profil.php'
            ||
            basename($_SERVER['PHP_SELF'])
            == 'profile.php'
        )
        echo 'active';
        ?>">

            <a href="profil.php">

                <i class="fa-regular fa-user"></i>

                <span>
                    Profil
                </span>

            </a>

        </li>

    </ul>

    <!-- LOGOUT -->

    <div class="logout">

        <a
        href="../../logout.php"

        onclick="
        return confirm(
        'Voulez-vous vous déconnecter ?'
        )
        ">

            <i class="fa-solid fa-right-from-bracket"></i>

            Déconnexion

        </a>

    </div>

</aside>