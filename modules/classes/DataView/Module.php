<?php

namespace DataView;

use Configuration\Database\Interceptor;
use User\User;

class Module {
    private $mysql;
    protected $user;
    protected $access_mask;
    protected $access_group;
    protected $data = array();

    public $name;

    public function __construct(Interceptor $mysql, User $user) {
        $this->mysql = $mysql;
        $this->user = $user;
    }

    public function get_data() {
        return $this->data;
    }
}