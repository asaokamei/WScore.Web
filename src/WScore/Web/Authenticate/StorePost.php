<?php
namespace WScore\Web\Authenticate;

class StorePost implements AuthInterface, AuthStorageInterface
{
    public $post_id = 'loginUser';
    public $post_pw = 'loginPass';
    public $action  = 'action';
    public $value   = 'login';

    public $data;

    /**
     * @param array $data
     */
    public function __construct( $data = array() )
    {
        if( !$data ) {
            $this->data = $_POST;
        } else {
            $this->data = $data;
        }
    }

    /**
     * @return array|null
     * @throws Auth_NoPW_Exception
     * @throws Auth_NoID_Exception
     */
    public function loadLogin()
    {
        if( $this->get( $this->action ) !== $this->value ) {
            return null;
        }
        if( !$id  = $this->get( $this->post_id )) {
            throw new Auth_NoID_Exception( 'no_id', 401 );
        }
        if( !$pw  = $this->get( $this->post_pw )) {
            throw new Auth_NoPW_Exception( 'no_pw', 402 );
        }
        $loginInfo = array(
            self::IS_LOGIN    => self::LOGIN_STILL_RAW,
            self::LOGIN_METHOD=> self::BY_POST_FORM,
            self::USER_ID     => $id,
            self::PASSWORD    => $pw,
            self::ACCESS_TIME => new \DateTime(),
            self::LOGIN_TIME  => new \DateTime(),
        );
        return $loginInfo;
    }

    /** do nothing for post
     */
    public function saveLogin( $loginInfo ) {
    }

    /** do nothing for post
     */
    public function logout() {
    }

    /**
     * @param $name
     * @return null
     */
    private function get( $name ) {
        return isset( $this->data[ $name ] ) ? $this->data[ $name ] : null;
    }
}