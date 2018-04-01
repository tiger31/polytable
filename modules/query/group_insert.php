<?php

class group_insert extends db_query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("INSERT INTO groups (name, id, university_id) VALUES (:name, :id, :university_id)");
        $this->type = db_query::INSERT;
    }
};