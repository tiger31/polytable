<?php

class user_insert extends db_query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("INSERT INTO users 
                (login, email, `group`, password_hash)
                 VALUES (:login, :email, :group, :password_hash)");
        $this->type = db_query::INSERT;
    }
};