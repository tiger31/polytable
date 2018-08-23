<?php

namespace Configuration\Rights;

abstract class IAccessor {

    protected $accessor;

    public function delegate() {
        $trace = debug_backtrace();
        $caller = ($trace[1]) ? $trace[1]['object'] : null;
        if ($caller && count(array_intersect(array_values(class_uses_deep($caller)), $this->accessor->friends)) > 0) {
            return $this->accessor;
        }
        return null;
    }

    public function accept(Accessor $accessor) {
        $this->accessor = $accessor;
    }

    public function compare(IAccessor $accessor) {
        return $accessor->accessor === $this->accessor;
    }
    public function have_access($cond) {
        return $this->accessor->have_access($cond);
    }
}
