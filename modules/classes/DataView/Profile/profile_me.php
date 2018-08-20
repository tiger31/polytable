<?php

namespace DataView\Profile;

use Configuration\Database\Interceptor;
use DataView\Module;
use User\User;

class profile_me extends Module {
    public function __construct(Interceptor $mysql, User $user) {
        parent::__construct($mysql, $user);
        $this->name = "me";
        $this->data = array(
            "id" => $user['id'],
            "login" => $user['login'],
            "title" => $user->getPost(),
            "group" => $user['group'],
            "email" => $user['email'],
            "vk" => $user['vk_id'],
            "number" => $user['number'],
            "password_changed" => $user['password_changed']
        );
    }
}