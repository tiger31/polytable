<?php
namespace Configuration\Database\Connection;

    abstract class Query {
        public $query;
        public $is_multiple = false;
        public $multiple = array();
        public $type;

        const SELECT = 1;
        const INSERT = 2;
        const UPDATE = 3;
        const COUNT = 4;
        const DELETE = 5;


        abstract function prepare(\PDO $mysql);

        /**
         * @param $values
         * @return bool|array|\Exception
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
            if ($query instanceof \PDOStatement) {
                switch ($this->type) {
                    case Query::SELECT:
                        $query->execute($values);
                        return ($query->rowCount() == 1) ? $query->fetch() : $query->fetchAll();
                        break;
                    case Query::COUNT:
                        $query->execute($values);
                        return (int)$query->fetchColumn();
                        break;
                    case Query::INSERT:
                    case Query::UPDATE:
                    case Query::DELETE:
                        return $query->execute($values);
                        break;
                }
                return false;
            } else return false;
        }
    }