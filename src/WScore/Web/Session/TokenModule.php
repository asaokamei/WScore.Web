<?php
namespace WScore\Web\Session;

use WScore\Response\ModuleInterface;
use WScore\Response\ModuleTrait;

class TokenModule implements ModuleInterface
{
    use ModuleTrait;

    /**
     * @Inject
     * @var \WScore\Web\Session\Token
     */
    public $token = null;

    /**
     * @var string
     */
    protected $tagName = '_token';

    public $errorCode = 'badToken';

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return null
     */
    public function respond()
    {
        // always set token into session.
        $token = $this->token->generateToken();
        $this->request->setInfo( $this->tagName, $token );

        // always verify token for methods, post, put, and delete.
        if( !in_array( $this->request->getInfo( 'requestMethod' ), [ 'post', 'put', 'delete' ] ) ) {
            return null;
        }
        $token = isset( $this->request->data[ $this->tagName ] ) ? $this->request->data[ $this->tagName ] : null;
        if( !$token ) {
            $this->request->error( $this->errorCode );
            return null;
        }
        if( !$this->token->verifyToken( $token ) ) {
            $this->request->error( $this->errorCode );
        }
        return null;
    }
}