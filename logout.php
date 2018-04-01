<?php

    session_start();

    include_once "modules/Security.php";

    if (session_check(true)) {
        unset($_SESSION['user']);
        session_write_close();
    }
    header('Location: ' . "index.php");
    die();