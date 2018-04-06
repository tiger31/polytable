<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/APICall.php";

class cache extends APICall {
    function __construct($config) {
        parent::__construct($config);
    }

    function handle() {
        global $mysql;
        $this->pre_check();
        include_once  $_SERVER['DOCUMENT_ROOT'] . "/modules/TimetableCache.php";
        $group = $mysql->exec(QUERY_GROUP_SELECT, RETURN_FALSE_ON_EMPTY, array("name" => $this->user->group));
        if ($group["recache_count"] > 0) {
            if (cacheData($this->user->group_id)) {
                $group = $mysql->exec(QUERY_GROUP_SELECT, RETURN_FALSE_ON_EMPTY, array("name" => $this->user->group));
                $cache_date = ($group['cache_last'] != null) ? new DateTime($group['cache_last']) : null;
                $cached_last = ($cache_date !== null) ? $cache_date->format("d/m H:i") : "никогда";
                $cache_until = ($group['cache_until'] != null) ? new DateTime($group['cache_until']) : null;
                $cached_until = ($cache_until !== null) ? $cache_until->format("d/m/Y") . " (осталось " . $cache_until->diff(new DateTime("now"))->format("%a") . " дней)" : "кэш отсутствует";
                AjaxResponse::create()->response($mysql->exec(QUERY_GROUP_UPDATE, RETURN_IGNORE, array("name" => $group["name"], "count" => $group["recache_count"] - 1)), array("info" => array("left" => $group["recache_count"] - 1, "cached_last" => $cached_last, "cached_until" => $cached_until)));
            }
        } else {
            AjaxResponse::create()->response();
        }
    }
}
