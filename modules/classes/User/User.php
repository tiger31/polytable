<?php declare(strict_types=1);

namespace User;

    use Configuration\Rights\Accessor;
    use Configuration\Rights\RightsGroup;
    use Security\Shield;

    class User extends Accessor {
        public $id;
        public $email;
        public $login;
        public $password_changed;
        public $password_hash;
        public $number;
        public $vk_id;
        public $is_head;
        public $is_contributor;
        public $group;
        public $group_id;
        public $verified;

        function __construct($data) {
            global $mysql;
            $this->id = $data['id'];
            $this->login = $data['login'];
            $this->number = $data['number'];
            $this->vk_id = $data['vk_id'];
            $this->email = $data["email"];
            $this->is_head = ($data['is_head'] == 1) ? true : false;
            $this->group = $data['group'];
            $this->group_id = $mysql->get_group($this->group)['id'];
            $this->verified = (bool)$data['verified'];
            $this->password_changed = $data['password_changed'];
            $this->password_hash = $data['password_hash'];

            parent::__construct((int)$data['privileges'], (int)$data['rights_group']);
        }



        function getID() {
            return $this->id;
        }

        function getEscapedName() {
            return Shield::escape_str($this->login);
        }

        function getPost() {
            switch ($this->rights->group()) {
                case RightsGroup::ADM:
                    return "Администратор";
                    break;
                case RightsGroup::MOD:
                    return "Модератор";
                    break;
                case RightsGroup::GROUP:
                    if ($this->is_head)
                        return "Староста";
                    return "Студент";
                    break;
            }
            return "";
        }

        function getFullPost() {
            if ($this->is_head) {
                return "Староста группы " . $this->group;
            }
            return "";
        }

        function check_contributor() {
            global $mysql;
            return $mysql->exec(QUERY_CONTRIBUTOR_CHECK, RETURN_FALSE_ON_EMPTY, array("gr_id" => $this->group_id, "id" => $this->id));
        }

        public function group_editor() {
            return ($this->is_head || $this->is_contributor);
        }

        static $user;
    }