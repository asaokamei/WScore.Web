<?php
namespace WScore\tests\Auth\Mocks;

use WScore\Web\Auth\UserInterface;

class UserNotFound implements UserInterface
{
    public function setLoginId( $id ) {
        return false;
    }

    public function getLoginPw( $id ) {
        return false;
    }

    public function getUserInfo( $id )
    {
        return false;
    }
}

