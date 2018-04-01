<?php

class file_insert extends db_query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("INSERT INTO uploads (name, original_name, showable, adder_id ,stored_untill)
            VALUES (:name, :original_name, :showable, :adder_id ,:stored_untill)");
        $this->type = db_query::INSERT;
    }
}