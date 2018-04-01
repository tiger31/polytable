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
    <title>PolyTable</title>
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
    <link rel="stylesheet" type="text/css" href="css/search.css"/>
    <link rel="stylesheet" type="text/css" href="css/footer.css"/>
    <script type="text/javascript" src="js/lib/jquery3.2.1.min.js"></script>
    <script type="text/javascript" src="js/lib/jquery.modal.window.js"></script>
    <script type="text/javascript" src="js/lib/jquery.ajax.inputs.js"></script>
    <script type="text/javascript" src="js/lib/jquery.dropzone.js"></script>
    <script type="text/javascript" src="js/loginform.js"></script>
</head>
<body>
    <div id="index_header">
        <div style="padding: 0 30px">
            <?php include_once "templates/header.php"?>
        </div>
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
