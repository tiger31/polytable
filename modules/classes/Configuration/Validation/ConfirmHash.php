<?php

namespace Configuration\Validation;

use Security\Shield;


class ConfirmHash {

    const ACCOUNT = "ACCOUNT";
    const PASSWORD = "PASSWORD";
    const EMAIL = "EMAIL";

    public $hash;
    private $target;
    private $user;
    private $lifetime; // 6 Hours as default
    private $data;

    private $stored = false;


    public function __construct($user, $target, $lifetime = 21600, $data = null) {
        $this->hash = md5(Shield::rnd_str(32) . time());
        $this->target = $target;
        $this->user = $user;
        $this->lifetime = new \DateTime();
        $this->lifetime->setTimestamp(time() + $lifetime);
        $this->data = $data;
    }

    public function set() {
        global $mysql;
        if (!$this->stored) {
            $result = $mysql(QUERY_CONFIRM_INSERT, RETURN_IGNORE, array(
                'login' => $this->user,
                'for' => $this->target,
                'value' => $this->hash,
                'stored' => (($this->data) ? $this->data : null),
                'lifetime' => $this->lifetime->format("Y-m-d H:i:s")));
            if ($result)
                $this->stored = true;
            return $result;
        }
        return false;
    }

}