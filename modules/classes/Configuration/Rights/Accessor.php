<?php

namespace Configuration\Rights;


class Accessor {
    protected $rights;
    protected $friends;

    public function  __construct($mask, $group) {
        $this->rights = new AccessMask($mask, $group);
    }

    public function have_access($cond) {
        return $this->rights->compare($cond);
    }
    public function delegate() {
        $trace = debug_backtrace();
        $caller = ($trace[1]) ? $trace[1]['object'] : null;
        if ($caller && count(array_intersect(array_values(class_uses_deep($caller)), $this->friends)) > 0) {
            return $this->rights;
        }
        return null;
    }
}