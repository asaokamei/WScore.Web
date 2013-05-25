<?php
namespace WScore\Web\Authenticate;

interface AuthUserInterface
{
    public function setLoginId( $id );
    public function getLoginPw( $id );
    public function getUserInfo( $id );
}