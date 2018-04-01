<?php error_reporting(-1);
    include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
    include_once $local_modules_path . "/Connect.php";
    include_once $local_modules_path . "/Security.php";
    include_once $local_modules_path . "/Calendar.php";
    include_once $local_modules_path . "/classes/User.php";

    session_start();
    set_csrf_token();

    $mysql->set_active(QUERY_GROUP_SELECT, QUERY_USER_SELECT, QUERY_HOMEWORK_SELECT);

    //Data checks
    if (!isset($_GET['id']) or (isset($_GET['id']) and !preg_match(REGEX['group'], $_GET['id']))) {
        //TODO searching
        header("Location: " . $default_redirect);
        die();
    }

    $group_name = str_replace("_", "/", $_GET['id']);
    $result = $mysql->exec(QUERY_GROUP_SELECT, RETURN_FALSE_ON_EMPTY, array("name" => $group_name));

    //Request check
    if (!$result) {
        header("Location: " . $default_redirect);
        die();
    }
    $homework = $mysql->exec(QUERY_HOMEWORK_SELECT, RETURN_FALSE_ON_EMPTY, array("group_id" => $result['id']));
    $cache_last = ($result['cache_last'] != null) ? new DateTime($result['cache_last']) : null;

    $calendar = new Calendar(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/data/cache/" . $result['id']), $homework);

    session_check(true);

    $user = User::loadFromSession();
    $editor = ($user instanceof User) ? $user->group_editor() : false;

?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
        <title>PolyTable</title>
        <link rel="stylesheet" type="text/css" href="css/style.css"/>
        <link rel="stylesheet" type="text/css" href="css/dropzone.css"/>
        <script type="text/javascript" src="js/lib/jquery3.2.1.min.js"></script>
        <script type="text/javascript" src="js/lib/jquery.modal.window.js"></script>
        <script type="text/javascript" src="js/lib/jquery.ajax.inputs.js"></script>
        <script type="text/javascript" src="js/lib/jquery.dropzone.js"></script>
        <script type="text/javascript" src="js/loginform.js"></script>
        <script type="text/javascript" src="js/calendar.js"></script>
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
                <div id="calendar">
                    <?php if($calendar->shift() != 0): ?>
                        <?php for ($i = 0; $i < $calendar->shift(); $i++): ?>
                            <div class="day empty"></div>
                        <?php endfor; ?>
                    <?php endif; ?>
                    <?php foreach ($calendar->days() as $day)
                        $day->template($editor);
                    ?>
                </div>
                <div id="cached_last">
                    <?=($cache_last) ? "Последнее обновление информации: " . $cache_last->format("d.m.Y H:i") : "Данные не были получены"?>
                </div>
                <div id="day_lessons">
                    <?php foreach ($calendar->days() as $day): ?>
                        <?php if($day->has_cache): ?>
                            <div class="lessons_container <?=$day->date;?> <?=($day->is_today()) ? "active" : "";?>" date="<?=$day->date;?>">
                                <div class="day_title"><?= $day->print_lessons_info();?></div>
                                <div class="lessons">
                                    <?php foreach ($day->lessons as $lesson)
                                        $lesson->template($editor);
                                    ?>
                                </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
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