<?php
namespace WScore\Web\Authenticate;

class StoreSession implements AuthInterface, AuthStorageInterface
{
    /**
     * id string to save to session.
     *
     * @var string
     */
    public $auth_id = 'Auth.ID';

    /**
     * @Inject
     * @var \WScore\Web\Session
     */
    public $session;

    /**
     * @return array|null
     */
    public function loadLogin()
    {
        $this->session->start();
        return $this->session->get( $this->auth_id );
    }

    /**
     * @param $loginInfo array
     */
    public function saveLogin( $loginInfo )
    {
        $loginInfo[ self::ACCESS_TIME ] = new \DateTime();
        $this->session->set( $this->auth_id, $loginInfo );
    }

    /**
     * 
     */
    public function logout()
    {
        $this->session->set( $this->auth_id, null );
    }
}
