<?php

namespace Configuration\Rights;

class Accessor {
    public $rights;
    public $friends;

    public function  __construct($mask, $group) {
        $this->rights = new AccessMask($mask, $group);
    }

    public function have_access($cond) {
        return $this->rights->compare($cond);
    }
}
