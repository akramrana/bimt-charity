<?php

namespace app\components;


class UserIdentity{

    const ROLE_SUPER_ADMIN = 1;
    const ROLE_ADMIN = 2;
    const ROLE_MODERATOR = 3;
    const ROLE_GENERAL_USER = 4;
    const ROLE_GUEST = 5;

    public function init(){
        
    }

    static public function isUserAuthenticate($userrole) {
        
        if ($userrole==self::ROLE_ADMIN) {
            return true;
        }
        else {
            return false;
        }
    }

}
