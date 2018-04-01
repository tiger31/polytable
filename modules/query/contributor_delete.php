<?php

class contributor_delete extends db_query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("DELETE FROM contributors WHERE group_id=:group_id AND user_id=:user_id");
        $this->type = db_query::DELETE;
    }
}