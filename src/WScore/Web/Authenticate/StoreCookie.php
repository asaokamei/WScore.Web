<?php
namespace WScore\Web\Authenticate;

class StoreCookie implements AuthInterface, AuthStorageInterface
{
    /** @var string    */
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
    
    public function loadLogin()
    {
        if( !isset( $this->data ) ) {
            return null;
        }
        $cookie  = unserialize( $this->data );
        if( !isset( $cookie[ 'id' ] ) ) {
            return null;
        }
        $token = $this->getToken( $cookie[ 'id' ] );
        if( $cookie[ 'token' ] !== $token ) {
            return null;
        }
        $loginInfo = array(
            self::IS_LOGIN    => self::LOGIN_TOKEN,
            self::LOGIN_METHOD=> self::BY_COOKIE,
            self::USER_ID     => $cookie[ 'id' ],
            self::PASSWORD    => null,
            self::ACCESS_TIME => new \DateTime(),
            self::LOGIN_TIME  => new \DateTime(),
        );
        return $loginInfo;
    }

    public function saveLogin( $loginInfo )
    {
        $saved  = 60 * 60 * 24 * $this->save_days; // save for 30 days
        $id     = $loginInfo[ self::USER_ID ];
        $token  = $this->getToken( $id );
        $cookie = array(
            "id" => $id,
            "token" => $token
        );
        $cookie = serialize( $cookie );
        setcookie( $this->cookie_id, $cookie, time()+$saved );
    }

    protected function getToken( $id ) {
        return sha1( $this->secret . $id . __FILE__ );
    }

    public function logout()
    {
        setcookie( $this->cookie_id, null, time() - (3600*24) );
    }
}
