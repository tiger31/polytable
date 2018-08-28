<?php

namespace DataView\Profile;

use Configuration\Database\Interceptor;
use DataView\Module;
use User\User;

class profile_group extends Module {
    public function __construct(Interceptor $mysql, User $user) {
        parent::__construct($mysql, $user);
        $group = $user['group'];
        $users = $mysql(QUERY_USER_SELECT, RETURN_FALSE_ON_EMPTY, array("group" => $group));
        $mates = array();
        foreach ($users as $id) {
            $tmp = $mysql(QUERY_USER_SELECT, RETURN_FALSE_ON_EMPTY, array("id" => $id['id']));
            $usr = new User($tmp);
            $res = array(
                "id" => $usr['id'],
                "login" => $usr['login'],
                "title" => $usr->getPost(),
                "group" => $usr['group'],
                "email" => $usr['email'],
                "vk" => $usr['vk_id'],
                "number" => $usr['number']
            );
            array_push($mates, $res);
        }
        $this->name = "group";
        $this->data = $mates;
    }
}