<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
include_once $local_modules_path . "/Connect.php";
include_once $local_modules_path . "/Security.php";
include_once $local_modules_path . "/classes/User.php";

session_start();
session_check(true);

?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta name="description" content="Polytable - улучшенное расписание вашего вуза">
    <meta name="keywords" content="Polytable, Политех, СПбПУ, ИКНТ, КСПТ, ФТК">
    <title>PolyTable</title>
    <link rel="icon" type="image/png" href="assets/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="assets/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="assets/favicon-96x96.png" sizes="96x96">
    <link rel="stylesheet" type="text/css" href="css/semantic.css"/>
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
    <link rel="stylesheet" type="text/css" href="css/search.css"/>
    <link rel="stylesheet" type="text/css" href="css/footer.css"/>
    <!-- Yandex.Metrika counter --> <script type="text/javascript" > (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter48396962 = new Ya.Metrika({ id:48396962, clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks"); </script> <noscript><div><img src="https://mc.yandex.ru/watch/48396962" style="position:absolute; left:-9999px;" alt="" /></div></noscript> <!-- /Yandex.Metrika counter -->
    <script type="text/javascript" src="js/lib/jquery3.2.1.min.js"></script>
    <script type="text/javascript" src="js/lib/jquery-ui.min.js"></script>
    <script type="text/javascript" src="js/lib/jquery.modal.window.js"></script>
    <script type="text/javascript" src="js/lib/jquery.ajax.inputs.js"></script>
    <script type="text/javascript" src="js/lib/jquery.dropzone.js"></script>
    <script type="text/javascript" src="js/loginform.js"></script>
</head>
<body>
    <div id="index_header">
        <?php include_once "templates/header.php"?>
    </div>
    <div id="main">
        <div id="polylogo"><img src="assets/images/logo2.png"/></div>
    </div>
    <div id="index_content">
        <h1>Найди свою группу:</h1>
        <div id="index_search">
            <?php include_once "templates/search.php"; ?>
        </div>
        <h1>Вопрос?</h1>
        <div class="question">
            <div class="question_image"></div>
            <div class="question_text">
                <h3>Как сделать так, чтобы расписание моей группы появилось на сайте?</h3>
                <h4>Расписание загружается на сайт вместе с подтверждением регистрации старосты. Так что если вы не староста, то просите его или её зарегестрироваться.</h4>
            </div>
        </div>
        <div class="question">
            <div class="question_image"></div>
            <div class="question_text">
                <h3>Как часто обновляется расписание?</h3>
                <h4>Расписание сохраняется на две недели вперёд, раз в неделю. Ночью с воскресенья на понедельник.</h4>
            </div>
        </div>
        <div class="question">
            <div class="question_image"></div>
            <div class="question_text">
                <h3>Кто может добавлять ДЗ?</h3>
                <h4>Староста группы и редакторы, которых староста лично назначает в своем профиле.</h4>
            </div>
        </div>
        <div class="question">
            <div class="question_image"></div>
            <div class="question_text">
                <h3>Кто может видеть мои личные данные?</h3>
                <h4>Никто.</h4>
            </div>
        </div>
    </div>
    <?php include_once "templates/footer.php"; ?>

</body>
</html>
