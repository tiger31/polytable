<?php

class contributor_check extends db_query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("SELECT COUNT(user_id) FROM contributors WHERE group_id=:gr_id AND user_id=:id");
        $this->type = db_query::COUNT;
    }
}