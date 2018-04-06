<?php
include_once "Autoload_query.php";
$host = "127.0.0.1";
$database = "groups_sch";
$user = "root";
$password = "";

const RETURN_FALSE_ON_EMPTY = 1;
const RETURN_TRUE_ON_EMPTY = 2;
const RETURN_IGNORE = 3;

const QUERY_GROUP_SELECT = "group_select";
const QUERY_GROUP_INSERT = "group_insert";
const QUERY_GROUP_UPDATE = "group_update";
const QUERY_GROUP_CHECK = "group_check";
const QUERY_USER_CHECK = "user_check";
const QUERY_USER_INSERT = "user_insert";
const QUERY_USER_UPDATE = "user_update";
const QUERY_USER_SELECT = "user_select";
const QUERY_EMAIL_SELECT = "email_select";
const QUERY_CONTRIBUTOR_SELECT = "contributor_select";
const QUERY_CONTRIBUTOR_CHECK = "contributor_check";
const QUERY_CONTRIBUTOR_INSERT = "contributor_insert";
const QUERY_CONTRIBUTOR_DELETE = "contributor_delete";
const QUERY_HOMEWORK_INSERT = "homework_insert";
const QUERY_HOMEWORK_CHECK = "homework_check";
const QUERY_HOMEWORK_SELECT = "homework_select";
const QUERY_HOMEWORK_UPDATE = "homework_update";
const QUERY_FILE_INSERT = "file_insert";
const QUERY_FILE_SELECT = "file_select";
const QUERY_FILE_UPLOAD = "file_upload";
const QUERY_CONFIRM_INSERT = "confirm_insert";
const QUERY_CONFIRM_SELECT = "confirm_select";
const QUERY_CONFIRM_DELETE = "confirm_remove";
const QUERY_CALENDAR_SELECT = "calendar_select";

$options = [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION //DEBUG ONLY
];

$mysql = new db_interaction($host, $user, $password, $database, $options);

class db_interaction {
    public $mysqlPDO;
    private $queries = array();

    function __invoke($query_name, $type, array $values) {
        return $this->exec($query_name, $type, $values);
    }

    function __construct($host, $user, $password, $database, $options) {
        $this->mysqlPDO = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password, $options);
    }

    function set_active(...$queries) {
        foreach ($queries as $query) {
            if (class_exists($query)) {
                if (isset($this->queries[$query])) continue;
                $this->queries[$query] = new $query();
                $request = $this->queries[$query];
                if ($request instanceof db_query and $request->query === null) {
                    $request->prepare($this->mysqlPDO);
                }
            } else {
                throw new RuntimeException("Class not found");
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
        if (!isset($this->queries[$query_name])) {
            return false;
        } else {
            $query = $this->queries[$query_name];
            if ($query instanceof db_query) {
                //Execute query and get result depended on query type;
                $result = $query->exec($values);
                //If query failed
                if (is_bool($result)) return $result;
                //Return false on empty
                switch ($type) {
                    case 1:
                    case 2:
                        if (is_array($result))
                            return $result;
                        else {
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
        $data = array(
            "name" => $name,
            "id" => ($id === null) ? "0" : $id
        );
        return $this->exec(QUERY_GROUP_CHECK, RETURN_TRUE_ON_EMPTY, $data);
    }

    function multiple_insert($dataVals) {
        $dataToInsert = array();
        $colNames = array("group_id", "day", "weekday", "lesson", "subject", "type", "time_start", "time_end", "teachers", "places");
        foreach ($dataVals as $row => $data) {
            foreach($data as $val) {
                $dataToInsert[] = $val;
            }
        }

        // (optional) setup the ON DUPLICATE column names
        $updateCols = array();

        foreach ($colNames as $curCol) {
            $updateCols[] = $curCol . " = VALUES($curCol)";
        }

        $onDup = implode(', ', $updateCols);

        // setup the placeholders - a fancy way to make the long "(?, ?, ?)..." string
        $rowPlaces = '(' . implode(', ', array_fill(0, count($colNames), '?')) . ')';
        $allPlaces = implode(', ', array_fill(0, count($dataVals), $rowPlaces));

        $sql = "INSERT INTO calendar (" . implode(', ', $colNames) .
            ") VALUES " . $allPlaces . " ON DUPLICATE KEY UPDATE $onDup";
        // and then the PHP PDO boilerplate
        $stmt = $this->mysqlPDO->prepare ($sql);

        try {
            $stmt->execute($dataToInsert);
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    function __destruct() {
        // TODO: Implement __destruct() method.
        $this->mysqlPDO = null;
    }

}