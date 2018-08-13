<?php
namespace DataView;

use User\User;
use Configuration\Database\Interceptor;
use Configuration\Database\Connection\Query;

    class Profile {
        private $modules = array("profile_me", "profile_group");
        private $data = array();
        public $mysql;

        public function __construct() {
            global $mysql;
            $this->mysql = new Interceptor($mysql, array(Query::SELECT, Query::COUNT));
            foreach ($this->modules as $module) {
                $class = __CLASS__ . "\\" . $module;
                $m = new $class($this->mysql, User::$user);
                $this->data[$m->name] = $m->get_data();
            }
        }

        public function get_data() {
            return $this->data;
        }

    }


