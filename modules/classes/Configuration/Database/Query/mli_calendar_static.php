<?php
namespace Configuration\Database\Query;

use Configuration\Database\Connection\MultilineInsert;

class mli_calendar_static extends MultilineInsert {
    function prepare(\PDO $mysql) {
        $this->mysql = $mysql;
        $this->table = "calendar_static";
        $this->columns = array("id", "group_id", "weekday", "lesson", "is_odd" ,"subject", "type", "time_start", "time_end", "teachers", "places");
        $this->updateColumns = array("weekday", "lesson", "is_odd", "subject", "type", "time_start", "time_end", "teachers", "places");
    }
}
