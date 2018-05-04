<?php declare(strict_types=1);

namespace DataView;

class DataGroup {

    protected $fields = array();

    function __construct($data) {

    }

    function __get($name) {
        return $this->fields[$name]->value;
    }

    function __set($name, $value) {
        return $this->fields[$name]->values = $value;
    }
}