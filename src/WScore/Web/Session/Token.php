<?php
namespace WScore\Web\Session;

use WScore\Response\ModuleInterface;
use WScore\Response\ModuleTrait;

class Token implements ModuleInterface
{
    use ModuleTrait;

    /**
     * @Inject
     * @var \WScore\Web\Session\Storage
     */
    public $storage;

    /**
     * @var string
     */
    protected $storageId = '..Session.Token.';

    /**
     * @var string
     */
    protected $tokenList  = 'tokens';

    /**
     * @var string
     */
    protected $tagName = '_token';

    protected $maxTokens = 20;

    protected $token = null;

    public $errorCode = 'badToken';

    /**
     *
     */
    public function __construct()
    {
        $this->storage->setup( $this->storageId );
        if( !$this->storage->has( $this->tokenList ) ) {
            $this->storage->set( $this->tokenList, array() );
        }
    }

    /**
     * @return null
     */
    public function respond()
    {
        // always set token into session.
        $this->pushToken();

        // always verify token for methods, post, put, and delete.
        if( !in_array( $this->request->getInfo( 'requestMethod' ), [ 'post', 'put', 'delete' ] ) ) {
            return null;
        }
        if( !isset( $this->request->data[ $this->tagName ] ) ) {
            $this->request->error( $this->errorCode );
        }
        elseif( !$this->verifyToken( $this->request->data[ $this->tagName ] ) ) {
            $this->request->error( $this->errorCode );
        }
        return null;
    }

    // +-------------------------------------------------------------+
    /**
     * @return string
     */
    public function pushToken()
    {
        $this->token = sha1( 'session.dumb' . time() . mt_rand(1,100*100) . __DIR__ );
        $this->storeToken( $this->token );
        $this->request->setInfo( $this->tagName, $this->token );
        return $this->token;
    }

    /**
     * @param $token
     */
    private function storeToken( $token )
    {
        $tokenList = $this->storage->get( $this->tokenList );
        $tokenList[] = $token;
        if( count( $tokenList ) > $this->maxTokens ) {
            $num_remove = count( $tokenList ) - $this->maxTokens;
            $tokenList  = array_slice( $tokenList, $num_remove );
        }
        $this->storage->set( $this->tokenList, $tokenList );
    }
    /**
     * @param null|string $token
     * @return bool
     */
    public function verifyToken( $token=null )
    {
        if( !$token ) {
            if( !isset( $_POST[ $this->tagName ] ) ) return false;
            $token = $_POST[ $this->tagName ];
        }
        $tokenList = $this->storage->get( $this->tokenList );
        if( empty( $tokenList ) ) return false;
        if( !in_array( $token, $tokenList ) ) {
            return false;
        }
        foreach( $tokenList as $k=>$v ) {
            if( $v === $token ) {
                unset( $tokenList[$k] );
            }
        }
        $tokenList = array_values( $tokenList );
        $this->storage->set( $this->tokenList, $tokenList );
        return true;
    }

}