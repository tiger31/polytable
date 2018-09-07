<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
header("pragma","no-cache");
$phrases = [
        "default" => [
                "Ищешь свою пару?",
                "Не падаем!",
                "Сегодня физра в 8?",
                "У пас пара, возможно лаба, по коням!",
                "Осталась пара вопросов",
                "А вы тоже в детстве не любили спать днём?",
                "Закройте окно!",
                "С легкой парой!",
                "Между первой и второй перерывчик небольшой",
                "Запарная неделя!"
        ],
        "rare" => [
                "Fun and interactive",
                "Lakad Matatag! Normalin, Normalin",
                "Еще не отчислили?",
                "Кажется, я уронил ruz",
                "Опять работа?",
                "Нельзя сотворить здесь",
                "Я получил власть, которая и не снилась моему отцу",
                "Спасибо в карман не положишь"
        ]
    ];
$ticket_max = 100;
$chances = [0.9, 0.1];
$first_ticket = rand(0, $ticket_max);
$phrase = "";
if ($first_ticket <= 90)
    $phrase = $phrases['default'][rand(0, count($phrases['default']) - 1)];
else
    $phrase = $phrases['rare'][rand(0, count($phrases['rare']) - 1)];
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta name="description" content="Polytable - улучшенное расписание вашего вуза">
    <meta name="keywords" content="Polytable, Политех, СПбПУ, ИКНТ, КСПТ, ФТК">
    <meta name="theme-color" content="#105d3b">
    <title>PolyTable</title>
    <link rel="icon" type="image/png" href="assets/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="assets/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="assets/favicon-96x96.png" sizes="96x96">
    <link rel="stylesheet" type="text/css" href="css/icon.css"/>
    <link rel="stylesheet" type="text/css" href="css/style.css?1337"/>
    <link rel="stylesheet" type="text/css" href="css/search.css"/>
    <link rel="stylesheet" type="text/css" href="css/footer.css"/>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&amp;subset=cyrillic" rel="stylesheet">
    <!-- Yandex.Metrika counter --> <script type="text/javascript" > (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter48396962 = new Ya.Metrika({ id:48396962, clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks"); </script> <noscript><div><img src="https://mc.yandex.ru/watch/48396962" style="position:absolute; left:-9999px;" alt="" /></div></noscript> <!-- /Yandex.Metrika counter -->
    <script type="text/javascript" src="js/lib/jquery3.2.1.min.js"></script>
    <script type="text/javascript" src="js/lib/jquery.modal.window.js"></script>
    <script type="text/javascript" src="js/lib/jquery.ajax.inputs.js?1337"></script>
    <script type="text/javascript" src="js/loginform.js"></script>
</head>
<body>
    <div id="index_header">
        <?php include_once "templates/header.php"?>
    </div>
    <div id="index_content">
        <img id="polytable" src="assets/images/biglogo.png">
        <div id="phrase"><?=$phrase;?></div>
        <div id="index_search">
            <?php include_once "templates/search.php"; ?>
        </div>
    </div>
    <div id="index_footer">
        <?php include_once "templates/footer.php"; ?>
    </div>


</body>
</html>
