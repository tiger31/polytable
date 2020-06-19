<?php declare(strict_types=1);

namespace DataView;

trait DataGroup {

    protected $access_data;

    public function offsetExists($offset) {
        // TODO: Implement offsetExists() method.
    }

    public function offsetGet($offset) {
        return $this->access_data[$offset]->get();
    }

    public function offsetSet($offset, $value) {
        $this->access_data[$offset]->set($value);
    }

    public function offsetUnset($offset) {
        unset($this->access_data[$offset]);
    }

    public function bind($offset, DataField $value) {
        $trace = debug_backtrace();
        $caller = ($trace[1]) ? $trace[1]['object'] : null;
        $func = ($trace[1]) ? $trace[1]['function'] : null;
        if ($caller === $this && $func == "__construct") {
            $this->access_data[$offset] = $value;
        } else
            throw new \Exception("Trying to create bind-param from non-owner");
    }
}