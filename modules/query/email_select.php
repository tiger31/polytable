<?php

class email_select extends db_query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("SELECT * FROM email_services WHERE `domain`=:url");
        $this->type = db_query::SELECT;
    }
}