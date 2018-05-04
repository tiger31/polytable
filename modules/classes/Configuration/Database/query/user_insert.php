<?php
use Configuration\Database\Connection\Query;
class user_insert extends Query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("INSERT INTO users 
                (login, email, `group`, password_hash)
                 VALUES (:login, :email, :group, :password_hash)");
        $this->type = Query::INSERT;
    }
};