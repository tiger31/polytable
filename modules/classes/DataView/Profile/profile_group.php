<?php

namespace DataView\Profile;

use Configuration\Database\Interceptor;
use DataView\Module;
use User\User;

class profile_group extends Module {
    public function __construct(Interceptor $mysql, User $user) {
        parent::__construct($mysql, $user);
        $this->name = "group";
        $this->data = array();
    }
}