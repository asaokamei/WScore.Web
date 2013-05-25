<?php
namespace WScore\Web\Authenticate;

interface AuthInterface
{
    // columns for loginInfo array. 
    const USER_ID      = 'user_id';
    const PASSWORD     = 'password';
    const LOGIN_TIME   = 'login_time';
    const ACCESS_TIME  = 'access_time';
    const IS_LOGIN     = 'is_LoggedIn';
    const LOGIN_METHOD = 'method';
    
    // value for IS_LOGIN column. 
    const LOGIN_VALID     = 'loginOK';
    const LOGIN_STILL_RAW = 'still-raw';
    const LOGIN_TOKEN     = 'useToken';

    // value for LOGIN_METHOD
    const BY_POST_FORM = 'by-post';
    const BY_COOKIE    = 'by-cookie';
    const BY_LOGIN     = 'by-login';
}
