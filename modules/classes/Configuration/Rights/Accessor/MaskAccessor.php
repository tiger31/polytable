<?php

namespace Configuration\Rights\Accessor;

use Configuration\Rights\Accessor;
use Configuration\Rights\AccessMask;

class MaskAccessor implements Accessor {
    protected $rights;

    public function __construct($mask, $group) {
        $this->rights = new AccessMask($mask, $group);
    }
    public function have_access($cond) {
        return $this->rights->compare($cond);
    }
}

