<?php
namespace WScore\Web\Auth;

abstract class AuthAbstract implements AuthInterface
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
     * @var array
     */
    public $loginInfo = array();

    /**
     * @Inject
     * @var \WScore\Web\Session
     */
    public $session;

    /**
     * @Inject
     * @var \WScore\Web\Auth\LoadPost
     */
    public $post;

    /**
     * @param array $post
     * @return $this
     */
    public function with( $post )
    {
        $this->post->with( $post );
        return $this;
    }

    /**
     * @return bool
     */
    public function isLoggedIn() {
        return $this->isLoggedIn;
    }

    /**
     * @return $this
     */
    public function logout()
    {
        $this->session->start();
        $this->session->del( $this->auth_id );
        return $this;
    }

    /**
     * @param string $savedPass
     * @param string $postPass
     * @return bool
     */
    protected function matchPass( $savedPass, $postPass )
    {
        if( $savedPass === crypt( $postPass, $savedPass ) ) return true;
        if( $savedPass === $postPass ) return true;
        return false;
    }

    /**
     * @param array $loginInfo
     */
    protected function saveSession( $loginInfo ) 
    {
        $loginInfo[ 'login_time' ] = date( 'Y-m-d H:i:s' );
        $this->session->set( $this->auth_id, $loginInfo );
    }

    /**
     */
    protected function verifySession() 
    {
        $this->session->start();
        $this->loginInfo = $this->session->get( $this->auth_id );
        if( $this->loginInfo ) {
            $this->isLoggedIn = true;
        }
    }
}