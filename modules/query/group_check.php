<?php

class group_check extends db_query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("SELECT COUNT(id) FROM groups WHERE id=:id OR name=:name");
        $this->type = db_query::COUNT;
    }
};