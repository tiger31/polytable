<?php

class contributor_select extends db_query {
    function prepare(PDO $mysql) {
       $this->query = $mysql->prepare("SELECT * FROM contributors WHERE group_id=:group_id ");
       $this->type = db_query::SELECT;
    }
}