<?php
include_once "modules/Config.php";
header("Content-type text/json");

$result = $mysql(QUERY_GROUP_SELECT, RETURN_FALSE_ON_EMPTY, array("signature"=>$_GET['query']));
echo (json_encode((is_assoc($result)) ? array($result) : $result));
