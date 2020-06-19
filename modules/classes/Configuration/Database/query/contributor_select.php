<?php
namespace Configuration\Database\Query;

use Configuration\Database\Connection\Query;
class contributor_select extends Query {
    function prepare(\PDO $mysql) {
       $this->query = $mysql->prepare("SELECT * FROM contributors WHERE group_id=:group_id ");
       $this->type = Query::SELECT;
    }
}