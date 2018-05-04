<?php

namespace Configuration\Rights;


class Accessor {
    protected $rights;

    public function __construct($mask, $group) {
        $this->rights = new AccessMask($mask, $group);
    }

    public function have_access($mask) {
        return $this->rights->compare($mask);
    }
}