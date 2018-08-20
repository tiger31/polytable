<?php

namespace DataView\DataViewAccessor;

use Configuration\Rights\Accessor;
use DataView\DataGroup;

class MaskViewAccessor extends Accessor\MaskAccessor implements Accessor, \ArrayAccess {
    use DataGroup;
}