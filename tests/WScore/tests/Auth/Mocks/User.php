<?php
namespace WScore\tests\Auth\Mocks;

use WScore\Web\Auth\UserInterface;

class User implements UserInterface
{
    public $id;
    public function setLoginId( $id ) {
        $this->id = $id;
    }

    public function getLoginPw( $id ) {
        if( !$this->id ) return false;
        return crypt( $this->id . '-password' );
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

