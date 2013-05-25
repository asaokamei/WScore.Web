<?php
namespace WScore\Web\Authenticate;

interface AuthStorageInterface 
{
    public function loadLogin();
    public function saveLogin( $loginInfo );
    public function logout();
}
