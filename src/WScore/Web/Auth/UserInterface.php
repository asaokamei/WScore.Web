<?php
namespace WScore\Web\Auth;

interface UserInterface
{
    public function setLoginId( $id );
    public function getLoginPw( $id );
    public function getUserInfo( $id );
}