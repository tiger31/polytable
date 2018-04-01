<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php";
include_once $local_modules_path . "/classes/User.php";
include_once $local_modules_path . "/Connect.php";

class Profile {
    private $modules = array();
    private $loader_state = null;
    private $user;

    function __construct(User $user) {
        $this->user = $user;
        switch ($user->privileges) {
            case RIGHTS['USER']:
                $this->load(USER_MODULES['default']['modules'], "default");
                break;
            case RIGHTS['MODERATOR']:
                $this->loader_state = "mod";
                $this->load(USER_MODULES['default']['modules'], "default");
                $this->load(USER_MODULES['mod']['modules'], "mod");
                break;
            case RIGHTS['ADMINISTRATOR']:
                $this->loader_state = "adm";
                $this->load(USER_MODULES['default']['modules'], "default");
                $this->load(USER_MODULES['mod']['modules'], "mod");
                $this->load(USER_MODULES['adm']['modules'], "adm");
                break;
        }
        $this->loader_state = "group";
        $this->load(USER_MODULES['group']['modules'], "group");
        if ($user->is_head)
            $this->load(USER_MODULES['head']['modules'], "head");
    }

    private function load($modules, $state) {
        $this->loader_state = $state;
        foreach ((array)$modules as $key => $value) {
            if ($this->__autoload($key)) {
                $module = new $key($this->user, $value);
                array_push($this->modules, $module);
            }
        }
    }

    private function __autoload($class_name) {
        $file_path = $_SERVER['DOCUMENT_ROOT'] . "/modules/user_activity/" . $this->loader_state . "/" . $class_name . ".php";
        if (file_exists($file_path)) {
            require_once $file_path;
            return true;
        }
        return false;
    }

    public function template_modules() {
        foreach ($this->modules as $module) {
            if ($module->is_active()) $module->template();
        }
    }
    public function template_grid() {
        foreach ($this->modules as $module) {
            if ($module->is_active()) $module->template_grid();
        }
    }
    public function template_menu() {
        include_once $_SERVER['DOCUMENT_ROOT'] . "/templates/profile/menu.php";
    }
}