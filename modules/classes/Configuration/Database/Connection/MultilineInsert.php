<?php
namespace Configuration\Database\Connection;

abstract class MultilineInsert extends Query {

    protected $columns = array();
    protected $updateColumns = array();
    protected $table;
    protected $mysql;

    public function exec($values) {
        $dataToInsert = array();
        foreach ($values as $row => $data) {
            foreach ($this->columns as $column)
                $dataToInsert[] = $data[$column];
        }

        // (optional) setup the ON DUPLICATE column names
        $updateCols = array();

        foreach ($this->updateColumns as $curCol) {
            $updateCols[] = $curCol . " = VALUES($curCol)";
        }

        $onDup = implode(', ', $updateCols);

        // setup the placeholders - a fancy way to make the long "(?, ?, ?)..." string
        $rowPlaces = '(' . implode(', ', array_fill(0, count($this->columns), '?')) . ')';
        $allPlaces = implode(', ', array_fill(0, count($values), $rowPlaces));

        $sql = "INSERT INTO " . $this->table . " (" . implode(', ', $this->columns) . ") VALUES " . $allPlaces . " ON DUPLICATE KEY UPDATE $onDup";
        // and then the PHP PDO boilerplate
        $stmt = $this->mysql->prepare($sql);

        return $stmt->execute($dataToInsert);
    }
}
