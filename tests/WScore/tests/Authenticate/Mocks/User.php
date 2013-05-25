<?php
namespace WScore\tests\Authenticate\Mocks;

use WScore\Web\Authenticate\AuthUserInterface;

class User implements AuthUserInterface
{
    public $id;
    public function setLoginId( $id ) {
        $this->id = $id;
    }

    public function getLoginPw( $id ) {
        return crypt( $id . '-password' );
    }

    public function getUserInfo( $id )
    {
        return array(
            'user_id' => $this->id,
            'password' => $this->getLoginPw( $id ),
            'info' => 'user-info',
        );
    }
}

