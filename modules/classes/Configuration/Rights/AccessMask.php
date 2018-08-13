<?php declare(strict_types=1);

namespace Configuration\Rights;

    class AccessMask {

        const PUBLIC = 0;
        const PRIVATE = -1;

        private $rights_mask;
        private $rights_group;
        private $rights_arr = array();

        public function __construct(int $rights, $rights_group = RightsGroup::CONSUMER) {
            $this->rights_mask = $rights;
            $this->rights_group = $rights_group;
            $this->rights_arr = self::apply_group(self::mask_to_arr($rights), $rights_group);
        }

        public function __destruct() {
            $this->rights_group = null;
            $this->rights_arr = null;
            $this->rights_mask = null;
        }

        public function toInt() {
            $value = 0;
            $arr = array();
            for ($i = 0; $i < count(AccessController::$rights_arr); $i++) {
                array_push($arr, $this->rights_arr[$i]);
                if ((bool)$this->rights_arr[$i] && AccessController::$rights_arr[$i]->power !== [])
                    foreach (AccessController::$rights_arr[$i]->power as $index)
                        $arr[$index] = 0;
            }
            for ($i = count(AccessController::$rights_arr) - 1; $i >= 0; $i--)
                $value = (int)$value * 2 + $arr[$i];
            return $value;
        }

        public function explain() {
            for ($i = 0; $i < count(AccessController::$rights_arr); $i++) {
                echo AccessController::$rights_arr[$i]->name . "(" . AccessController::$rights_arr[$i]->group . ") - " . (((bool)$this->rights_arr[$i]) ? "true<br>" : "false<br>");
            }
        }

        public function compare($mask) {
            if ($mask === -1) return false;
            $arr = self::mask_to_arr($mask);
            for ($i = 0; $i < count(AccessController::$rights_arr); $i++)
                if ($arr[$i] > $this->rights_arr[$i]) {
                    return false;
                }
            return true;
        }

        public function has(int $right) {
            return (bool)$this->rights_arr[$right];
        }

        public function group() {
            return $this->rights_group;
        }

        public static function mask_to_arr(int $mask) {
            $arr = array();
            for ($i = 0; $i < count(AccessController::$rights_arr); $i++) {
                array_push($arr, $mask % 2);
                if ((bool)$arr[$i] && AccessController::$rights_arr[$i]->power !== [])
                    foreach (AccessController::$rights_arr[$i]->power as $index)
                        $arr[$index] = 1;
                $mask = (int)$mask / 2;
            }
            return $arr;
        }

        public static function explain_mask($mask, $group = RightsGroup::CONSUMER) {
            $to_explain = new AccessMask($mask, $group);
            $to_explain->explain();
            $to_explain->__destruct();
        }

        public static function apply_group($arr, $group) {
            $mask_value = $group;
            if ($group !== RightsGroup::CONSUMER)
                for ($i = 0; $i < count(AccessController::$rights_arr); $i++) {
                    $param_value = AccessController::$rights_arr[$i]->group;
                    if ((bool)$arr[$i] && $mask_value < $param_value) {
                        $arr[$i] = 0;
                        if (AccessController::$rights_arr[$i]->power !== [])
                            foreach (AccessController::$rights_arr[$i]->power as $index)
                                $arr[$index] = 0;
                    }
                }
            return $arr;
        }
}

