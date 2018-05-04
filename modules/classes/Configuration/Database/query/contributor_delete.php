<?php
use Configuration\Database\Connection\Query;
class contributor_delete extends Query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("DELETE FROM contributors WHERE group_id=:group_id AND user_id=:user_id");
        $this->type = Query::DELETE;
    }
}