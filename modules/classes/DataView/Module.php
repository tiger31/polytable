<?php

namespace DataView;

use Configuration\Database\Interceptor;
use Configuration\Rights\Accessor\MaskAccessor;
use Configuration\Rights\UserDelegatedAccessor;
use User\User;

class Module {
    use UserDelegatedAccessor;

    private $mysql;
    protected $user;
    protected $access_mask;
    protected $access_group;
    protected $data = array();

    public $name;

    public function __construct(Interceptor $mysql, User $user) {
        $this->mysql = $mysql;
        $this->user = $user;
        $this->delegate(); //Delegate from UserDelegatedAccessor calls parent::__construct sp ignore warning
    }

    public function get_data() {
        return $this->data;
    }
}