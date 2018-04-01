<?php

class file_select extends db_query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("SELECT * FROM uploads WHERE name=:name");
        $this->type = db_query::SELECT;
    }
}