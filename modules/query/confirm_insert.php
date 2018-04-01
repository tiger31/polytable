<?php

class confirm_insert extends db_query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("INSERT INTO confirm_hash (login, `for`, `value`, lifetime) VALUES (:login, :for, :value, :lifetime)");
        $this->type = db_query::INSERT;
    }
}