<?php
use Configuration\Database\Connection\Query;
class email_select extends Query {
    function prepare(PDO $mysql) {
        $this->query = $mysql->prepare("SELECT * FROM email_services WHERE `domain`=:url");
        $this->type = Query::SELECT;
    }
}