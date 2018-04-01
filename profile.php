<?php
    include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
    include_once $local_modules_path . "/Connect.php";
    include_once $local_modules_path . "/classes/User.php";
    include_once $local_modules_path . "/classes/Profile.php";

    session_start();

    include_once $local_modules_path . "/Security.php";

    if (!session_check(true)) {
        header("Location: " . $default_redirect);
    }
    $user = User::loadFromSession();
    $profile = new Profile($user);

?>
<html>
    <head>
        <meta charset="utf-8">
        <title>Профиль</title>
        <link rel="stylesheet" type="text/css" href="css/semantic.css">
        <link rel="stylesheet" type="text/css" href="css/profile.css"/>
        <script type="text/javascript" src="js/lib/jquery3.2.1.min.js"></script>
        <script type="text/javascript" src="js/lib/jquery.ajax.inputs.js"></script>
        <script type="text/javascript" src="js/lib/jquery.formatter.min.js"></script>
        <script type="text/javascript" src="js/loginform.js"></script>
        <script type="text/javascript" src="js/profile.js"></script>
        <script type="text/javascript" src="js/lib/semantic.js"></script>
    </head>
    <body>
            <div id="menu">
                <div class="item"><img src="assets/images/logo3.png"></div>
                <div class="item card">
                    <div id="avatar"><img src="data/image/64/<?=$user->getID()?>.png" /></div>
                    <div id="name"><?=($user->verified) ? $user->getEscapedName() : $user->login;?></div>
                    <div id="post"><?=$user->getPost();?></div>
                </div>
                <? $profile->template_menu();?>
                <a href="logout.php">
                    <div class="item toggle">
                        <i class="ui icon sign out"></i>
                        Выход
                    </div>
                </a>
            </div>
            <div id="content">
                <div id="modules">
                    <? $profile->template_modules(); ?>
                </div>
            </div>

    </body>
</html>
