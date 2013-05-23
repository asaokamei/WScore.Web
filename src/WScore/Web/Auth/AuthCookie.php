<?php
namespace WScore\Web\Auth;

class AuthCookie
{
    public $cookie_id = 'Auth.Cookie';

    public $data = null;

    public $secret = null;

    public $save_days = 180; // save for 180 days.
    /**
     * @param null|string  $secret
     * @param null|string  $data
     */
    public function __construct( $secret=null, $data=null)
    {
        if( $secret ) {
            $this->secret = $secret;
        } elseif( $_SERVER[ 'HTTP_USER_AGENT' ] ) {
            $this->secret = $_SERVER[ 'HTTP_USER_AGENT' ];
        }
        if( $data ) {
            $this->data = $data;
        } elseif( isset( $_COOKIE[ $this->cookie_id ] ) ) {
            $this->data = $_COOKIE[ $this->cookie_id ];
        }
    }

    protected function getToken( $id ) {
        return sha1( $this->secret . $id . __FILE__ );
    }

    public function save( $id )
    {
        $save_time = 60 * 60 * 24 * $this->save_days; // save for 30 days
        $token     = $this->getToken( $id );
        $ck_save = array(
            "id" => $id,
            "pass" => $token
        );
        $cookie = serialize( $ck_save );
        setcookie( $this->cookie_id, $cookie, time()+$save_time );
    }

    public function getLoginId()
    {
        if( isset( $this->data ) )
        {
            $cookie  = unserialize( $this->data );
            if( isset( $cookie[ 'id' ] ) ) {
                $token = $this->getToken( $cookie[ 'id' ] );
                if( $cookie["pass"] === $token ) {
                    return $cookie[ 'id' ];
                }
            }
        }
        return false;
    }

    public function logout() {
        setcookie( $this->cookie_id, null, time() - (3600*24) );
    }
}

