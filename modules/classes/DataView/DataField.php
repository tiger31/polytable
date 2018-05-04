<?php

namespace DataView;

use Configuration\Rights\AccessMask;
use Configuration\Rights\Accessor;

class DataField {

    private $owner;
    private $value;
    private $visibility;
    private $locked;
    private $change;

    public function __construct($value, $visibility = AccessMask::PRIVATE, $locked = true, $change = AccessMask::PRIVATE) {
        $trace = debug_backtrace();
        if (!$trace[1] || !($trace[1]['object'] instanceof Accessor)) throw new \Exception("Initializing DataField without an Accessor is not allowed");
        $this->owner = $trace[1]['object'];
        $this->value = $value;
        $this->visibility = $visibility;
        $this->locked = $locked;
        $this->change = $change;
    }

    public function get() {
        $trace = debug_backtrace();
        $caller = ($trace[1]) ? $trace[1]['object'] : null;
        if ($this->visibility === AccessMask::PRIVATE) {
            if ($this->owner === $caller)
                return $this->value;
            return null;
        }
        return $this->value;
    }
}