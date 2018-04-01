<?php error_reporting(-1);
    include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
    include_once $local_modules_path . "/Connect.php";
    include_once $local_modules_path . "/Security.php";
    include_once $local_modules_path . "/Calendar.php";
    include_once $local_modules_path . "/classes/User.php";

    global $mysql;
    $mysql->set_active(QUERY_GROUP_SELECT);

    session_start();
    set_csrf_token();
    session_check(true);

    //Data checks
    if (!isset($_GET['id']) or (isset($_GET['id']) and !preg_match(REGEX['group'], $_GET['id']))) {
        //TODO searching
        header("Location: " . $default_redirect);
        die();
    }
    $result = $mysql->exec(QUERY_GROUP_SELECT, RETURN_FALSE_ON_EMPTY, array("name" => $_GET['id']));
    if (!$result){
        header("Location: " . $default_redirect);
        die();
    }

    $user = User::loadFromSession();
    $editor = ($user instanceof User) ? $user->group_editor() : false;
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
        <title>PolyTable</title>
        <link rel="stylesheet" type="text/css" href="css/semantic.css">
        <link rel="stylesheet" type="text/css" href="css/style.css"/>
        <link rel="stylesheet" type="text/css" href="css/dropzone.css"/>
        <script type="text/javascript" src="js/lib/jquery3.2.1.min.js"></script>
        <script type="text/javascript" src="js/lib/handlebars-latest.js"></script>
        <script type="text/javascript" src="js/lib/moment.js"></script>
        <script type="text/javascript" src="js/lib/jquery.modal.window.js"></script>
        <script type="text/javascript" src="js/lib/jquery.ajax.inputs.js"></script>
        <script type="text/javascript" src="js/lib/jquery.dropzone.js"></script>
        <script type="text/javascript" src="js/lib/jquery.calendar.js"></script>
        <script type="text/javascript" src="js/loginform.js"></script>
        <script type="text/javascript" src="js/calendar.js"></script>
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
                <div id="group_number">Группа: <?=$_GET['id'];?></div>
                <div id="container">
                    <div class="arrow left">
                        <i class="ui icon angle left"></i>
                    </div>
                    <div id="calendar"></div>
                    <div class="arrow right">
                        <i class="ui icon angle right"></i>
                    </div>
                </div>
                <div id="cached_last">
                </div>
                <div id="day_lessons">

                </div>
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