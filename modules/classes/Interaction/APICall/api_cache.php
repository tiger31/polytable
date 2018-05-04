<?php

namespace Interaction\APICall;
use Interaction\APICall;

class api_cache extends APICall {
    function __construct($config) {
        $this->name = "cache";
        $this->fields = json_decode('[{"name":"group","isset":true,"regex":true}]', true);
        $this->bit_mask = 2048;
        $this->user_needed = true;
        $this->force_csrf_check = false;
        $this->method = "POST";
        $this->input = $_POST;

        parent::__construct($config);
    }

    function handle() {
        global $mysql;
        include_once  $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/Cache/TimetableCache.php";
        $group = $mysql(QUERY_GROUP_SELECT, RETURN_FALSE_ON_EMPTY, array("name" => $this->input['group']));
        if ($group["recache_count"] > 0 && $group["cache"] == 1) {
            if (cacheData($this->user->group_id)) {
                $group = $mysql->exec(QUERY_GROUP_SELECT, RETURN_FALSE_ON_EMPTY, array("name" => $this->user->group));
                $cache_date = ($group['cache_last'] != null) ? new \DateTime($group['cache_last']) : null;
                $cached_last = ($cache_date !== null) ? $cache_date->format("d/m H:i") : "никогда";
                $cache_until = ($group['cache_until'] != null) ? new \DateTime($group['cache_until']) : null;
                $cached_until = ($cache_until !== null) ? $cache_until->format("d/m/Y") . " (осталось " . $cache_until->diff(new DateTime("now"))->format("%a") . " дней)" : "кэш отсутствует";
                $this->response->response($mysql->exec(QUERY_GROUP_UPDATE, RETURN_IGNORE, array("name" => $group["name"], "count" => $group["recache_count"] - 1)), array("info" => array("left" => $group["recache_count"] - 1, "cached_last" => $cached_last, "cached_until" => $cached_until)));
            } else {
                $this->response->error(500, array("info" => "Сервера расписания не отвечают"))->response();
            }
        } else {
            $this->response->response();
        }
    }
}
