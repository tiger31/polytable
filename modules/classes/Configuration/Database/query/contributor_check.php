<?php
use Configuration\Database\Connection\Query;
class contributor_check extends Query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("SELECT COUNT(user_id) FROM contributors WHERE group_id=:gr_id AND user_id=:id");
        $this->type = Query::COUNT;
    }
}