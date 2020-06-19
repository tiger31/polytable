<?php
namespace Configuration\Database;

class Interceptor {
    private $mysql;
    private $types;
    private $stored = array();

    public function __construct(Connection $mysql, array $types) {
        $this->mysql = $mysql;
        $this->types = $types;
    }

    public function __invoke($query, $return, $data) {
        return $this->intercept_query($query, $return, $data);
    }

    private function intercept_query($query, $return, $data) {
        $mysql = $this->mysql;
        $mysql->set_active($query);
        if (in_array($mysql->check_type($query), $this->types)) {
            $stored = $this->find_stored($query, $data);
            if (!$stored) {
                return $this->store($query, $data, $mysql($query, $return, $data));
            }
            return $stored;
        } else {
            throw new \Exception("This query is forbidden to execute in current context");
        }
    }
    private function find_stored($query, $data) {
        if (is_array($this->stored[$query]))
            foreach ($this->stored[$query] as $result)
                if ($result['data'] == $data)
                    return $result['result'];
        return false;
    }
    private function store($query, $data, $result) {
        if (!is_array($this->stored[$query]))
            $this->stored[$query] = array();
        array_push($this->stored[$query], array("data" => $data, "result" => $result));
        return $result;
    }
}