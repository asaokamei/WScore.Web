<?php
namespace WScore\tests\Authenticate\Mocks;

use WScore\Web\Authenticate\AuthUserInterface;

class UserNotFound implements AuthUserInterface
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

