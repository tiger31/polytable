<?php error_reporting(-1);
    include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
    use User\User;
    //Data checks
    if (!isset($_GET['id']) or (isset($_GET['id']) and !preg_match(REGEX['group'], $_GET['id']))) {
        //TODO searching
        header("Location: " . "/");
        die();
    }
    $result = $mysql->exec(QUERY_GROUP_SELECT, RETURN_FALSE_ON_EMPTY, array("name" => $_GET['id']));
    if (!$result) {
        header("Location: " . "/");
        die();
    }
    $user = User::$user;
    $editor = ($user instanceof User) ? ($user->have_access(16385) && $user->group_id == $result['id']) : false;
    $date = new DateTime();
    header("pragma","no-cache");
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
        <meta http-equiv="Cache-Control" content="no-cache">
        <meta name="description" content="Polytable - улучшенное расписание вашего вуза">
        <meta name="keywords" content="Polytable, Политех, СПбПУ, ИКНТ, КСПТ, ФТК">
        <meta name="theme-color" content="#105d3b">
        <title>Группа <?=$result['name'];?> - PolyTable</title>
        <link rel="icon" type="image/png" href="assets/favicon-16x16.png" sizes="16x16">
        <link rel="icon" type="image/png" href="assets/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="assets/favicon-96x96.png" sizes="96x96">
        <link rel="stylesheet" type="text/css" href="css/icon.css">
        <link rel="stylesheet" type="text/css" href="css/style.css?1337"/>
        <link rel="stylesheet" type="text/css" href="css/dropzone.css"/>
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&amp;subset=cyrillic" rel="stylesheet">
        <!-- Yandex.Metrika counter -->  <script type="text/javascript" >  (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter48396962 = new Ya.Metrika({ id:48396962, clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks"); </script> <noscript><div><img src="https://mc.yandex.ru/watch/48396962" style="position:absolute; left:-9999px;" alt="" /></div></noscript> <!-- /Yandex.Metrika counter -->
        <script type="text/javascript" src="js/lib/jquery3.2.1.min.js"></script>
        <script type="text/javascript" src="js/lib/handlebars-latest.js"></script>
        <script type="text/javascript" src="js/lib/moment.js"></script>
        <script type="text/javascript" src="js/lib/jquery.modal.window.js"></script>
        <script type="text/javascript" src="js/lib/jquery.ajax.inputs.js?1337"></script>
        <script type="text/javascript" src="js/lib/jquery.dropzone.js"></script>
        <script type="text/javascript" src="js/modules/module.calendar.js?1337"></script>
        <script type="text/javascript" src="js/loginform.js"></script>
        <script type="text/javascript" src="js/calendar.js?1337"></script>
        <script type="text/javascript">
            calendar = new Calendar("<?=$result['name'];?>", $("#calendar"));
            Calendar.editor = <?=($editor) ? "true" : "false";?>;
        </script>
    </head>
    <body>
    <table id="marking">
        <tr id="header">
            <td>
                <?php include_once "templates/header.php"?>
            </td>
        </tr>
        <tr id="notification">
            <td></td>
        </tr>
        <tr id="content">
            <td>
                <div id="calendar-block"></div>
            </td>
        </tr>
        <tr id="footer">
            <td>
                <?php include_once "templates/footer.php"; ?>
            </td>
        </tr>
    </table>
    </body>
</html>