<?php
namespace WScore\Web\Auth;

/*
$auth = new Auth( $users );
$auth->loadInfo( $id, $pw );
try {
    
    $auth->getAuth();

}
catch( \Exception $e ) {
    
}
*/

class Auth
{
    const USER_ID      = 'user_id';
    const LOGIN_TIME   = 'login_time';
    const ACCESS_TIME  = 'access_time';
    const IS_LOGIN     = 'is_LoggedIn';
    
    /**
     * id string to save to session.
     *
     * @var string
     */
    public $auth_id = 'Auth.ID';

    /**
     * @var null
     */
    public $user_id = null;

    /**
     * @var null
     */
    public $password = null;

    /**
     * @var array
     */
    public $user_info = array();
    
    /**
     * @var bool
     */
    protected $isLoggedIn = false;

    /**
     * @var array
     */
    public $loginInfo = array();

    /**
     * @var null|UserInterface
     */
    protected $user;

    /**
     * @var bool    set to true if password is not jammed, or allow as is match. 
     */
    private $allow_raw_password_match = false;

    // +-------------------------------------------------------------+
    // +-------------------------------------------------------------+
    /**
     * @param null|UserInterface $user
     */
    public function __construct( $user=null )
    {
        $this->user = $user;
        $this->sessionStart();
        $this->sessionGet();
    }
    
    /**
     * @param string  $id
     * @param string  $pw
     * @return $this
     */
    public function loadInfo( $id, $pw )
    {
        $this->user_id  = $id;
        $this->password = $pw;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function getAuth()
    {
        if( $this->isLoggedIn = $this->verify() ) {
            $this->isLoggedIn = true;
            $this->user_info  = $this->user->getInfo( $this->user_id );
            $this->sessionSet();
        }
        return $this->isLoggedIn;
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->isLoggedIn;
    }
    
    public function accessTime()
    {
        return $this->loginInfo[ self::ACCESS_TIME ];
    }
    
    public function loginTime()
    {
        return $this->loginInfo[ self::LOGIN_TIME ];
    }

    // +-------------------------------------------------------------+
    // +-------------------------------------------------------------+
    /**
     * @return bool
     */
    protected function verify()
    {
        if( $this->verifyLogin() ) {
            $this->loginInfo[ self::IS_LOGIN ]    = true;
            $this->loginInfo[ self::USER_ID ]     = $this->user_id;
            $this->loginInfo[ self::LOGIN_TIME ]  = $this->now();
            $this->loginInfo[ self::ACCESS_TIME ] = $this->now();
            return true;
        }
        if( $this->verifySession() ) {
            $this->loginInfo[ self::ACCESS_TIME ] = $this->now();
            $this->user_id   = $this->loginInfo[ self::USER_ID ];
            return true;
        }
        return false;
    }
    
    /**
     * @return bool
     */
    protected function verifyLogin()
    {
        if( !$this->user_id ) {
            return false;
        }
        $pw = $this->user->getPassword( $this->user_id );
        if( $login = $this->verifyPassword( $pw ) ) {
        }
        return $login;
    }

    /**
     * @param string $pw
     * @return bool
     */
    protected function verifyPassword( $pw ) 
    {
        if( $pw === crypt( $this->password, $pw ) ) return true;
        if( $this->allow_raw_password_match && $pw === $this->password ) return true;
        return false;
    }
    
    /**
     * @return bool
     */
    protected function verifySession()
    {
        if( isset( $this->loginInfo ) && 
            isset( $this->loginInfo[ self::IS_LOGIN ] ) && 
            $this->loginInfo[ self::IS_LOGIN ] ) 
        {
            $this->user_id = $this->loginInfo[ self::USER_ID ];
            return true;
        }
        return false;
    }

    /**
     * @return \DateTime
     */
    protected function now() {
        return new \DateTime();
    }

    /**
     */
    protected function sessionStart()
    {
        if( !isset( $_SESSION ) ) {
            ob_start();
            session_start();
        }
    }

    /**
     */
    protected function sessionGet() {
        if( array_key_exists( $this->auth_id, $_SESSION ) ) {
            $this->loginInfo = $_SESSION[ $this->auth_id ];
        }
    }

    /**
     */
    protected function sessionSet() {
        $_SESSION[ $this->auth_id ] = $this->loginInfo;
    }
}