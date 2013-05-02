<?php
namespace WScore\Web\Auth;

class Auth
{
    /**
     * id string to save to session.
     *
     * @var string
     */
    public $auth_id = 'Auth.ID';

    /**
     * @var bool
     */
    protected $isLoggedIn = false;

    /**
     * @var
     */
    protected $access_time = 0;

    /**
     * @var
     */
    protected $login_time = 0;

    /**
     * @var array
     */
    public $loginInfo = array();

    /**
     * @Inject
     * @var \WScore\Web\Session
     */
    public $session;

    public function getAuth()
    {
        $this->verify();
        return $this;
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->isLoggedIn;
    }
    
    public function accessTime( $time )
    {
        return $this;
    }
    
    public function loginTime( $time )
    {
        
    }

    /**
     * @param array $loginInfo
     */
    protected function save( $loginInfo )
    {
        $loginInfo[ 'login_time' ]  = $this->now();
        $loginInfo[ 'access_time' ] = $loginInfo[ 'login_time' ];
        $this->session->set( $this->auth_id, $loginInfo );
    }

    /**
     */
    private function verify()
    {
        $this->session->start();
        $this->loginInfo = $this->session->get( $this->auth_id );
        if( $this->loginInfo ) 
        {
            $this->isLoggedIn = true;
            $this->loginInfo[ 'access_time' ] = $this->now();
            $this->session->set( $this->auth_id, $this->loginInfo );
        }
    }
    
    private function now() {
        return date( 'Y-m-d H:i:s' );
    }
}