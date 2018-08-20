<?php
    include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
    include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/Security/Shield.php";
    include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/User/User.php";
    use User\User;

    $user = User::$user;
    if (!$user) {
        header("Location: " . "/");
        die();
    }
    header("pragma","no-cache");
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Cache-Control" content="no-cache">
        <title>Профиль</title>
        <link rel="icon" type="image/png" href="assets/favicon-16x16.png" sizes="16x16">
        <link rel="icon" type="image/png" href="assets/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="assets/favicon-96x96.png" sizes="96x96">
        <link rel="stylesheet" type="text/css" href="css/icon.css">
        <link rel="stylesheet" type="text/css" href="css/profile.css?1337"/>
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&amp;subset=cyrillic" rel="stylesheet">
        <!-- Yandex.Metrika counter --> <script type="text/javascript" >  (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter48396962 = new Ya.Metrika({ id:48396962, clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks"); </script> <noscript><div><img src="https://mc.yandex.ru/watch/48396962" style="position:absolute; left:-9999px;" alt="" /></div></noscript> <!-- /Yandex.Metrika counter -->
        <script type="text/javascript" src="js/lib/jquery3.2.1.min.js"></script>
        <script type="text/javascript" src="js/lib/handlebars-latest.js"></script>
        <script type="text/javascript" src="js/lib/moment.js"></script>
        <script type="text/javascript" src="js/lib/jquery.modal.window.js"></script>
        <script type="text/javascript" src="js/lib/jquery.ajax.inputs.js?1337"></script>
        <script type="text/javascript" src="js/lib/field.password.js"></script>
        <script type="text/javascript" src="js/lib/jquery.formatter.min.js"></script>
        <script type="text/javascript" src="js/loginform.js"></script>
        <script type="text/javascript" src="js/profile.js?1337"></script>
    </head>
    <body>
            <div id="menu">
                <div class="item"><a href="/"><img src="assets/images/logo3.png"></a></div>
                <div class="item card">
                    <div id="avatar"><img src="data/image/64/<?=$user['id']?>.png" /></div>
                    <div id="name"><?=($user->verified) ? $user->getEscapedName() : $user['login'];?></div>
                    <div id="post"><?=$user->getPost();?></div>
                </div>
                <a href="logout.php">
                    <div class="item toggle">
                        <i class="ui icon sign out alternate"></i>
                        Выход
                    </div>
                </a>
            </div>
            <div id="content">
                <div id="modules">
                </div>
            </div>

    </body>
</html>
