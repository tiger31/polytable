<?php

namespace DataView;

use Configuration\Rights\AccessMask;
use Configuration\Rights\Accessor;

class DataField {

    private $owner;
    private $value;
    private $visibility;
    private $change;

    public function __construct($value, $visibility = AccessMask::PRIVATE, $change = AccessMask::PRIVATE) {
        $trace = debug_backtrace();
        if (!$trace[1] || !($trace[1]['object'] instanceof Accessor))
            throw new \Exception("Initializing DataField without an Accessor is not allowed");
        $this->owner = $trace[1]['object'];
        $this->value = $value;
        $this->visibility = $visibility;
        $this->change = $change;
    }

    public function get() {
        $trace = end(debug_backtrace());
        $caller =  $trace['object'] ? $trace['object'] : null;

        if (!($caller instanceof Accessor))
            return null;
        if ($this->visibility === AccessMask::PRIVATE) {
            if ($this->owner === $caller)
                return $this->value;
            return null;
        } else if ($caller->have_access($this->visibility)) {
            return $this->value;
        }
        return null;
    }

    public function set($value) {
        $trace = debug_backtrace();
        $caller = ($trace[1]) ? $trace[1]['object'] : null;
        if (!($caller instanceof Accessor))
            return null;
        if ($this->visibility === AccessMask::PRIVATE) {
            if ($this->owner === $caller)
                return $this->value = $value;
            return null;
        } else if ($caller->have_access($this->change))
            return $this->value = $value;
        return null;
    }

}