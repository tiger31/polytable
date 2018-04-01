<?php

class confirm_remove extends db_query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("DELETE FROM confirm_hash WHERE login=:login AND `for`=:type");
        $this->type = db_query::DELETE;
    }
}