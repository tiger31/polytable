<?php
namespace Configuration\Database;

    use Configuration\Database\Connection\Query;

    define("RETURN_FALSE_ON_EMPTY", 1);
    define("RETURN_TRUE_ON_EMPTY", 2);
    define("RETURN_IGNORE", 3);

    define("QUERY_GROUP_SELECT", "group_select");
    define("QUERY_GROUP_INSERT", "group_insert");
    define("QUERY_GROUP_UPDATE", "group_update");
    define("QUERY_GROUP_CHECK", "group_check");
    define("QUERY_USER_CHECK", "user_check");
    define("QUERY_USER_INSERT", "user_insert");
    define("QUERY_USER_UPDATE", "user_update");
    define("QUERY_USER_SELECT", "user_select");
    define("QUERY_EMAIL_SELECT", "email_select");
    define("QUERY_CONTRIBUTOR_SELECT", "contributor_select");
    define("QUERY_CONTRIBUTOR_CHECK", "contributor_check");
    define("QUERY_CONTRIBUTOR_INSERT", "contributor_insert");
    define("QUERY_CONTRIBUTOR_DELETE", "contributor_delete");
    define("QUERY_HOMEWORK_INSERT", "homework_insert");
    define("QUERY_HOMEWORK_CHECK", "homework_check");
    define("QUERY_HOMEWORK_SELECT", "homework_select");
    define("QUERY_HOMEWORK_UPDATE", "homework_update");
    define("QUERY_FILE_INSERT", "file_insert");
    define("QUERY_FILE_SELECT", "file_select");
    define("QUERY_FILE_UPLOAD", "file_upload");
    define("QUERY_CONFIRM_INSERT", "confirm_insert");
    define("QUERY_CONFIRM_SELECT", "confirm_select");
    define("QUERY_CONFIRM_DELETE", "confirm_remove");
    define("QUERY_CALENDAR_SELECT", "calendar_select");

    class Connection {
        private $mysqlPDO;
        private $queries = array();
        private $host;
        private $user;
        private $password;
        private $database;

        function __invoke($query_name, $type, array $values) {
            return $this->exec($query_name, $type, $values);
        }

        function __construct($host, $user, $password, $database, $options) {
            $this->host = $host;
            $this->user = $user;
            $this->password = $password;
            $this->database = $database;
            $this->mysqlPDO = new \PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password, $options);
        }

        function set_active(...$queries) {
            foreach ($queries as $query) {
                $file = $_SERVER['DOCUMENT_ROOT'] . "/modules/classes/" . str_replace('\\', "/", __NAMESPACE__) . "/query/" . $query . ".php";
                if (file_exists($file)) {
                    require_once $file;
                    if (isset($this->queries[$query]))
                        continue;
                    $this->queries[$query] = new $query();
                    $request = $this->queries[$query];
                    if ($request instanceof Query and $request->query === null) {
                        $request->prepare($this->mysqlPDO);
                    }
                } else {
                    throw new \RuntimeException("Class not found");
                }
            }
        }

        /**
         * Custom function for executing SQL queries
         * @param int $type
         * Type switches returns of function
         * = 1 Return bool false on empty else assoc array
         * = 2 Return bool true on empty else bool false
         * = 3 Return bool true if executed successfully else false
         * @param string $query_name
         * String key of array with queries
         * @param array $values
         * Values that needs to be send with query.
         * $values must be assoc array with same key as set in @func prepare
         * @return bool|array
         */
        function exec($query_name, $type, array $values) {
            $this->set_active($query_name);
            if (!isset($this->queries[$query_name])) {
                return false;
            } else {
                $query = $this->queries[$query_name];
                if ($query instanceof Query) {
                    //Execute query and get result depended on query type;
                    $result = $query->exec($values);
                    //If query failed
                    if (is_bool($result))
                        return $result;
                    //Return false on empty
                    switch ($type) {
                        case 1:
                        case 2:
                            if (is_array($result))
                                return $result; else {
                                if (is_int($result) && $result > 1)
                                    return ($type === 2) ? false : $result;
                                return ($type === 1) ? (bool)$result : !((bool)$result);
                            }
                            break;
                        case 3:
                            return $result;
                            break;
                    }
                    return false;
                }
                return false;
            }
        }

        function check_type($query) {
            $this->set_active($query);
            if (!isset($this->queries[$query])) {
                return false;
            } else {
                if ($this->queries[$query] instanceof Query) {
                    return $this->queries[$query]->type;
                } else
                    return false;
            }
        }

        function get_group($name) {
            $this->set_active(QUERY_GROUP_SELECT);
            return $this->exec(QUERY_GROUP_SELECT, RETURN_FALSE_ON_EMPTY, array("name" => $name));
        }

        function login_free($login) {
            $this->set_active(QUERY_USER_CHECK);
            $data = array("login" => $login);
            $login_free = $this->exec(QUERY_USER_CHECK, RETURN_TRUE_ON_EMPTY, $data);
            return $login_free;

        }

        function email_free($email) {
            return $this->exec(QUERY_USER_CHECK, RETURN_TRUE_ON_EMPTY, array("email" => $email));
        }

        function group_free($name, $id = null) {
            $this->set_active(QUERY_GROUP_CHECK);
            $data = array("name" => $name, "id" => ($id === null) ? "0" : $id);
            return $this->exec(QUERY_GROUP_CHECK, RETURN_TRUE_ON_EMPTY, $data);
        }

        function multiple_insert($dataVals) {
            $dataToInsert = array();
            $colNames = array("group_id", "day", "weekday", "lesson", "subject", "type", "time_start", "time_end", "teachers", "places");
            foreach ($dataVals as $row => $data) {
                foreach ($data as $val) {
                    $dataToInsert[] = $val;
                }
            }

            // (optional) setup the ON DUPLICATE column names
            $updateCols = array();

            foreach (array("subject", "type", "time_start", "time_end", "teachers", "places") as $curCol) {
                $updateCols[] = $curCol . " = VALUES($curCol)";
            }

            $onDup = implode(', ', $updateCols);

            // setup the placeholders - a fancy way to make the long "(?, ?, ?)..." string
            $rowPlaces = '(' . implode(', ', array_fill(0, count($colNames), '?')) . ')';
            $allPlaces = implode(', ', array_fill(0, count($dataVals), $rowPlaces));

            $sql = "INSERT INTO calendar (" . implode(', ', $colNames) . ") VALUES " . $allPlaces . " ON DUPLICATE KEY UPDATE $onDup";
            // and then the PHP PDO boilerplate
            $stmt = $this->mysqlPDO->prepare($sql);

            try {
                $stmt->execute($dataToInsert);
            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        }

        function __destruct() {
            // TODO: Implement __destruct() method.
            $this->mysqlPDO = null;
        }
    }