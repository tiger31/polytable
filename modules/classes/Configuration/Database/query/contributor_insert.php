<?php
use Configuration\Database\Connection\Query;
class contributor_insert extends Query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("INSERT INTO contributors (user_id, group_id) VALUE (:user_id, :group_id)");
        $this->type = Query::INSERT;
    }
}
