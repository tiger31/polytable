<?php

abstract class db_query {
    public $query;
    public $is_multiple = false;
    public $multiple = array();
    public $type;

    const SELECT = 1;
    const INSERT = 2;
    const UPDATE = 3;
    const COUNT = 4;
    const DELETE = 5;


    abstract function prepare(PDO $mysql);

    /**
     * @param $values
     * @return bool|array|Exception
     */
    function exec($values) {
        $query = $this->query;
        if ($this->is_multiple)
            foreach ($this->multiple as $statement) {
                if (array_keys($values) == $statement["keys"]) {
                    $query = $statement["query"];
                    break;
                }
            }
        if ($query instanceof PDOStatement) {
            switch ($this->type) {
                case 1:
                    $query->execute($values);
                    return ($query->rowCount() == 1) ? $query->fetch() : $query->fetchAll();
                    break;
                case 4:
                    $query->execute($values);
                    return (int)$query->fetchColumn();
                    break;
                case 2:
                case 3:
                case 5:
                    return $query->execute($values);
                    break;
            }
            return false;
        }
        else return false;
    }

}