<?php

namespace Configuration\Rights;

use User\User;

trait UserDelegatedAccessor {
    public function delegate() {
        if ($this instanceof Accessor) {
            //Trait should be used by class implemented from Accessor or extended from any Accessor child,
            if (User::$user)
                parent::__construct(User::$user->delegate());
        }
    }
}
