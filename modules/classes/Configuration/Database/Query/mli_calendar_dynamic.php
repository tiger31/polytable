<?php
namespace Configuration\Database\Query;

use Configuration\Database\Connection\MultilineInsert;

class mli_calendar_dynamic extends MultilineInsert {
    function prepare(\PDO $mysql) {
        $this->mysql = $mysql;
        $this->table = "calendar_dynamic";
        $this->columns = array("id", "group_id", "day" ,"weekday", "lesson", "is_odd" ,"subject", "type", "time_start", "time_end", "teachers", "places", "action", "chain");
        $this->updateColumns = array("group_id", "day" ,"weekday", "lesson", "subject", "type", "time_start", "time_end", "teachers", "places", "action", "chain");
    }
}
