<?php declare(strict_types=1);

namespace User;

    use Configuration\Rights\AccessMask;
    use Configuration\Rights\Accessor;
    use Configuration\Rights\IAccessor;
    use Configuration\Rights\RightsGroup;
    use DataView\DataField;
    use DataView\DataGroup;
    use Security\Shield;

    class User extends IAccessor implements \ArrayAccess {
        public $is_head;
        public $group_id;
        public $verified;

        use DataGroup;

        function __construct($data) {
            global $mysql;
            $this->is_head = ($data['is_head'] == 1) ? true : false;
            $this->group_id = $mysql->get_group($data['group'])['id'];
            $this->verified = (bool)$data['verified'];

            $this->accessor = new Accessor((int)$data['privileges'], (int)$data['rights_group']);
            $this->accessor->friends = ["Configuration\Rights\UserDelegatedAccessor"];

            $this->bind("id", new DataField($data['id'], AccessMask::PUBLIC));
            $this->bind("login", new DataField($data['login'], AccessMask::PUBLIC));
            $this->bind("group", new DataField($data['group'], AccessMask::PUBLIC));
            $this->bind("email", new DataField($data['email']));
            $this->bind("number", new DataField($data['number']));
            $this->bind("vk_id", new DataField($data['vk_id']));
            $this->bind("password_hash", new DataField($data['password_hash']));
            $this->bind("password_changed", new DataField($data['password_changed']));

        }

        function getEscapedName() {
            return Shield::escape_str($this['login']);
        }

        function getPost() {
            switch ($this->accessor->rights->group()) {
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
                return "Староста группы " . $this['group'];
            }
            return "";
        }

        function check_contributor() {
            global $mysql;
            return $mysql->exec(QUERY_CONTRIBUTOR_CHECK, RETURN_FALSE_ON_EMPTY, array("gr_id" => $this->group_id, "id" => $this['id']));
        }

        public function group_editor() {
            return ($this->accessor->rights->has(1));
        }

        static $user;
    }