<?php

class file_upload extends db_query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("UPDATE uploads SET stored_untill=:stored_untill WHERE name=:name");
        $this->type = db_query::UPDATE;
    }
}