<div class="sidebar">

    <!-- LOGO -->

    <div class="logo-box">

        <img
        src="../../assets/images/logo.png"
        class="logo">

        <div>

            <h2>UATM GASA FORMATION</h2>

            <p>GESTION DES MEMOIRES</p>

        </div>

    </div>

    <!-- MENU -->

    <ul class="menu">

        <!-- DASHBOARD -->

        <li class="<?php
        if(basename($_SERVER['PHP_SELF']) == 'dashboard.php')
        echo 'active';
        ?>">

            <a href="dashboard.php">

                <i class="fa-solid fa-house"></i>

                Tableau de bord

            </a>

        </li>

        <!-- CREATE USER -->

        <li class="<?php
        if(basename($_SERVER['PHP_SELF']) == 'create_user.php')
        echo 'active';
        ?>">

            <a href="create_user.php">

                <i class="fa-solid fa-user-plus"></i>

                Créer utilisateur

            </a>

        </li>

        <!-- USERS -->

        <li class="<?php
        if(basename($_SERVER['PHP_SELF']) == 'users.php')
        echo 'active';
        ?>">

            <a href="users.php">

                <i class="fa-solid fa-users"></i>

                Tous les utilisateurs

            </a>

        </li>

        <!-- MEMOIRES -->

        <li class="<?php
        if(basename($_SERVER['PHP_SELF']) == 'memoires.php')
        echo 'active';
        ?>">

            <a href="memoires.php">

                <i class="fa-solid fa-book"></i>

                Mémoires<aside class="sidebar">

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

        <!-- MEMOIRES / INTERACTIONS -->

        <li class="<?php
        if(basename($_SERVER['PHP_SELF'])
        == 'interactions.php')
        echo 'active';
        ?>">

            <a href="../etudiant/interactions.php">

                <i class="fa-solid fa-book"></i>

                <span>
                    Mémoires
                </span>

            </a>

        </li>

        <!-- CREATE USER -->

        <li class="<?php
        if(
            basename($_SERVER['PHP_SELF'])
            == 'create_user.php'
            ||
            basename($_SERVER['PHP_SELF'])
            == 'edit_user.php'
        )
        echo 'active';
        ?>">

            <a href="create_user.php">

                <i class="fa-solid fa-user-plus"></i>

                <span>
                    Créer utilisateur
                </span>

            </a>

        </li>

        <!-- USERS -->

        <li class="<?php
        if(basename($_SERVER['PHP_SELF'])
        == 'users.php')
        echo 'active';
        ?>">

            <a href="users.php">

                <i class="fa-solid fa-users"></i>

                <span>
                    Tous les utilisateurs
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

                <i class="fa-regular fa-envelope"></i>

                <span>
                    Messages
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

        <!-- COMMENTAIRES -->

        <li class="<?php
        if(basename($_SERVER['PHP_SELF'])
        == 'commentaires.php')
        echo 'active';
        ?>">

            <a href="commentaires.php">

                <i class="fa-regular fa-comment"></i>

                <span>
                    Commentaires
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

            </a>

        </li>

        <!-- SOUMISSIONS -->

        <li class="<?php
        if(basename($_SERVER['PHP_SELF']) == 'soumissions.php')
        echo 'active';
        ?>">

            <a href="soumissions.php">

                <i class="fa-solid fa-clipboard"></i>

                Soumissions

            </a>

        </li>

        <!-- COMMENTAIRES -->

        <li class="<?php
        if(basename($_SERVER['PHP_SELF']) == 'commentaires.php')
        echo 'active';
        ?>">

            <a href="commentaires.php">

                <i class="fa-regular fa-comment"></i>

                Commentaires

            </a>

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

</div>