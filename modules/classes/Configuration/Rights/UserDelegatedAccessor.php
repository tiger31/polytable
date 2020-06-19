<?php

namespace Configuration\Rights;

use User\User;

trait UserDelegatedAccessor {
    public function contract() {
        if ($this instanceof IAccessor) {
            //Trait should be used by class implemented from Accessor or extended from any Accessor child,
            if (User::$user)
                $this->accept(User::$user->delegate());
        }
    }
}
