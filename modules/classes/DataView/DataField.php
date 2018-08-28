<?php

namespace DataView;

use Configuration\Rights\AccessMask;
use Configuration\Rights\IAccessor;

class DataField {

    private $owner;
    private $value;
    private $visibility;
    private $change;

    public function __construct($value, $visibility = AccessMask::PRIVATE, $change = AccessMask::PRIVATE) {
        $trace = debug_backtrace();
        if (!$trace[1] || !in_array('DataView\DataGroup', class_uses_deep($trace[1]['object'])))
            throw new \Exception("Initializing DataField without an Accessor is not allowed");
        $this->owner = $trace[1]['object'];
        $this->value = $value;
        $this->visibility = $visibility;
        $this->change = $change;
    }

    public function get() {
        $trace = debug_backtrace();
        $caller = ($trace[2] && isset($trace[2]['object'])) ? $trace[2]['object'] : null;
        if (!($caller instanceof IAccessor)) {
            if ($this->visibility == AccessMask::PUBLIC)
                return $this->value;
            return null;
        }
        if ($this->visibility === AccessMask::PRIVATE) {
            if ($this->owner instanceof IAccessor && $this->owner->compare($caller))
                return $this->value;
            return null;
        } else if ($caller->have_access($this->visibility)) {
            return $this->value;
        }
        return null;
    }

    public function set($value) {
        $trace = debug_backtrace();
        $caller = ($trace[2] && isset($trace[2]['object'])) ? $trace[2]['object'] : null;
        if (!($caller instanceof IAccessor)) {
            if ($this->change == AccessMask::PUBLIC)
                return $this->value = $value;
            return null;
        }
        if ($this->visibility === AccessMask::PRIVATE) {
            if ($this->owner instanceof IAccessor && $this->owner->compare($caller))
                return $this->value = $value;
            return null;
        } else if ($caller->have_access($this->change))
            return $this->value = $value;
        return null;
    }

}