<?php set_time_limit(0);
include_once "Connect.php";
    include_once "TimetableCache.php";
    global $mysql;
    $mysql->set_active(QUERY_GROUP_UPDATE);
    cacheAll();
    $mysql(QUERY_GROUP_UPDATE, RETURN_IGNORE, array());
