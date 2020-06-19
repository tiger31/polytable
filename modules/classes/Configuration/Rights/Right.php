<?php

namespace Configuration\Rights;

class Right {

    public $name;
    public $index;
    public $group;
    public $power;
    public $lower;
    public $allocatable;
    public $description;

    public function __construct($name, $index, $group, $power = [], $lower = [], $allocatable = false, $description = null) {
        $this->name = $name;
        $this->index = $index;
        $this->group = $group;
        $this->power = $power;
        $this->lower = $lower;
        $this->allocatable = $allocatable;
        $this->description = $description;
    }
}