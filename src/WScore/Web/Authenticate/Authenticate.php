<?php
namespace WScore\Web\Authenticate;

class Auth_Exception extends \Exception {}
class Auth_UserNotFound_Exception extends Auth_Exception {}
class Auth_PasswordNotMatch_Exception extends Auth_Exception {}
class Auth_NoID_Exception extends Auth_Exception {}
class Auth_InvalidLogin_Exception extends Auth_Exception {}
class Auth_NoPW_Exception extends Auth_Exception {}


class Authenticate implements AuthInterface
{
    /**
     * @var array
     */
    public $loginInfo = array();

    /**
     * @var array
     */
    public $user_info = array();

    /**
     * @var null
     */
    public $lastAccess = null;

    /**
     * @Inject
     * @var AuthUserInterface
     */
    public $user;

    /**
     * @Inject
     * @var \WScore\Web\Authenticate\StorePost
     */
    public $post;

    /**
     * @Inject
     * @var \WScore\Web\Authenticate\StoreSession
     */
    public $session;

    /**
     * @var AuthStorageInterface[]
     */
    protected $loginStores = array();

    /**
     * @var AuthStorageInterface[]
     */
    protected $saveStorage = array();

    // +-------------------------------------------------------------+
    //  construction and managing storage objects
    // +-------------------------------------------------------------+
    /**
     */
    public function __construct()
    {
        $this->setLoginMethod( $this->post );
        $this->setLoginMethod( $this->session );
        $this->setStorage( $this->session );
    }

    /**
     * @param AuthStorageInterface $method
     */
    public function setLoginMethod( $method ) {
        if( $method instanceof AuthStorageInterface ) {
            $this->loginStores[] = $method;
        }
    }

    public function setStorage( $store ) {
        if( $store instanceof AuthStorageInterface ) {
            $this->saveStorage[] = $store;
        }
    }

    // +-------------------------------------------------------------+
    //  authentication and log{in|out}
    // +-------------------------------------------------------------+
    /**
     * @return bool
     * @throws Auth_UserNotFound_Exception
     */
    public function getAuth()
    {
        $loginInfo = $this->load();
        if( !$loginInfo ) return false;
        
        $this->loginInfo = $loginInfo;
        $this->validate();
        $this->save();
        return true;
    }

    /**
     * @param $id
     * @throws Auth_UserNotFound_Exception
     */
    public function login( $id )
    {
        $this->loginInfo = array(
            self::IS_LOGIN    => self::LOGIN_VALID,
            self::LOGIN_METHOD=> self::BY_LOGIN,
            self::USER_ID     => $id,
            self::PASSWORD    => null,
            self::ACCESS_TIME => new \DateTime(),
            self::LOGIN_TIME  => new \DateTime(),
        );
        $this->validate();
        $this->save();
    }
    
    /**
     * logout for eah save-storage. 
     */
    public function logout()
    {
        $this->loginInfo = array();
        $this->user_info = array();
        if( !empty( $this->saveStorage ) ) {
            foreach( $this->saveStorage as $storage ) {
                $storage->logout();
            }
        }
    }

    // +-------------------------------------------------------------+
    //  get login information.
    // +-------------------------------------------------------------+
    /**
     * @return bool
     */
    public function isLoggedIn() {
        return !empty( $this->loginInfo );
    }

    /**
     * @return null|string
     */
    public function getUserId() {
        return $this->get( self::USER_ID );
    }

    /**
     * @return \DateTime
     */
    public function getLastAccess() {
        return $this->lastAccess;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get( $name ) {
        if( array_key_exists( $name, $this->loginInfo ) ) return $this->loginInfo[ $name ];
        return null;
    }
    // +-------------------------------------------------------------+
    //  internal methods
    // +-------------------------------------------------------------+
    /**
     * validates if login info is valid. 
     * 
     * @throws Auth_PasswordNotMatch_Exception
     * @throws Auth_UserNotFound_Exception
     */
    protected function validate()
    {
        // check basic login info
        if( !isset( $this->loginInfo[ self::USER_ID ] ) ) {
            throw new Auth_NoID_Exception();
        }
        if( !in_array( $this->loginInfo[ self::IS_LOGIN ], array(
            self::LOGIN_VALID, self::LOGIN_STILL_RAW, self::LOGIN_TOKEN
        ) ) ) {
            throw new Auth_InvalidLogin_Exception();
        }
        // checks for user data. 
        $this->user_info = $this->user->getUserInfo( $this->loginInfo[ self::USER_ID ] );
        if( !$this->user_info ) {
            throw new Auth_UserNotFound_Exception( 'no_user', 402 );
        }
        // check for password. 
        if( $this->loginInfo[ self::IS_LOGIN ] === self::LOGIN_STILL_RAW ) 
        {
            $pw_jam = $this->user->getLoginPw( $this->loginInfo[ self::USER_ID ] );
            $pw_raw = $this->loginInfo[ self::PASSWORD ];
            if( !$this->matchPassword( $pw_jam, $pw_raw ) ) {
                throw new Auth_PasswordNotMatch_Exception();
            }
            $this->loginInfo[ self::PASSWORD ] = $pw_jam;
            $this->loginInfo[ self::IS_LOGIN ] = self::LOGIN_VALID;
        }
    }
    
    /**
     * @throws \RuntimeException
     * @return null|array
     */
    protected function load()
    {
        if( empty( $this->loginStores ) ) {
            throw new \RuntimeException( 'not login storage' );
        }
        $loginInfo = null;
        foreach( $this->loginStores as $storage ) {
            if( $loginInfo = $storage->loadLogin() ) { break; }
        }
        $this->lastAccess = $loginInfo[ self::ACCESS_TIME ];
        return $loginInfo;
    }

    /**
     * 
     */
    protected function save()
    {
        if( empty( $this->saveStorage ) ) return;
        foreach( $this->saveStorage as $storage ) {
            $storage->saveLogin( $this->loginInfo );
        }
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
}
