<?php
namespace WScore\Web\Session;

use WScore\Response\ModuleInterface;
use WScore\Response\ModuleTrait;

class Token
{
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

    protected $maxTokens = 20;

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
     * @return string
     */
    public function generateToken()
    {
        $token = sha1( 'session.dumb' . time() . mt_rand(1,100*100) . __DIR__ );
        $this->storeToken( $token );
        return $token;
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
     * @param string $token
     * @return bool
     */
    public function verifyToken( $token )
    {
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