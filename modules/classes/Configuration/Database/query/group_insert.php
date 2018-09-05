<?php
namespace Configuration\Database\Query;

use Configuration\Database\Connection\Query;
class group_insert extends Query {
    function prepare(\PDO $mysql) {
        $this->query = $mysql->prepare("INSERT INTO groups (`name`, id, university_id, `year`, faculty_id, faculty_name, faculty_abbr) VALUES (:name, :id, :university_id, :year, :faculty_id, :faculty_name, :faculty_abbr)");
        $this->type = Query::INSERT;
    }
};