<?php
use Configuration\Database\Connection\Query;
class file_insert extends Query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("INSERT INTO uploads (name, original_name, showable, size, hash, adder_id, stored_untill)
            VALUES (:name, :original_name, :showable, :size, :hash, :adder_id ,:stored_untill)");
        $this->type = Query::INSERT;
    }
}