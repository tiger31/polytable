<?php

class contributor_insert extends db_query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("INSERT INTO contributors (user_id, group_id) VALUE (:user_id, :group_id)");
        $this->type = db_query::INSERT;
    }
}
