<?php
namespace WScore\Response;

/**
 * Class Request
 *
 * @package WScore\Response
 */
class Request
{
    /**
     * request data, such as $_POST, $_GET. 
     * @var array
     */
    public $data = array();

    /**
     * information about request. 
     * @var array
     */
    public $info = array(
        'requestRoot'   => null,
        'requestUri'    => null,
        'requestMethod' => 'get',   // method, such as get, post, put, delete.
        'requestType'   => 'html',
    );
    
    // +----------------------------------------------------------------------+
    //  managing url and pathInfo.
    // +----------------------------------------------------------------------+
    /**
     */
    public function __construct()
    {
    }

    /**
     * @param null|string $path
     * @return Request
     */
    public function copy( $path=null )
    {
        $request = clone( $this );
        $request->modifyUri( $path );
        return $request;
    }

    /**
     * checks if the given $path matches with the requestUri.
     *
     * @param string $path
     * @return bool
     */
    public function match( $path )
    {
        if( strncmp( $this->getInfo( 'requestUri' ), $path, strlen( $path ) ) ) {
            // ignore the module with appUrl which does not match with pathInfo.
            return false;
        }
        return true;
    }

    /**
     * modifies request{Uri|Root} to be handled by next chain of resource.
     *
     * @param string $path
     */
    public function modifyUri( $path )
    {
        $this->setInfo( 'requestRoot', $this->getInfo( 'requestRoot') . $path );
        $this->setInfo( 'requestUri',  substr( $this->info[ 'requestUri' ], strlen( $path ) ) );
    }
    // +----------------------------------------------------------------------+
    //  setting request parameters.
    // +----------------------------------------------------------------------+
    /**
     * @param string $method
     * @return $this
     */
    public function on( $method ) {
        return $this->setInfo( 'requestMethod', strtolower( $method ) );
    }

    /**
     * @param array $data
     * @param bool $overwrite
     * @return $this
     */
    public function with( $data, $overwrite=false )
    {
        if( $overwrite ) {
            $this->data = $data;
        } else {
            $this->data = array_merge( $this->data, $data );
        }
        return $this;
    }

    /**
     * @param $what
     * @return $this
     */
    public function type( $what ) {
        return $this->setInfo( 'requestType', strtolower( $what ) );
    }

    /**
     * @param string $uri
     * @return $this
     */
    public function uri( $uri ) {
        return $this->setInfo( 'requestUri', $uri );
    }

    /**
     * @param $path
     * @return $this
     */
    public function path( $path ) {
        return $this->setInfo( 'requestRoot', $path );
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setInfo( $name, $value ) {
        $this->info[ $name ] = $value;
        return $this;
    }

    /**
     * @param null|string $name
     * @return array|null|string|mixed
     */
    public function getInfo( $name=null ) {
        if( isset( $name ) ) {
            return isset( $this->info[$name] ) ? $this->info[$name] : null;
        }
        return $this->info;
    }

    /**
     * @return array
     */
    public function requestInfo() {
        return $this->info;
    }
    // +----------------------------------------------------------------------+
}