<?php
namespace WScore\Web\Auth;

/*
$auth = new Auth();
$post = new AuthPost();
try {
    
    if( $id = $post->getId() ) {
        $pw = $post->getPw();
        $auth->postInfo( $id, $pw );
        $user_info = $user->getInfo( $id );
    }
    $auth->getAuth();
    $auth->userInfo( $user_info
    $auth->setUserInfo( 

}
catch( \Exception $e ) {
    
}
*/

class AuthDba implements  UserInterface
{
    public function getLoginPw( $id ) {
    }

    public function getUserInfo( $id ) {
    }

    public function setLoginId( $id ) {
    }
}

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
     * @var array
     */
    public $user_info = array();
    
    /**
     * @var array
     */
    public $loginInfo = array();

    /**
     * @var null
     */
    public $lastAccess = null;

    /**
     * @Inject
     * @var UserInterface
     */
    public $user;
    
    /**
     * @Inject
     * @var AuthPost
     */
    public $post;

    /**
     * @Inject
     * @var \WScore\Web\Session
     */
    public $session;

    /**
     * @var AuthCookie
     */
    public $cookie;
    
    // +-------------------------------------------------------------+
    //  public methods
    // +-------------------------------------------------------------+
    /**
     */
    public function __construct()
    {
        $this->session->start();
    }

    /**
     * @throws \RuntimeException
     * @return bool|string
     */
    public function getAuth()
    {
        if( $id = $this->verify() ) {
            $this->user_info  = $this->user->getUserInfo( $id );
            $this->save();
            if( isset( $this->cookie ) ) $this->cookie->save( $this->user_id );
        }
        return $id;
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return (bool) $this->user_id;
    }
    
    public function accessTime()
    {
        return $this->lastAccess;
    }
    
    public function loginTime()
    {
        return $this->loginInfo[ self::LOGIN_TIME ];
    }

    /**
     * @param string $id
     * @return bool
     */
    public function login( $id )
    {
        if( !$id ) return false;
        $this->user_id = $id;
        $this->loginInfo[ self::IS_LOGIN ]    = true;
        $this->loginInfo[ self::USER_ID ]     = $id;
        $this->loginInfo[ self::LOGIN_TIME ]  = $this->now();
        $this->loginInfo[ self::ACCESS_TIME ] = $this->now();
        return true;
    }

    /**
     * 
     */
    public function logout()
    {
        $this->user_id = null;
        $this->loginInfo = array();
        $this->session->set( $this->auth_id, null );
        if( isset( $this->cookie ) ) $this->cookie->logout();
    }
    
    /**
     *
     */
    public function save() {
        $this->loginInfo[ self::ACCESS_TIME ] = $this->now();
        $this->session->set( $this->auth_id, $this->loginInfo );
    }
    // +-------------------------------------------------------------+
    // +-------------------------------------------------------------+
    /**
     * @return bool
     */
    protected function verify()
    {
        if( $id = $this->verifyLogin() ) {
            $this->login( $id );
        }
        if( $id = $this->verifySession() ) {
            $this->user_id = $id;
        }
        if( $id = $this->verifyCookie() ) {
            $this->login( $id );
        }
        return $this->user_id;
    }

    /**
     * @throws \RuntimeException
     * @return bool|string
     */
    protected function verifyLogin()
    {
        if( !$this->post->isPost() ) return false;
        
        if( !$id = $this->post->getLoginId() ) {
            throw new \RuntimeException( 'no_id', 401 );
        }
        $this->user->setLoginId( $id );
        if( !$pw_jam = $this->user->getLoginPw( $id ) ) {
            throw new \RuntimeException( 'no_user', 402 );
        };
        if( !$pw_raw = $this->post->getLoginPw() ) {
            throw new \RuntimeException( 'no_pw', 403 );
        }
        if( $this->matchPassword( $pw_jam, $pw_raw ) ) {
            throw new \RuntimeException( 'bad_pw', 404 );
        }
        return $id;
    }

    /**
     * @param string $pw_jam
     * @param string $pw_raw
     * @return bool
     */
    protected function matchPassword( $pw_jam, $pw_raw ) 
    {
        if( $pw_jam === crypt( $pw_raw, $pw_jam ) ) return true;
        return false;
    }
    
    /**
     * @return bool|string
     */
    protected function verifySession()
    {
        $loginInfo = $this->session->get( $this->auth_id );
        if( $loginInfo && 
            isset( $loginInfo[ self::IS_LOGIN ] ) &&
            $loginInfo[ self::IS_LOGIN ] ) 
        {
            $this->loginInfo  = $loginInfo;
            $this->lastAccess = $loginInfo[ self::ACCESS_TIME ];
            return $loginInfo[ self::USER_ID ];
        }
        return false;
    }

    /**
     * @return bool|string
     */
    protected function verifyCookie()
    {
        if( !isset( $this->cookie ) ) return false; 
        $id = $this->cookie->getLoginId();
        return $id;
    }
    
    /**
     * @return \DateTime
     */
    protected function now() {
        return new \DateTime();
    }
}