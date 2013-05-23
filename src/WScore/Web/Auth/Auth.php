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
        $this->lastAccess = $this->now();
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
        return $this->post->verify( $this->user );
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