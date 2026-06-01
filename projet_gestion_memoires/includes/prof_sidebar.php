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

        <!-- MEMOIRES -->

        <li class="<?php
        if(
            basename($_SERVER['PHP_SELF'])
            == 'liste_memoires.php'
            ||
            basename($_SERVER['PHP_SELF'])
            == 'detail_memoire.php'
        )
        echo 'active';
        ?>">

            <a href="liste_memoires.php">

                <i class="fa-solid fa-book"></i>

                <span>
                    Mémoires a evaluer
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

        onclick="return confirm(
        'Voulez-vous vous déconnecter ?'
        )">

            <i class="fa-solid fa-right-from-bracket"></i>

            <span>
                Déconnexion
            </span>

        </a>

    </div>

</aside>