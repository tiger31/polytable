<?php
include_once "modules/Connect.php";
header("Content-type text/json");
$mysql->set_active(QUERY_GROUP_SELECT);

$result = $mysql->exec(QUERY_GROUP_SELECT, RETURN_FALSE_ON_EMPTY, array("signature"=>$_GET['query']));
echo (json_encode((count($result) === 1) ? array($result) : $result));
