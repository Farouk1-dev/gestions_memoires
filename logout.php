<?php

session_start();

/*
=====================================
SUPPRIMER TOUTES LES SESSIONS
=====================================
*/

$_SESSION = [];

/*
=====================================
DETUIRE SESSION
=====================================
*/

session_destroy();

/*
=====================================
SUPPRIMER COOKIE SESSION
=====================================
*/

if(ini_get("session.use_cookies")){

    $params = session_get_cookie_params();

    setcookie(

        session_name(),

        '',

        time() - 42000,

        $params["path"],

        $params["domain"],

        $params["secure"],

        $params["httponly"]

    );
}

/*
=====================================
REDIRECTION LOGIN
=====================================
*/

header("Location: login.php");

exit();

?>